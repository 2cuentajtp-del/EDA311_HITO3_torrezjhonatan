<?php
require_once __DIR__ . '/src/ayudas.php';
require_once __DIR__ . '/src/Conexion.php';
require_once __DIR__ . '/src/RepositorioTabla.php';
require_once __DIR__ . '/src/Validador.php';

[$clave, $entidad] = cargarEntidad();
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
$esEdicion = $id > 0;
$errores = [];

try {
    $pdo = Conexion::obtener();
    $repositorio = new RepositorioTabla($pdo, $entidad);

    $registro = $esEdicion ? $repositorio->obtenerPorId($id) : [];
    if ($esEdicion && $registro === null) {
        mensajeFlash('error', 'El registro no existe.');
        header('Location: entidad.php?e=' . urlencode($clave));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        [$valores, $errores] = Validador::validar($entidad['campos'], $_POST, $pdo);
        $registro = array_merge($registro ?? [], $_POST);

        if (!$errores) {
            try {
                if ($esEdicion) {
                    $repositorio->actualizar($id, $valores);
                    mensajeFlash('exito', 'Registro modificado correctamente.');
                } else {
                    $repositorio->insertar($valores);
                    mensajeFlash('exito', 'Registro creado correctamente.');
                }
                header('Location: entidad.php?e=' . urlencode($clave));
                exit;
            } catch (PDOException $ex) {
                $errores[] = ($ex->getCode() === '23000')
                    ? 'Ya existe un registro con un valor único repetido (por ejemplo, email o nombre).'
                    : 'No se pudo guardar: ' . $ex->getMessage();
            }
        }
    }
} catch (Throwable $ex) {
    cabecera('Error');
    echo '<div class="aviso error">Error: ' . e($ex->getMessage()) . '</div>';
    pie();
    exit;
}

cabecera(($esEdicion ? 'Modificar ' : 'Registrar ') . $entidad['titulo']);
?>
<h1><?= $esEdicion ? 'Modificar' : 'Registrar' ?> - <?= e($entidad['titulo']) ?></h1>
<?php foreach ($errores as $mensaje): ?><div class="aviso error"><?= e($mensaje) ?></div><?php endforeach; ?>

<form class="formulario" method="post">
    <input type="hidden" name="e" value="<?= e($clave) ?>">
    <?php if ($esEdicion): ?>
        <input type="hidden" name="id" value="<?= $id ?>">
        <p class="nota">Editando <?= e($entidad['pk']) ?> = <strong><?= $id ?></strong></p>
    <?php endif; ?>

    <?php foreach ($entidad['campos'] as $nombre => $def): $valorActual = $registro[$nombre] ?? ''; ?>
        <label for="<?= e($nombre) ?>"><?= e($def['etiqueta']) ?></label>
        <?php if ($def['tipo'] === 'fk'): ?>
            <select id="<?= e($nombre) ?>" name="<?= e($nombre) ?>" required>
                <option value="">-- Selecciona --</option>
                <?php foreach ($repositorio->opcionesForaneas($def) as $opcion): ?>
                    <option value="<?= (int)$opcion['id'] ?>" <?= (string)$opcion['id'] === (string)$valorActual ? 'selected' : '' ?>>
                        <?= e($opcion['id'] . ' - ' . $opcion['texto']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else:
            $tipoHtml = match ($def['tipo']) {
                'email' => 'email', 'entero' => 'number', 'decimal' => 'number', 'fecha' => 'date', default => 'text'
            };
            $extra = $def['tipo'] === 'decimal' ? 'step="0.01" min="0"' : ($def['tipo'] === 'entero' ? 'step="1"' . (isset($def['min']) ? ' min="' . $def['min'] . '"' : '') . (isset($def['max']) ? ' max="' . $def['max'] . '"' : '') : '');
        ?>
            <input id="<?= e($nombre) ?>" type="<?= $tipoHtml ?>" name="<?= e($nombre) ?>" value="<?= e($valorActual) ?>" <?= $extra ?> required>
        <?php endif; ?>
    <?php endforeach; ?>

    <p style="margin-top:18px">
        <button class="btn verde" type="submit"><?= $esEdicion ? 'Guardar cambios' : 'Registrar' ?></button>
        <a class="btn gris" href="entidad.php?e=<?= e($clave) ?>">Cancelar</a>
    </p>
</form>
<?php pie(); ?>
