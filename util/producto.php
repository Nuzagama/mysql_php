<?php

class Producto {
    public int $idProducto;
    public string $nombreProducto;
    public float $precio;
    public string $descripcion;
    public int $cantidad;
    public string $imagen;

    public function __construct(int $idProducto, string $nombreProducto, float $precio, string $descripcion, int $cantidad, string $imagen) {
        $this->idProducto = $idProducto;
        $this->nombreProducto = $nombreProducto;
        $this->precio = $precio;
        $this->descripcion = $descripcion;
        $this->cantidad = $cantidad;
        $this->imagen = $imagen;
    }
}




?>