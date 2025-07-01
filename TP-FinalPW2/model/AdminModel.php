<?php
class AdminModel
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    private function getFechaFiltro($filtros)
    {
        switch ($filtros) {
            case 'dia':
                return date('Y-m-d', strtotime('-1 day'));
            case 'semana':
                return date('Y-m-d', strtotime('-1 week'));
            case 'mes':
                return date('Y-m-d', strtotime('-1 month'));
            case 'anio':
                return date('Y-m-d', strtotime('-1 year'));
            default:
                return null;
        }
    }

    public function getCantidadJugadores()
    {
        $query = "SELECT COUNT(*) AS total FROM usuario";
        $result = $this->db->query($query);

        return isset($result[0]['total']) ? $result[0]['total'] : 0;
    }

    public function getCantidadPartidas($filtro)
    {
        $fecha = $this->getFechaFiltro($filtro);
        $where = "";

        if ($fecha) {
            $where = " WHERE fecha_creacion >= '$fecha'";
        }

        $query = "SELECT COUNT(*) AS total FROM partida" . $where;
        $result = $this->db->query($query);
        return isset($result[0]['total']) ? $result[0]['total'] : 0;
    }

    public function getCantidadPreguntas($filtro)
    {
        $fecha = $this->getFechaFiltro($filtro);
        $where = "";

        if ($fecha) {
            $where = " WHERE fecha_creacion >= '$fecha'";
        }

        $query = "SELECT COUNT(*) AS total FROM pregunta" . $where;
        $result = $this->db->query($query);
        return isset($result[0]['total']) ? $result[0]['total'] : 0;
    }

    public function getCantidadPreguntasCreadas($filtro)
    {
        $fecha = $this->getFechaFiltro($filtro);
        $where = "";

        if ($fecha) {
            $where = " WHERE fecha_creacion >= '$fecha' AND creador_id <> 1";
        }

        $query = "SELECT COUNT(*) AS total FROM pregunta" . $where;
        $result = $this->db->query($query);
        return isset($result[0]['total']) ? $result[0]['total'] : 0;
    }

    public function getUsuariosNuevos($filtro)
    {

        $where = "";
        $fecha = $this->getFechaFiltro($filtro);
        if ($fecha) {
            $where = " WHERE fecha_registro >= '$fecha'";
        }
        $query = "SELECT COUNT(*) as total FROM usuario" . $where;
        $result = $this->db->query($query);
        return isset($result[0]['total']) ? $result[0]['total'] : 0;
    }
}