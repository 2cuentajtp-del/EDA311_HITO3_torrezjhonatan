# EDA311_HITO3_ApellidoNombre

Aplicación web en **PHP + MySQL** para gestionar las tablas `clientes`, `pedidos` y `productos`
de la base `tienda_torrez`. Incluye CRUD completo con formularios HTML, una **lista doblemente
enlazada** implementada desde cero y **Bubble Sort** aplicado directamente sobre la lista.

## 1. Requisitos de software

- PHP 8.1 o superior con la extensión `pdo_mysql` habilitada
- MySQL 8.x (o MariaDB 10.4+)
- Un servidor web (Apache de XAMPP/WAMP/Laragon) o el servidor integrado de PHP
- Git y un navegador web

## 2. Descargar el proyecto

```bash
git clone https://github.com/TU_USUARIO/EDA311_HITO3_ApellidoNombre.git
cd EDA311_HITO3_ApellidoNombre
```

## 3. Configurar el servidor y el entorno PHP

**Opción A: XAMPP**
1. Instala XAMPP e inicia **Apache** y **MySQL** desde su panel.
2. Copia la carpeta del proyecto dentro de `C:\xampp\htdocs\`.
3. Verifica que `pdo_mysql` esté activo (`extension=pdo_mysql` en `php.ini`; viene activo por defecto).

**Opción B: servidor integrado de PHP**
```bash
php -S localhost:8000
```
(ejecutar dentro de la carpeta del proyecto).

## 4. Configurar la conexión a MySQL

1. Copia la plantilla de configuración:
   ```bash
   cp config.example.php config.php        # en Windows: copy config.example.php config.php
   ```
2. Edita `config.php` y ajusta:

| Clave | Descripción | Ejemplo |
|---|---|---|
| `host` | Servidor MySQL | `127.0.0.1` |
| `puerto` | Puerto | `3306` |
| `base` | Base facilitada por el docente | `tienda_torrez` |
| `usuario` | Usuario MySQL | `root` |
| `password` | Contraseña de tu MySQL local | *(la tuya)* |

`config.php` está en `.gitignore`: **nunca se sube al repositorio**.

3. La base de datos la entrega el docente. Impórtala en MySQL (Workbench: *Server > Data Import*,
   o `mysql -u usuario -p < archivo.sql`).
   Si solo quieres probar localmente, existe `sql/estructura_referencia.sql` con la estructura
   deducida de la exploración (no contiene datos).

## 5. Iniciar la aplicación

- **XAMPP:** abrir `http://localhost/EDA311_HITO3_ApellidoNombre/`
- **Servidor integrado:** abrir `http://localhost:8000/`

Desde el menú se accede a Clientes, Pedidos y Productos.

## 6. Estructura de la base explorada

| Tabla | Campos | Clave primaria | Claves foráneas / índices |
|---|---|---|---|
| `clientes` | id_cliente (int, AI), nombre, email, edad | `id_cliente` | `email` único (`uq_cliente_email`) |
| `pedidos` | id_pedido, id_cliente, fecha, total | `id_pedido` | `id_cliente` → `clientes.id_cliente` (`fk_pedido_cliente`) |
| `productos` | id_producto (int, AI), nombre varchar(100), categoria varchar(50), precio decimal(10,2), stock int | `id_producto` | `nombre` único; índices por categoría y (categoría, precio) |

Relación: un cliente puede tener muchos pedidos (1:N). Por la clave foránea, **no se puede
eliminar un cliente con pedidos**; la aplicación muestra un mensaje en lugar de fallar.

> Los tipos de `nombre`/`email`/`edad` (clientes) y de `fecha`/`total` (pedidos) se dedujeron
> de sus nombres; verifícalos con `DESCRIBE` y ajusta `src/entidades.php` si difieren.

## 7. Estructura del proyecto

```
├── index.php                 Menú principal
├── entidad.php               Consulta: lista enlazada + Bubble Sort + acciones
├── formulario.php            Registrar / modificar (con validación)
├── eliminar.php              Eliminar registros
├── config.example.php        Plantilla de conexión (copiar a config.php)
├── assets/estilos.css        Estilos
├── sql/estructura_referencia.sql
└── src/
    ├── NodoLista.php                 Nodo (datos, anterior, siguiente)
    ├── ListaDoblementeEnlazada.php   Lista + inserción + recorrido + Bubble Sort
    ├── Conexion.php                  Conexión PDO
    ├── RepositorioTabla.php          CRUD con consultas preparadas y carga a la lista
    ├── Validador.php                 Validación de formularios
    ├── entidades.php                 Definición de tablas y campos
    └── ayudas.php                    Funciones comunes (HTML, mensajes)
```

## 8. Lista doblemente enlazada

Cada `NodoLista` guarda un registro (arreglo asociativo) y dos referencias: `anterior` y `siguiente`.
`ListaDoblementeEnlazada` administra `cabeza`, `cola` y `tamano`, e ofrece `insertarAlFinal`,
`insertarAlInicio`, `recorrer` (cabeza → cola) y `recorrerInverso` (cola → cabeza).
`RepositorioTabla::cargarEnLista()` ejecuta el `SELECT` y va insertando cada fila como un nodo.
No se usa `SplDoublyLinkedList` ni ninguna biblioteca.

## 9. Bubble Sort sobre la lista

`ordenarBurbuja($campo, $ascendente)` recorre los nodos comparando cada uno con su siguiente;
si están en orden incorrecto, intercambia los datos de ambos nodos. Tras cada pasada, el último
nodo de la zona no ordenada queda en su sitio (marcador `$limite`), y el proceso termina cuando
una pasada no produce intercambios. **Nunca convierte la lista en un arreglo** ni usa `sort()`/`usort()`.

Criterio de comparación: números se comparan numéricamente y texto con `strcasecmp`
(sin distinguir mayúsculas). Criterios por defecto, modificables desde la interfaz:

- **Clientes:** por nombre (A-Z), como una agenda.
- **Pedidos:** por fecha, para ver la secuencia cronológica.
- **Productos:** por precio, para comparar valores.

La interfaz muestra la lista original, la lista ordenada y estadísticas (pasadas, comparaciones, intercambios).
