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

    public function crearPregunta(string $texto, int $categoria_id, array $opciones)
    {
        // Insertar la pregunta y obtener su ID
        $query = "INSERT INTO pregunta (texto, categoria_id, estado, fecha_creacion) VALUES (?, ?, 'activa', NOW())";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Error al preparar consulta pregunta: " . $this->db->error);
        }
        $stmt->bind_param("si", $texto, $categoria_id);
        $stmt->execute();
        $pregunta_id = $stmt->insert_id;  // Obtener el ID recién insertado
        $stmt->close();

        // Insertar las respuestas
        $queryResp = "INSERT INTO respuesta (pregunta_id, texto, es_correcta, numero) VALUES (?, ?, ?, ?)";
        $stmtResp = $this->db->prepare($queryResp);
        if (!$stmtResp) {
            die("Error al preparar consulta respuestas: " . $this->db->error);
        }

        foreach ($opciones as $index => $op) {
            $textoResp = $op['texto'];
            $esCorrecta = (int)$op['correcta'];
            $numeroRespuesta = $index + 1;

            $stmtResp->bind_param("isii", $pregunta_id, $textoResp, $esCorrecta, $numeroRespuesta);
            $stmtResp->execute();
        }

        $stmtResp->close();
    }


    public function actualizarPregunta(int $id, string $texto)
    {
        $query = "UPDATE pregunta SET texto = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("si", $texto, $id);
        $stmt->execute();
        $stmt->close();
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