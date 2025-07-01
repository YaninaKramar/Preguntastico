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
    public function getUsuariosPorPais($filtro) {
        $where = "";
        $fecha = $this->getFechaFiltro($filtro);
        if ($fecha) {
            $where = " WHERE fecha_registro >= '$fecha'";
        }
        $query="SELECT pais, COUNT(*) as total FROM usuario" . $where . " GROUP BY pais";
        $result = $this->db->query($query);
        return $result;
    }
    public function getUsuariosPorSexo($filtro) {
        $where = "";
        $fecha = $this->getFechaFiltro($filtro);
        if ($fecha) {
            $where = " WHERE fecha_registro >= '$fecha'";
        }
        $query="SELECT sexo, COUNT(*) as total FROM usuario " . $where . " GROUP BY sexo";
        $result = $this->db->query($query);
        return $result;
    }
    public function getUsuariosPorEdad($filtro) {
        $where = "";
        $fecha = $this->getFechaFiltro($filtro);
        if ($fecha) {
            $where = " WHERE fecha_registro >= '$fecha'";
        }

        $stmt = $this->db->query("SELECT fecha_nac FROM usuario" . $where);

        $menores = 0;
        $medio = 0;
        $jubilados = 0;

        foreach ($stmt as $row) {
            $edad = date_diff(date_create($row['fecha_nac']), date_create('today'))->y;
            if ($edad < 18) {
                $menores++;
            } elseif ($edad >= 65) {
                $jubilados++;
            } else {
                $medio++;
            }
        }
        return [
            ['grupo' => 'Menores', 'total' => $menores],
            ['grupo' => 'Medio', 'total' => $medio],
            ['grupo' => 'Jubilados', 'total' => $jubilados]
        ];
    }
    public function getPorcentajeCorrectasPorUsuario($filtro) {
        $where = "";
        $fecha = $this->getFechaFiltro($filtro); // esta función ya la usás en otros métodos
        if ($fecha) {
            $where = "WHERE p.fecha >= '$fecha'";
        }

        $stmt = $this->db->query("
        SELECT u.nombre_completo AS usuario,
               pp.respondida_bien
        FROM partida_pregunta pp
        JOIN partida p ON pp.partida_id = p.id
        JOIN usuario u ON p.usuario_id = u.id
        $where
    ");

        $resumen = [];

        foreach ($stmt as $row) {
            $usuario = $row['usuario'];
            $esCorrecta = (bool)$row['respondida_bien'];

            if (!isset($resumen[$usuario])) {
                $resumen[$usuario] = ['correctas' => 0, 'totales' => 0];
            }

            $resumen[$usuario]['totales']++;
            if ($esCorrecta) {
                $resumen[$usuario]['correctas']++;
            }
        }

        $resultado = [];

        foreach ($resumen as $usuario => $datos) {
            $porcentaje = $datos['totales'] > 0
                ? round(100 * $datos['correctas'] / $datos['totales'], 2)
                : 0;

            $resultado[] = [
                'usuario' => $usuario,
                'porcentaje' => $porcentaje
            ];
        }

        return $resultado;
    }







}
