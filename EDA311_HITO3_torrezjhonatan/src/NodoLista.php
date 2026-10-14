<?php
/**
 * Nodo de la lista doblemente enlazada.
 * Guarda un registro completo (arreglo asociativo) y los enlaces
 * al nodo anterior y al siguiente.
 */
class NodoLista
{
    public array $datos;
    public ?NodoLista $anterior = null;
    public ?NodoLista $siguiente = null;

    public function __construct(array $datos)
    {
        $this->datos = $datos;
    }
}
