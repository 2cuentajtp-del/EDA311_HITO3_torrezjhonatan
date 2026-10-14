<?php
require_once __DIR__ . '/src/ayudas.php';
require_once __DIR__ . '/src/Conexion.php';
require_once __DIR__ . '/src/RepositorioTabla.php';

[$clave, $entidad] = cargarEntidad();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: entidad.php?e=' . urlencode($clave));
    exit;
}

$id = (int)($_POST['id'] ?? 0);

try {
    $repositorio = new RepositorioTabla(Conexion::obtener(), $entidad);
    $repositorio->eliminar($id);
    mensajeFlash('exito', 'Registro eliminado correctamente.');
} catch (PDOException $ex) {
    if ($ex->getCode() === '23000') {
        mensajeFlash('error', 'No se puede eliminar: el registro está relacionado con otros datos (por ejemplo, un cliente con pedidos).');
    } else {
        mensajeFlash('error', 'No se pudo eliminar: ' . $ex->getMessage());
    }
} catch (Throwable $ex) {
    mensajeFlash('error', 'Error: ' . $ex->getMessage());
}

header('Location: entidad.php?e=' . urlencode($clave));
exit;
