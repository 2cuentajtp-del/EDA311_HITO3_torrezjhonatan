<?php
/** Crea la conexión PDO a MySQL usando config.php. */
class Conexion
{
    public static function obtener(): PDO
    {
        $ruta = __DIR__ . '/../config.php';
        if (!file_exists($ruta)) {
            throw new RuntimeException('Falta config.php. Copia config.example.php como config.php y completa tus datos.');
        }
        $c = require $ruta;
        $dsn = "mysql:host={$c['host']};port={$c['puerto']};dbname={$c['base']};charset={$c['charset']}";
        return new PDO($dsn, $c['usuario'], $c['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
}
