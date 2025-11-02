<?php
class Habitacion
{
    private $table = "Habitacion";

    private $idHabitacion;
    private $tipoHabitacion;
    private $numeroHabitacion;
    private $estadoHabitacion;
    private $detalleHabitacion;
    private $amenidadesIds = [];

    public function __construct($id = null, $tipoHabitacion = null, $numero = '', $estado = '', $detalle = '')
    {
        $this->idHabitacion = $id;
         $this->tipoHabitacion = $tipoHabitacion;
        $this->numeroHabitacion = $numero;
        $this->estadoHabitacion = $estado;
        $this->detalleHabitacion = $detalle;
       
    }

    public function getIdHabitacion()
    {
        return $this->idHabitacion;
    }

    public function setIdHabitacion($idHabitacion)
    {
        $this->idHabitacion = $idHabitacion;
    }

    public function getTipoHabitacion()
    {
        return $this->tipoHabitacion;
    }

    public function setTipoHabitacion($tipoHabitacion)
    {
        $this->tipoHabitacion = $tipoHabitacion;
    }

    public function getNumeroHabitacion()
    {
        return $this->numeroHabitacion;
    }

    public function setNumeroHabitacion($numeroHabitacion)
    {
        $this->numeroHabitacion = $numeroHabitacion;
    }

    public function getEstadoHabitacion()
    {
        return $this->estadoHabitacion;
    }

    public function setEstadoHabitacion($estadoHabitacion)
    {
        $this->estadoHabitacion = $estadoHabitacion;
    }

    public function getDetalleHabitacion()
    {
        return $this->detalleHabitacion;
    }

    public function setDetalleHabitacion($detalleHabitacion)
    {
        $this->detalleHabitacion = $detalleHabitacion;
    }
    public function setAmenidadesIds(array $amenidades)
    {
        $this->amenidadesIds = $amenidades;
    }
    public function getAmenidadesIds()
    {
        return $this->amenidadesIds;
    }
    
}
?>