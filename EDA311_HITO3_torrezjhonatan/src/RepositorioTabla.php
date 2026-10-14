<?php
require_once __DIR__ . '/ListaDoblementeEnlazada.php';

/** Operaciones CRUD con consultas preparadas para una entidad definida en entidades.php. */
class RepositorioTabla
{
    public function __construct(private PDO $pdo, private array $entidad) {}

    /** Recupera los registros de MySQL y los almacena en una lista doblemente enlazada. */
    public function cargarEnLista(): ListaDoblementeEnlazada
    {
        $lista = new ListaDoblementeEnlazada();
        $consulta = $this->pdo->query($this->entidad['sql'] . ' ORDER BY ' . $this->entidad['pk'] . ' ASC');
        while ($fila = $consulta->fetch()) {
            $lista->insertarAlFinal($fila);
        }
        return $lista;
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = 'SELECT * FROM ' . $this->entidad['tabla'] . ' WHERE ' . $this->entidad['pk'] . ' = ?';
        $st = $this->pdo->prepare($sql);
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    public function insertar(array $valores): void
    {
        $campos = array_keys($valores);
        $marcas = implode(', ', array_fill(0, count($campos), '?'));
        $sql = 'INSERT INTO ' . $this->entidad['tabla'] . ' (' . implode(', ', $campos) . ') VALUES (' . $marcas . ')';
        $this->pdo->prepare($sql)->execute(array_values($valores));
    }

    public function actualizar(int $id, array $valores): void
    {
        $asignaciones = implode(', ', array_map(fn($c) => "$c = ?", array_keys($valores)));
        $sql = 'UPDATE ' . $this->entidad['tabla'] . ' SET ' . $asignaciones . ' WHERE ' . $this->entidad['pk'] . ' = ?';
        $this->pdo->prepare($sql)->execute([...array_values($valores), $id]);
    }

    public function eliminar(int $id): void
    {
        $sql = 'DELETE FROM ' . $this->entidad['tabla'] . ' WHERE ' . $this->entidad['pk'] . ' = ?';
        $this->pdo->prepare($sql)->execute([$id]);
    }

    /** Opciones (id => texto) para un campo de clave foránea. */
    public function opcionesForaneas(array $campo): array
    {
        $sql = "SELECT {$campo['ref_pk']} AS id, {$campo['ref_texto']} AS texto FROM {$campo['ref_tabla']} ORDER BY {$campo['ref_texto']}";
        return $this->pdo->query($sql)->fetchAll();
    }
}
