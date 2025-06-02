<?php

class PartidaModel
{

    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function crearPartida($usuario_id) {
        $query = "INSERT INTO partida (fecha, puntaje_final, usuario_id) VALUES (CURDATE(), 0, ?)";
        $stmt = $this->database->prepare($query);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->database->error);
        }
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function obtenerPreguntaDelNivelDelUsuario($partida_id, $nivelUsuario){

        if($nivelUsuario){
            $query = "SELECT p.id, p.texto, c.nombre, c.color
              FROM pregunta p
              JOIN categoria c ON p.categoria_id = c.id
              WHERE p.id NOT IN (
                  SELECT pregunta_id FROM partida_pregunta WHERE partida_id = ?
              ) AND p.dificultad = ?
              ORDER BY RAND() LIMIT 1";
            $stmt = $this->database->prepare($query);
            $stmt->bind_param("is", $partida_id, $nivelUsuario);
        } else{
            $query = "SELECT p.id, p.texto, c.nombre, c.color
                  FROM pregunta p
                  JOIN categoria c ON p.categoria_id = c.id
                  WHERE p.id NOT IN (
                      SELECT pregunta_id FROM partida_pregunta WHERE partida_id = ?
                  )
                  ORDER BY RAND() LIMIT 1";
            $stmt = $this->database->prepare($query);
            $stmt->bind_param("i", $partida_id);
        }

        $stmt->execute();
        $pregunta = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $pregunta;
    }

    public function obtenerPreguntaAleatoriaNoRespondida($partida_id) {

        $usuario = $this->obtenerUsuarioDePartida($partida_id);

        if (!$usuario) {
            return null; // No se encontro el usuario
        }

        $nivelUsuario = $this->obtenerNivelDelUsuario($usuario);

        $pregunta = $this->obtenerPreguntaDelNivelDelUsuario($partida_id, $nivelUsuario);

        // Si no hay preguntas del nivel del usuario:
        if (!$pregunta) {
            $pregunta = $this->obtenerPreguntaDelNivelDelUsuario($partida_id, null);
        }

        if (!$pregunta) {
            return null; // No hay más preguntas disponibles
        }


        // Traer respuestas de esa pregunta
        $queryRespuestas = "SELECT numero, texto, es_correcta FROM respuesta WHERE pregunta_id = ?";
        $stmt2 = $this->database->prepare($queryRespuestas);
        $stmt2->bind_param("i", $pregunta['id']);
        $stmt2->execute();
        $respuestas = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        $pregunta['respuestas'] = $respuestas;

        return $pregunta;
    }

    public function guardarRespuesta($partida_id, $pregunta_id, $respuesta_usuario_id) {
        // Obtener la respuesta seleccionada para verificar si es correcta
        $query = "SELECT es_correcta FROM respuesta WHERE numero = ? AND pregunta_id = ?";
        $stmt = $this->database->prepare($query);
        $stmt->bind_param("ii", $respuesta_usuario_id, $pregunta_id);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $correcta = $fila['es_correcta'] ? 1 : 0;

        // Guardar la respuesta
        $query2 = "INSERT INTO partida_pregunta (partida_id, pregunta_id, respondida_bien) VALUES (?, ?, ?)";
        $stmt2 = $this->database->prepare($query2);
        $stmt2->bind_param("iii", $partida_id, $pregunta_id, $correcta);
        $stmt2->execute();
        $stmt2->close();

        $query4 = "UPDATE pregunta SET intentos = intentos + 1 WHERE id = ?";
        $stmt4 = $this->database->prepare($query4);
        $stmt4->bind_param("i", $pregunta_id);
        $stmt4->execute();
        $stmt4->close();

        if ($correcta) {
            $query3 = "UPDATE partida SET puntaje_final = puntaje_final + 1 WHERE id = ?";
            $stmt3 = $this->database->prepare($query3);
            $stmt3->bind_param("i", $partida_id);
            $stmt3->execute();
            $stmt3->close();

            $query5 = "UPDATE pregunta SET correctas = correctas + 1 WHERE id = ?";
            $stmt5 = $this->database->prepare($query5);
            $stmt5->bind_param("i", $pregunta_id);
            $stmt5->execute();
            $stmt5->close();
        }

        $this->calcularDificultadPregunta($pregunta_id);

        $usuario_id = $this->obtenerUsuarioDePartida($partida_id);

        if($usuario_id){
            $this->calcularNivelDelUsuario($usuario_id);
        }

        return $correcta;
    }

    public function calcularDificultadPregunta($pregunta_id) {

        $query = "SELECT correctas, intentos FROM pregunta WHERE id = ?";
        $stmt = $this->database->prepare($query);
        $stmt->bind_param("i", $pregunta_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $correctas = $result['correctas'];
        $intentos = $result['intentos'];

        if ($intentos > 0) {
            $ratio = $correctas / $intentos;
            if ($ratio > 0.7) {
                $dificultad = 'facil';
            } elseif ($ratio < 0.3) {
                $dificultad = 'dificil';
            } else {
                $dificultad = 'media';
            }

            // Cambiar dificultad
            $update = "UPDATE pregunta SET dificultad = ? WHERE id = ?";
            $stmt = $this->database->prepare($update);
            $stmt->bind_param("si", $dificultad, $pregunta_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    public function calcularPuntajeFinal($partida_id)
    {
        $query = "
        SELECT COUNT(*) as puntaje
        FROM partida_pregunta
        WHERE partida_id = ? AND respondida_bien = 1
    ";

        $stmt = $this->database->prepare($query);
        $stmt->bind_param("i", $partida_id);
        $stmt->execute();

        $resultado = $stmt->get_result()->fetch_assoc();
        return $resultado['puntaje'];
    }


    public function obtenerRespuestaCorrecta($pregunta_id)
    {
        $stmt = $this->database->prepare("
        SELECT r.texto, r.numero
        FROM respuesta r
        WHERE r.pregunta_id = ? AND r.es_correcta = 1
        LIMIT 1
    ");
        $stmt->bind_param("i", $pregunta_id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    private function obtenerUsuarioDePartida($partida_id){
        $query = "SELECT usuario_id FROM partida WHERE id = ?";
        $stmt = $this->database->prepare($query);
        $stmt->bind_param("i", $partida_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result ? $result['usuario_id'] : null;
    }

    private function calcularNivelDelUsuario($usuario_id){
        $query = "SELECT 
                    SUM(pp.respondida_bien) AS correctas,
                    COUNT(*) AS intentos,
                    ROUND(SUM(pp.respondida_bien) / COUNT(*), 2) AS ratio
                  FROM partida_pregunta pp
                  JOIN partida p ON pp.partida_id = p.id
                  WHERE p.usuario_id = ?";

        $stmt = $this->database->prepare($query);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $nivel = '';

        if ($result && isset($result['intentos']) && $result['intentos'] > 0){

            $ratio = $result['ratio'];

            if ($ratio > 0.7) {
                $nivel = 'dificil';
            } elseif ($ratio < 0.3) {
                $nivel = 'facil';
            } else {
                $nivel = 'media';
            }
        } else {
            $nivel = 'media';
        }


        $update = "UPDATE usuario SET nivel = ? WHERE id = ?";
        $stmt = $this->database->prepare($update);
        $stmt->bind_param("si", $nivel, $usuario_id);
        $stmt->execute();
        $stmt->close();
    }

    private function obtenerNivelDelUsuario($usuario_id){
        $query = "SELECT nivel FROM usuario WHERE id = ?";
        $stmt = $this->database->prepare($query);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $resultNivel = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $resultNivel['nivel'];
    }

}