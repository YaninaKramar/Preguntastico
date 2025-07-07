<?php

class EditorModel
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }
    private function mapPreguntasConOpciones(array $preguntasRaw)
    {
        $preguntas = [];
        foreach ($preguntasRaw as $p) {
            // traemos las opciones de esa pregunta
            $opcionesRaw = $this->db->query(
                "SELECT texto, es_correcta FROM respuesta WHERE pregunta_id = {$p['id']} ORDER BY pregunta_id"
            );
            $opciones = array_map(function ($row) {
                return ['texto'    => $row['texto'], 'correcta' => (bool)$row['es_correcta'],];
            }, $opcionesRaw);

            $preguntas[] = ['id'       => $p['id'], 'texto'    => $p['texto'], 'estado'   => $p['estado'], 'opciones' => $opciones,];
        }
        return $preguntas;
    }

    public function crearPregunta(string $texto, array $opciones)
    {
        $this->db->beginTransaction();
        $this->db->query("INSERT INTO pregunta (texto, estado, fecha_creacion) VALUES ('$texto', 'aprobada', NOW())");
        $preguntaId = $this->db->lastInsertId();

        foreach ($opciones as $op) {$txt = $op['texto']; $correct = $op['correcta'] ? 1 : 0; $this->db->query("INSERT INTO respuesta (pregunta_id, texto, es_correcta) VALUES ($preguntaId, '$txt', $correct)");
        }
        $this->db->commit();
        return $preguntaId;
    }

    public function actualizarPregunta(int $id, string $texto, array $opciones)
    {
        $this->db->beginTransaction();
        $this->db->query("UPDATE pregunta SET texto = '$texto' WHERE id = $id");
        // Simplificación: borrar y volver a insertar opciones
        $this->db->query("DELETE FROM respuesta WHERE pregunta_id = $id");
        foreach ($opciones as $op) {$txt = $op['texto'];$correct = $op['correcta'] ? 1 : 0;$this->db->query("INSERT INTO respuesta (pregunta_id, texto, es_correcta) VALUES ($id, '$txt', $correct)");
        }
        $this->db->commit();
    }

    public function eliminarPregunta(int $id)
    {
        $query = "UPDATE pregunta SET estado = 'eliminada' WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    public function getPreguntas(): array
    {
        $raw = $this->db->query("SELECT id, texto, estado FROM pregunta WHERE estado = 'activa' ORDER BY fecha_creacion DESC");
        return $this->mapPreguntasConOpciones($raw);
    }

    public function getPreguntasSugeridas()
    {
        $raw = $this->db->query("SELECT id, texto, estado FROM pregunta WHERE estado = 'sugerida' ORDER BY fecha_creacion DESC");
        return $this->mapPreguntasConOpciones($raw);
    }

    public function aprobarSugerida(int $id)
    {
        $query = "UPDATE pregunta SET estado = 'activa' WHERE id = ? AND estado = 'sugerida'";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    public function rechazarSugerida(int $id)
    {
        $query = "UPDATE pregunta SET estado = 'rechazada' WHERE id = ? AND estado = 'sugerida'";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    public function getPreguntasReportadas()
    {
        $raw = $this->db->query("
        SELECT 
            p.id, 
            p.texto, 
            p.estado, 
            COUNT(r.id) AS cantidad_reportes 
        FROM 
            pregunta p 
        JOIN 
            reporte r ON r.pregunta_id = p.id AND r.estado = 'pendiente' 
        WHERE 
            p.estado = 'reportada' 
        GROUP BY 
            p.id, p.texto, p.estado 
        HAVING 
            COUNT(r.id) >= 3
        ORDER BY 
            cantidad_reportes DESC
    ");

        return $this->mapPreguntasConOpciones($raw);
    }


    public function aprobarReporte(int $preguntaId)
    {
        // Se considera que la pregunta es válida y cerramos reportes

        $query = "UPDATE reporte SET estado = 'cerrado' WHERE pregunta_id = ? AND estado = 'pendiente'";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("i", $preguntaId);
        $stmt->execute();
        $stmt->close();

        $query = "UPDATE pregunta SET estado = 'activa' WHERE id = ? AND estado = 'reportada'";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("i", $preguntaId);
        $stmt->execute();
        $stmt->close();
    }

    public function darBajaReportada(int $preguntaId)
    {
        $query = "UPDATE pregunta SET estado = 'eliminada' WHERE id = ? AND estado = 'reportada'";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("i", $preguntaId);
        $stmt->execute();
        $stmt->close();

        $query = "UPDATE reporte SET estado = 'cerrado' WHERE pregunta_id = ? AND estado = 'pendiente'";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("i", $preguntaId);
        $stmt->execute();
        $stmt->close();
    }
}