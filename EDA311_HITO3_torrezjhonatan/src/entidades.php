<?php
/**
 * Descripción de las tablas según la exploración de la base (MySQL Workbench).
 * tipo: texto | email | entero | decimal | fecha | fk
 */
return [
    'clientes' => [
        'titulo'   => 'Clientes',
        'tabla'    => 'clientes',
        'pk'       => 'id_cliente',
        'sql'      => 'SELECT id_cliente, nombre, email, edad FROM clientes',
        'campos'   => [
            'nombre' => ['etiqueta' => 'Nombre', 'tipo' => 'texto',  'max' => 100],
            'email'  => ['etiqueta' => 'Email',  'tipo' => 'email',  'max' => 100],
            'edad'   => ['etiqueta' => 'Edad',   'tipo' => 'entero', 'min' => 0, 'max' => 120],
        ],
        'columnas' => ['id_cliente' => 'ID', 'nombre' => 'Nombre', 'email' => 'Email', 'edad' => 'Edad'],
        'orden_defecto' => 'nombre',
        'criterio_texto' => 'Por defecto por nombre (A-Z): facilita ubicar clientes como en una agenda.',
    ],
    'pedidos' => [
        'titulo'   => 'Pedidos',
        'tabla'    => 'pedidos',
        'pk'       => 'id_pedido',
        'sql'      => 'SELECT p.id_pedido, p.id_cliente, c.nombre AS cliente, p.fecha, p.total
                       FROM pedidos p INNER JOIN clientes c ON c.id_cliente = p.id_cliente',
        'campos'   => [
            'id_cliente' => ['etiqueta' => 'Cliente', 'tipo' => 'fk', 'ref_tabla' => 'clientes',
                             'ref_pk' => 'id_cliente', 'ref_texto' => 'nombre'],
            'fecha'      => ['etiqueta' => 'Fecha',   'tipo' => 'fecha'],
            'total'      => ['etiqueta' => 'Total',   'tipo' => 'decimal', 'min' => 0],
        ],
        'columnas' => ['id_pedido' => 'ID', 'cliente' => 'Cliente', 'fecha' => 'Fecha', 'total' => 'Total'],
        'orden_defecto' => 'fecha',
        'criterio_texto' => 'Por defecto por fecha (más antiguo primero): permite ver la secuencia cronológica de ventas.',
    ],
    'productos' => [
        'titulo'   => 'Productos',
        'tabla'    => 'productos',
        'pk'       => 'id_producto',
        'sql'      => 'SELECT id_producto, nombre, categoria, precio, stock FROM productos',
        'campos'   => [
            'nombre'    => ['etiqueta' => 'Nombre',    'tipo' => 'texto',   'max' => 100],
            'categoria' => ['etiqueta' => 'Categoría', 'tipo' => 'texto',   'max' => 50],
            'precio'    => ['etiqueta' => 'Precio',    'tipo' => 'decimal', 'min' => 0],
            'stock'     => ['etiqueta' => 'Stock',     'tipo' => 'entero',  'min' => 0],
        ],
        'columnas' => ['id_producto' => 'ID', 'nombre' => 'Nombre', 'categoria' => 'Categoría', 'precio' => 'Precio', 'stock' => 'Stock'],
        'orden_defecto' => 'precio',
        'criterio_texto' => 'Por defecto por precio (menor a mayor): útil para comparar y detectar productos baratos o caros.',
    ],
];
