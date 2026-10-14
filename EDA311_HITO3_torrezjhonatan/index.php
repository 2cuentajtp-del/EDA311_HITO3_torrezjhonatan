<?php
require_once __DIR__ . '/src/ayudas.php';
cabecera('Inicio');
?>
<h1>Gestión de la tienda</h1>
<p>Selecciona la tabla que deseas administrar.</p>
<div class="tarjetas">
    <a class="tarjeta" href="entidad.php?e=clientes">Clientes</a>
    <a class="tarjeta" href="entidad.php?e=pedidos">Pedidos</a>
    <a class="tarjeta" href="entidad.php?e=productos">Productos</a>
</div>
<?php pie(); ?>
