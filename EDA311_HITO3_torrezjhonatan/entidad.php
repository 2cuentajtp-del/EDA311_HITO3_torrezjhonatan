<?php
require_once __DIR__ . '/src/ayudas.php';
require_once __DIR__ . '/src/Conexion.php';
require_once __DIR__ . '/src/RepositorioTabla.php';

[$clave, $entidad] = cargarEntidad();

try {
    $repositorio = new RepositorioTabla(Conexion::obtener(), $entidad);

    // 1) Lista cargada desde MySQL (orden original: por clave primaria)
    $listaOriginal = $repositorio->cargarEnLista();

    // 2) Segunda lista con los mismos registros, ordenada con Bubble Sort
    $campoOrden = $_GET['campo'] ?? $entidad['orden_defecto'];
    if (!array_key_exists($campoOrden, $entidad['columnas'])) {
        $campoOrden = $entidad['orden_defecto'];
    }
    $ascendente = ($_GET['dir'] ?? 'asc') !== 'desc';

    $listaOrdenada = $repositorio->cargarEnLista();
    $listaOrdenada->ordenarBurbuja($campoOrden, $ascendente);
} catch (Throwable $error) {
    cabecera($entidad['titulo']);
    echo '<div class="aviso error">Error de base de datos: ' . e($error->getMessage()) . '</div>';
    pie();
    exit;
}

cabecera($entidad['titulo']);
?>
<h1><?= e($entidad['titulo']) ?></h1>
<p><a class="btn verde" href="formulario.php?e=<?= e($clave) ?>">+ Registrar nuevo</a></p>

<h2>1. Registros en la lista doblemente enlazada (orden de MySQL)</h2>
<p class="nota">Total de nodos: <?= $listaOriginal->obtenerTamano() ?></p>
<table>
    <tr>
        <?php foreach ($entidad['columnas'] as $etiqueta): ?><th><?= e($etiqueta) ?></th><?php endforeach; ?>
        <th>Acciones</th>
    </tr>
    <?php if ($listaOriginal->estaVacia()): ?>
        <tr><td colspan="<?= count($entidad['columnas']) + 1 ?>">No hay registros.</td></tr>
    <?php endif; ?>
    <?php foreach ($listaOriginal->recorrer() as $fila): ?>
        <tr>
            <?php foreach ($entidad['columnas'] as $campo => $etiqueta): ?><td><?= e($fila[$campo]) ?></td><?php endforeach; ?>
            <td>
                <a class="btn" href="formulario.php?e=<?= e($clave) ?>&id=<?= (int)$fila[$entidad['pk']] ?>">Modificar</a>
                <form class="inline" method="post" action="eliminar.php"
                      onsubmit="return confirm('¿Eliminar este registro?');">
                    <input type="hidden" name="e" value="<?= e($clave) ?>">
                    <input type="hidden" name="id" value="<?= (int)$fila[$entidad['pk']] ?>">
                    <button class="btn rojo" type="submit">Eliminar</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>2. Resultado ordenado con Bubble Sort</h2>
<form class="barra" method="get">
    <input type="hidden" name="e" value="<?= e($clave) ?>">
    <div>
        <label>Ordenar por</label>
        <select name="campo">
            <?php foreach ($entidad['columnas'] as $campo => $etiqueta): ?>
                <option value="<?= e($campo) ?>" <?= $campo === $campoOrden ? 'selected' : '' ?>><?= e($etiqueta) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label>Sentido</label>
        <select name="dir">
            <option value="asc" <?= $ascendente ? 'selected' : '' ?>>Ascendente</option>
            <option value="desc" <?= !$ascendente ? 'selected' : '' ?>>Descendente</option>
        </select>
    </div>
    <button class="btn" type="submit">Ordenar</button>
</form>
<p class="nota">Criterio sugerido: <?= e($entidad['criterio_texto']) ?></p>
<div class="stats">
    Pasadas: <strong><?= $listaOrdenada->pasadas ?></strong> |
    Comparaciones: <strong><?= $listaOrdenada->comparaciones ?></strong> |
    Intercambios: <strong><?= $listaOrdenada->intercambios ?></strong>
</div>
<table>
    <tr>
        <th>#</th>
        <?php foreach ($entidad['columnas'] as $etiqueta): ?><th><?= e($etiqueta) ?></th><?php endforeach; ?>
    </tr>
    <?php $posicion = 0; foreach ($listaOrdenada->recorrer() as $fila): $posicion++; ?>
        <tr>
            <td><?= $posicion ?></td>
            <?php foreach ($entidad['columnas'] as $campo => $etiqueta): ?><td><?= e($fila[$campo]) ?></td><?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
</table>
<?php pie(); ?>
