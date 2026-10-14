# EDA311_HITO3_ApellidoNombre

Aplicación web en **PHP + MySQL** para gestionar las tablas `clientes`, `pedidos` y `productos`
de la base de datos `tienda_torrez`. Incluye CRUD completo con formularios HTML, una
**lista doblemente enlazada** implementada desde cero y el algoritmo **Bubble Sort**
aplicado directamente sobre la lista.

## 1. Requisitos de software

- PHP 8.1 o superior con la extensión `pdo_mysql` habilitada
- MySQL 8.x (o MariaDB 10.4+)
- Servidor web (Apache de XAMPP/WAMP/Laragon) o el servidor integrado de PHP
- Git
- Navegador web actualizado

## 2. Descargar el proyecto

```bash
git clone https://github.com/TU_USUARIO/EDA311_HITO3_ApellidoNombre.git
cd EDA311_HITO3_ApellidoNombre
```

## 3. Configurar el servidor web y el entorno PHP

**Opción A: XAMPP**

1. Instala XAMPP e inicia **Apache** y **MySQL** desde su panel de control.
2. Copia la carpeta del proyecto dentro de `C:\xampp\htdocs\`.
3. Verifica que `extension=pdo_mysql` esté activa en `php.ini` (viene activa por defecto).

**Opción B: servidor integrado de PHP**

Dentro de la carpeta del proyecto ejecuta:

```bash
php -S localhost:8000
```

## 4. Configurar la conexión a MySQL

1. Copia la plantilla de configuración:

```bash
   cp config.example.php config.php          # Linux / macOS
   copy config.example.php config.php        # Windows
```

2. Edita `config.php` y ajusta los valores:

| Clave      | Descripción                         | Ejemplo         |
|------------|-------------------------------------|-----------------|
| `host`     | Servidor MySQL                      | `127.0.0.1`     |
| `puerto`   | Puerto de MySQL                     | `3306`          |
| `base`     | Base facilitada por el docente      | `tienda_torrez` |
| `usuario`  | Usuario de MySQL                    | `root`          |
| `password` | Contraseña de tu MySQL              | *(la tuya)*     |

> `config.php` está incluido en `.gitignore`, por lo que **las credenciales reales nunca se suben al repositorio**.
> Solo se publica `config.example.php`, que contiene valores de ejemplo.

3. La base de datos la facilita el docente. Impórtala en MySQL desde Workbench
   (*Server > Data Import*) o por consola:

```bash
   mysql -u TU_USUARIO -p < archivo_del_docente.sql
```

   Para pruebas locales sin la base del docente existe `sql/estructura_referencia.sql`,
   que crea solo la estructura (sin datos).

## 5. Iniciar la aplicación

1. Asegúrate de que MySQL y el servidor web estén en ejecución.
2. Abre el navegador en:
   - **XAMPP:** `http://localhost/EDA311_HITO3_ApellidoNombre/`
   - **Servidor integrado:** `http://localhost:8000/`
3. Desde el menú principal elige **Clientes**, **Pedidos** o **Productos**.

## 6. Estructura de la base de datos explorada

| Tabla       | Campos                                                                 | Clave primaria | Claves foráneas e índices                                      |
|-------------|------------------------------------------------------------------------|----------------|----------------------------------------------------------------|
| `clientes`  | id_cliente (int, auto_increment), nombre, email, edad                  | `id_cliente`   | `email` único (`uq_cliente_email`)                             |
| `pedidos`   | id_pedido, id_cliente, fecha, total                                    | `id_pedido`    | `id_cliente` → `clientes.id_cliente` (`fk_pedido_cliente`)     |
| `productos` | id_producto (int, auto_increment), nombre varchar(100), categoria varchar(50), precio decimal(10,2), stock int | `id_producto` | `nombre` único; índices por categoría y por (categoría, precio) |

**Relación:** un cliente puede tener muchos pedidos (1:N). Debido a la clave foránea,
no es posible eliminar un cliente que tenga pedidos asociados; la aplicación muestra un
mensaje explicativo en lugar de fallar.

## 7. Estructura del proyecto

```
EDA311_HITO3_ApellidoNombre/
├── index.php                      Menú principal
├── entidad.php                    Consulta: lista enlazada, Bubble Sort y acciones
├── formulario.php                 Registrar y modificar (con validación)
├── eliminar.php                   Eliminar registros
├── config.example.php             Plantilla de conexión (copiar a config.php)
├── .gitignore                     Excluye config.php
├── assets/
│   └── estilos.css                Estilos de la interfaz
├── sql/
│   └── estructura_referencia.sql  Estructura de referencia para pruebas
└── src/
    ├── NodoLista.php                 Nodo: datos, anterior, siguiente
    ├── ListaDoblementeEnlazada.php   Lista, inserción, recorrido y Bubble Sort
    ├── Conexion.php                  Conexión PDO a MySQL
    ├── RepositorioTabla.php          CRUD con consultas preparadas y carga a la lista
    ├── Validador.php                 Validación de datos de formularios
    ├── entidades.php                 Definición de tablas y campos
    └── ayudas.php                    Funciones comunes (HTML, mensajes)
```

## 8. Lista doblemente enlazada

Cada `NodoLista` almacena un registro completo (arreglo asociativo) y dos referencias:
`anterior` y `siguiente`. La clase `ListaDoblementeEnlazada` administra los punteros
`cabeza` y `cola` y el contador `tamano`, y ofrece:

- `insertarAlFinal()` e `insertarAlInicio()`
- `recorrer()` (de cabeza a cola) y `recorrerInverso()` (de cola a cabeza)
- `ordenarBurbuja()`

`RepositorioTabla::cargarEnLista()` ejecuta el `SELECT` en MySQL y va insertando
cada fila como un nodo nuevo. No se usa `SplDoublyLinkedList` ni ninguna biblioteca
que sustituya la implementación.

## 9. Algoritmo Bubble Sort

`ordenarBurbuja($campo, $ascendente)` recorre los nodos comparando cada uno con su siguiente.
Si están en orden incorrecto, intercambia los datos de ambos nodos. Tras cada pasada,
el último nodo de la zona no ordenada queda en su posición final (marcador `$limite`),
y el proceso termina cuando una pasada no produce intercambios.

- Opera **directamente sobre la lista**: no se convierte a arreglo ni se usa `sort()` / `usort()`.
- **Criterio de comparación:** los números se comparan numéricamente y el texto con
  `strcasecmp` (sin distinguir mayúsculas).
- **Criterios por defecto** (modificables desde la interfaz):
  - Clientes: por nombre (A-Z), como una agenda.
  - Pedidos: por fecha, para ver la secuencia cronológica de ventas.
  - Productos: por precio, para comparar valores.
- **Complejidad:** O(n²) en el peor caso y O(n) si la lista ya está ordenada.

La interfaz muestra la lista original, la lista ordenada y estadísticas
(pasadas, comparaciones e intercambios) para comprobar visualmente el resultado.

## 10. Funcionalidades

- Registrar, consultar, modificar y eliminar registros en las tres tablas.
- Validación de datos (campos obligatorios, formato de email, números, fechas, existencia del cliente en pedidos).
- Consultas preparadas contra inyección SQL y escape de salida contra XSS.
- Manejo de errores por claves foráneas y valores únicos duplicados.

## Autor

Nombre Apellido, EDA311, Hito 3
