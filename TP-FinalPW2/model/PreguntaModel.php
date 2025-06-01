<?php

class PreguntaModel
{

    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function obtenerPreguntaAleatoriaNoRespondida($partida_id) {
        $query = "SELECT p.id, p.texto, c.color
              FROM preguntas p
              JOIN categorias c ON p.categoria_id = c.id
              WHERE p.id NOT IN (
                  SELECT pregunta_id FROM partida_pregunta WHERE partida_id = ?
              )
              ORDER BY RAND() LIMIT 1";
        $stmt = $this->database->prepare($query);
        $stmt->bind_param("i", $partida_id);
        $stmt->execute();
        $pregunta = $stmt->get_result()->fetch_assoc();
        if (!$pregunta) {
            return null; // No quedan preguntas
        }

        // Traer respuestas de esa pregunta
        $queryRespuestas = "SELECT numero, texto FROM respuestas WHERE pregunta_id = ?";
        $stmt2 = $this->database->prepare($queryRespuestas);
        $stmt2->bind_param("i", $pregunta['id']);
        $stmt2->execute();
        $respuestas = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        $pregunta['respuestas'] = $respuestas;

        return $pregunta;
    }

    public function guardarRespuesta($partida_id, $pregunta_id, $respuesta_usuario_id) {
        // Obtener la respuesta seleccionada para verificar si es correcta
        $query = "SELECT es_correcta FROM respuestas WHERE numero = ? AND pregunta_id = ?";
        $stmt = $this->database->prepare($query);
        $stmt->bind_param("ii", $respuesta_usuario_id, $pregunta_id);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();

        $correcta = $fila['es_correcta'] ? 1 : 0;

        // Guardar la respuesta
        $query2 = "INSERT INTO partida_pregunta (partida_id, pregunta_id, respondida_bien) VALUES (?, ?, ?)";
        $stmt2 = $this->database->prepare($query2);
        $stmt2->bind_param("iii", $partida_id, $pregunta_id, $correcta);
        $stmt2->execute();

        if ($correcta) {
            $query3 = "UPDATE partida SET puntaje_final = puntaje_final + 1 WHERE id = ?";
            $stmt3 = $this->database->prepare($query3);
            $stmt3->bind_param("i", $partida_id);
            $stmt3->execute();
        }

        return $correcta;
    }


}