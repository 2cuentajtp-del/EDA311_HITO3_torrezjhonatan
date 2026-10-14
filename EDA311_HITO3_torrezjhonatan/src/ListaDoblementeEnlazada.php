<?php
require_once __DIR__ . '/NodoLista.php';

/**
 * Lista doblemente enlazada implementada desde cero (sin SplDoublyLinkedList).
 * Incluye inserción, recorrido y ordenamiento burbuja directamente sobre los nodos.
 */
class ListaDoblementeEnlazada
{
    private ?NodoLista $cabeza = null;
    private ?NodoLista $cola = null;
    private int $tamano = 0;

    /** Estadísticas de la última ordenación (para mostrarlas en la interfaz). */
    public int $pasadas = 0;
    public int $comparaciones = 0;
    public int $intercambios = 0;

    public function obtenerTamano(): int
    {
        return $this->tamano;
    }

    public function estaVacia(): bool
    {
        return $this->tamano === 0;
    }

    /** Inserta un registro al final de la lista. */
    public function insertarAlFinal(array $datos): void
    {
        $nuevo = new NodoLista($datos);
        if ($this->cola === null) {
            $this->cabeza = $nuevo;
            $this->cola = $nuevo;
        } else {
            $nuevo->anterior = $this->cola;
            $this->cola->siguiente = $nuevo;
            $this->cola = $nuevo;
        }
        $this->tamano++;
    }

    /** Inserta un registro al inicio de la lista. */
    public function insertarAlInicio(array $datos): void
    {
        $nuevo = new NodoLista($datos);
        if ($this->cabeza === null) {
            $this->cabeza = $nuevo;
            $this->cola = $nuevo;
        } else {
            $nuevo->siguiente = $this->cabeza;
            $this->cabeza->anterior = $nuevo;
            $this->cabeza = $nuevo;
        }
        $this->tamano++;
    }

    /** Recorre la lista de cabeza a cola (siguiendo los enlaces "siguiente"). */
    public function recorrer(): Generator
    {
        $actual = $this->cabeza;
        while ($actual !== null) {
            yield $actual->datos;
            $actual = $actual->siguiente;
        }
    }

    /** Recorre la lista de cola a cabeza (siguiendo los enlaces "anterior"). */
    public function recorrerInverso(): Generator
    {
        $actual = $this->cola;
        while ($actual !== null) {
            yield $actual->datos;
            $actual = $actual->anterior;
        }
    }

    /**
     * Bubble Sort sobre la lista (sin convertirla a arreglo).
     *
     * Se compara cada nodo con su siguiente y, si están en orden incorrecto,
     * se intercambian los datos que guardan. Tras cada pasada el último nodo
     * de la zona no ordenada queda en su posición final, por eso se usa
     * $limite como marcador (el nodo desde el cual ya está ordenado).
     * Si una pasada no produce intercambios, la lista ya está ordenada.
     */
    public function ordenarBurbuja(string $campo, bool $ascendente = true): void
    {
        $this->pasadas = 0;
        $this->comparaciones = 0;
        $this->intercambios = 0;

        if ($this->tamano < 2) {
            return;
        }

        $limite = null; // nodo a partir del cual la lista ya está ordenada

        do {
            $huboIntercambio = false;
            $actual = $this->cabeza;
            $this->pasadas++;

            while ($actual->siguiente !== $limite) {
                $siguiente = $actual->siguiente;
                $this->comparaciones++;

                $resultado = $this->compararValores($actual->datos[$campo], $siguiente->datos[$campo]);
                $fueraDeOrden = $ascendente ? ($resultado > 0) : ($resultado < 0);

                if ($fueraDeOrden) {
                    $temporal = $actual->datos;
                    $actual->datos = $siguiente->datos;
                    $siguiente->datos = $temporal;
                    $this->intercambios++;
                    $huboIntercambio = true;
                }
                $actual = $siguiente;
            }

            $limite = $actual; // $actual es el último nodo de la zona no ordenada
        } while ($huboIntercambio && $limite !== $this->cabeza);
    }

    /** Compara dos valores: numéricos como números, texto sin distinguir mayúsculas. */
    private function compararValores($a, $b): int
    {
        if (is_numeric($a) && is_numeric($b)) {
            return (float)$a <=> (float)$b;
        }
        return strcasecmp((string)$a, (string)$b);
    }
}
