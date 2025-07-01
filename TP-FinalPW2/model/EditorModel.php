<?php

class EditorModel
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }
    private function mapPreguntasConOpciones(array $preguntasRaw): array
    {
        $preguntas = [];
        foreach ($preguntasRaw as $p) {
            // traemos las opciones de esa pregunta
            $opcionesRaw = $this->db->query(
                "SELECT texto, es_correcta FROM respuesta WHERE pregunta_id = {$p['id']} ORDER BY id"
            );
            $opciones = array_map(function ($row) {
                return [
                    'texto'    => $row['texto'],
                    'correcta' => (bool)$row['es_correcta'],
                ];
            }, $opcionesRaw);

            $preguntas[] = [
                'id'       => $p['id'],
                'texto'    => $p['texto'],
                'estado'   => $p['estado'],
                'opciones' => $opciones,
            ];
        }
        return $preguntas;
    }

    public function crearPregunta(string $texto, array $opciones): int
    {
        $this->db->beginTransaction();
        $this->db->query("INSERT INTO pregunta (texto, estado, fecha_creacion) VALUES ('$texto', 'aprobada', NOW())");
        $preguntaId = $this->db->lastInsertId();

        foreach ($opciones as $op) {
            $txt = $op['texto'];
            $correct = $op['correcta'] ? 1 : 0;
            $this->db->query("INSERT INTO respuesta (pregunta_id, texto, es_correcta) VALUES ($preguntaId, '$txt', $correct)");
        }
        $this->db->commit();
        return $preguntaId;
    }

    public function actualizarPregunta(int $id, string $texto, array $opciones): void
    {
        $this->db->beginTransaction();
        $this->db->query("UPDATE pregunta SET texto = '$texto' WHERE id = $id");
        // Simplificación: borrar y volver a insertar opciones
        $this->db->query("DELETE FROM respuesta WHERE pregunta_id = $id");
        foreach ($opciones as $op) {
            $txt = $op['texto'];
            $correct = $op['correcta'] ? 1 : 0;
            $this->db->query("INSERT INTO respuesta (pregunta_id, texto, es_correcta) VALUES ($id, '$txt', $correct)");
        }
        $this->db->commit();
    }

    public function eliminarPregunta(int $id): void
    {
        $this->db->query("UPDATE pregunta SET estado = 'eliminada' WHERE id = $id");
    }

    public function getPreguntas(): array
    {
        $raw = $this->db->query("SELECT id, texto, estado FROM pregunta WHERE estado <> 'eliminada' ORDER BY fecha_creacion DESC");
        return $this->mapPreguntasConOpciones($raw);
    }

    public function getPreguntasSugeridas(): array
    {
        $raw = $this->db->query("SELECT id, texto, estado FROM pregunta WHERE estado = 'pendiente' ORDER BY fecha_creacion DESC");
        return $this->mapPreguntasConOpciones($raw);
    }

    public function aprobarSugerida(int $id): void
    {
        $this->db->query("UPDATE pregunta SET estado = 'aprobada' WHERE id = $id AND estado = 'pendiente'");
    }

    public function rechazarSugerida(int $id): void
    {
        $this->db->query("UPDATE pregunta SET estado = 'rechazada' WHERE id = $id AND estado = 'pendiente'");
    }

    public function getPreguntasReportadas(): array
    {
        $raw = $this->db->query(
            "SELECT p.id, p.texto, p.estado, COUNT(r.id) AS cantidad_reportes
             FROM pregunta p
             JOIN reporte r ON r.pregunta_id = p.id AND r.estado = 'pendiente'
             WHERE p.estado = 'aprobada'
             GROUP BY p.id, p.texto, p.estado
             ORDER BY cantidad_reportes DESC"
        );
        return $this->mapPreguntasConOpciones($raw);
    }

    public function aprobarReporte(int $preguntaId): void
    {
        // Se considera que la pregunta es válida → cerramos reportes
        $this->db->beginTransaction();
        $this->db->query("UPDATE reporte SET estado = 'cerrado' WHERE pregunta_id = $preguntaId AND estado = 'pendiente'");
        $this->db->commit();
    }

    public function darBajaReportada(int $preguntaId): void
    {
        $this->db->beginTransaction();
        $this->db->query("UPDATE pregunta SET estado = 'dada_baja' WHERE id = $preguntaId");
        $this->db->query("UPDATE reporte SET estado = 'cerrado' WHERE pregunta_id = $preguntaId AND estado = 'pendiente'");
        $this->db->commit();
    }
}