<?php

class TipoHabitacion
{
    private $table = 'TipoHabitacion';

    private $idTipoHabitacion;
    private $nombreTipoHabitacion;
    private $capacidad;
    private $precioTipoHabitacion;

    public function __construct($id, $nombre, $capacidad, $precio)
    {
        $this->idTipoHabitacion = $id;
        $this->nombreTipoHabitacion = $nombre;
        $this->capacidad = $capacidad;
        $this->precioTipoHabitacion = $precio;
    }

    public function getId()
    {
        return $this->idTipoHabitacion;
    }

    public function setId($id)
    {
        $this->idTipoHabitacion = $id;
    }

    public function getNombre()
    {
        return $this->nombreTipoHabitacion;
    }

    public function setNombre($nombre)
    {
        $this->nombreTipoHabitacion = $nombre;
    }

    public function getCapacidad()
    {
        return $this->capacidad;
    }

    public function setCapacidad($capacidad)
    {
        $this->capacidad = $capacidad;
    }

    public function getPrecio()
    {
        return $this->precioTipoHabitacion;
    }

    public function setPrecio($precio)
    {
        $this->precioTipoHabitacion = $precio;
    }


}
?>