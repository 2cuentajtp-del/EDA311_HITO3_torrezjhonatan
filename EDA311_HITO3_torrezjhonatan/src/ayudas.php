<?php
session_start();

function e($texto): string
{
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

function mensajeFlash(?string $tipo = null, ?string $texto = null): ?array
{
    if ($tipo !== null) {
        $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
        return null;
    }
    $mensaje = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $mensaje;
}

function cargarEntidad(): array
{
    $entidades = require __DIR__ . '/entidades.php';
    $clave = $_GET['e'] ?? $_POST['e'] ?? '';
    if (!isset($entidades[$clave])) {
        http_response_code(404);
        exit('Entidad no válida.');
    }
    return [$clave, $entidades[$clave]];
}

function cabecera(string $titulo): void
{
    $mensaje = mensajeFlash();
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . e($titulo) . ' - Tienda Torrez</title>';
    echo '<link rel="stylesheet" href="assets/estilos.css"></head><body>';
    echo '<header><a href="index.php" class="marca">Tienda Torrez</a><nav>';
    echo '<a href="entidad.php?e=clientes">Clientes</a><a href="entidad.php?e=pedidos">Pedidos</a><a href="entidad.php?e=productos">Productos</a>';
    echo '</nav></header><main>';
    if ($mensaje) {
        echo '<div class="aviso ' . e($mensaje['tipo']) . '">' . e($mensaje['texto']) . '</div>';
    }
}

function pie(): void
{
    echo '</main></body></html>';
}
