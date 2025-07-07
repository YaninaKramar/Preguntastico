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

    public function obtenerPreguntaDelNivelDelUsuario($partida_id, $nivelUsuario, $usuario_id){

        $quedanPreguntasNuevas= $this->quedanPreguntasNuevas($nivelUsuario,$usuario_id);

        if($nivelUsuario){
            $query = "SELECT p.id, p.texto, c.nombre, c.color
                      FROM pregunta p
                      JOIN categoria c ON p.categoria_id = c.id
                      WHERE p.id NOT IN (
                          SELECT pregunta_id FROM partida_pregunta WHERE partida_id = ?
                      )
                      ". ($quedanPreguntasNuevas ? " AND p.id NOT IN (
                              SELECT pregunta_id FROM usuario_pregunta WHERE usuario_id = ?
                          )" : "") . "
                      AND p.dificultad = ?
                      AND p.estado = 'activa'
                      ORDER BY RAND() LIMIT 1";


            if ($quedanPreguntasNuevas) {
                $stmt = $this->database->prepare($query);
                $stmt->bind_param("iis", $partida_id, $usuario_id, $nivelUsuario);
            } else {
                $stmt = $this->database->prepare($query);
                $stmt->bind_param("is", $partida_id, $nivelUsuario);
            }

        } else {
            $query = "SELECT p.id, p.texto, c.nombre, c.color
                  FROM pregunta p
                  JOIN categoria c ON p.categoria_id = c.id
                  WHERE p.id NOT IN (
                      SELECT pregunta_id FROM partida_pregunta WHERE partida_id = ?
                  )" . ($quedanPreguntasNuevas ? " AND p.id NOT IN (
                SELECT pregunta_id FROM usuario_pregunta WHERE usuario_id = ?
                  )" : "") . "
                  AND p.estado = 'activa'
                  ORDER BY RAND() LIMIT 1";

            if ($quedanPreguntasNuevas) {
                $stmt = $this->database->prepare($query);
                $stmt->bind_param("ii", $partida_id, $usuario_id);
            } else {
                $stmt = $this->database->prepare($query);
                $stmt->bind_param("i", $partida_id);
            }

        }
        $stmt->execute();
        $pregunta = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $pregunta;
    }


    private function quedanPreguntasNuevas($nivelUsuario, $usuario_id) {

        if ($nivelUsuario) {//que la pregunta no haya sido respondida por el usuario
            $query = "SELECT COUNT(*) as total
                  FROM pregunta
                  WHERE dificultad = ?
                  AND id NOT IN (
                      SELECT pregunta_id FROM usuario_pregunta WHERE usuario_id = ? 
                  )";
            $stmt = $this->database->prepare($query);
            $stmt->bind_param("si", $nivelUsuario, $usuario_id);
        } else {
            $query = "SELECT COUNT(*) as total
                  FROM pregunta
                  WHERE id NOT IN (
                      SELECT pregunta_id FROM usuario_pregunta WHERE usuario_id = ?
                  )";
            $stmt = $this->database->prepare($query);
            $stmt->bind_param("i", $usuario_id);
        }

        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $resultado['total'] > 0;
    }


    public function obtenerPreguntaAleatoriaNoRespondida($partida_id) {

        $usuario_id = $this->obtenerUsuarioDePartida($partida_id);

        if (!$usuario_id) {
            return null; // No se encontro el usuario
        }

        $nivelUsuario = $this->obtenerNivelDelUsuario($usuario_id);

        $pregunta = $this->obtenerPreguntaDelNivelDelUsuario($partida_id, $nivelUsuario, $usuario_id);

        // Si no hay preguntas del nivel del usuario:
        if (!$pregunta) {
            $pregunta = $this->obtenerPreguntaDelNivelDelUsuario($partida_id, null,$usuario_id);
        }

        // Si esa pregunta ya se estuvo en una partida anterior no repetir:


        if (!$pregunta) {
            return null; // No hay más preguntas disponibles
        }


        // Traer respuestas de esa pregunta
        $queryRespuestas = "SELECT numero, texto, es_correcta FROM respuesta WHERE pregunta_id = ? ORDER BY RAND()";

        $stmt2 = $this->database->prepare($queryRespuestas);
        $stmt2->bind_param("i", $pregunta['id']);
        $stmt2->execute();
        $respuestas = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        $pregunta['respuestas'] = $respuestas;

        return $pregunta;
    }

    public function guardarRespuesta($partida_id, $pregunta_id, $respuesta_usuario_id, $usuario_id) {
        // Obtener la respuesta seleccionada para verificar si es correcta
        $query = "SELECT es_correcta FROM respuesta WHERE numero = ? AND pregunta_id = ?";
        $stmt = $this->database->prepare($query);
        $stmt->bind_param("ii", $respuesta_usuario_id, $pregunta_id);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $correcta = $fila['es_correcta'] ? 1 : 0;

        // Guardar la respuesta en partida_pregunta
        $query2 = "INSERT INTO partida_pregunta (partida_id, pregunta_id, respondida_bien) VALUES (?, ?, ?)";
        $stmt2 = $this->database->prepare($query2);
        $stmt2->bind_param("iii", $partida_id, $pregunta_id, $correcta);
        $stmt2->execute();
        $stmt2->close();

        // Guardar la pregunta en usuario_pregunta
        $query5 = "INSERT INTO usuario_pregunta (usuario_id, pregunta_id) VALUES (?, ?)";
        $stmt5 = $this->database->prepare($query5);
        $stmt5->bind_param("ii", $usuario_id, $pregunta_id);
        $stmt5->execute();
        $stmt5->close();

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

        if ($intentos >= 10) {
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

        // Corroborar que haya respondido al menos 10 preguntas
        if ($result && isset($result['intentos']) && $result['intentos'] >= 10){

            $ratio = $result['ratio'];

            if ($ratio > 0.7) {
                $nivel = 'dificil';
            } elseif ($ratio < 0.3) {
                $nivel = 'facil';
            } else {
                $nivel = 'media';
            }

            // Se acutaliza el nivel porque ya tiene al menos 10 preguntas respondidas
            $update = "UPDATE usuario SET nivel = ? WHERE id = ?";
            $stmt = $this->database->prepare($update);
            $stmt->bind_param("si", $nivel, $usuario_id);
            $stmt->execute();
            $stmt->close();

        } else {
            // Si tiene menos de 10 preguntas respondidas, no se actualiza el nivel
        }
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

    public function obtenerPreguntaPorId($id){
        // Obtener la pregunta y su categoría, solo si está activa
        $query = "SELECT p.id, p.texto, c.nombre AS nombre, c.color AS color
          FROM pregunta p
          JOIN categoria c ON p.categoria_id = c.id
          WHERE p.id = ? AND p.estado = 'activa'";
        $stmt = $this->database->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$result) return null;

        // Obtener respuestas asociadas
        $queryRespuestas = "SELECT texto, numero, es_correcta FROM respuesta WHERE pregunta_id = ?";
        $stmtRespuestas = $this->database->prepare($queryRespuestas);
        $stmtRespuestas->bind_param("i", $id);
        $stmtRespuestas->execute();
        $respuestas = $stmtRespuestas->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmtRespuestas->close();

        // Añadir respuestas al resultado
        $result['respuestas'] = $respuestas;

        return $result;
    }

    public function borrarPreguntasRespondidasSiCompletoLaTabla($usuario_id) {
        // Total de preguntas
        $queryTotal = "SELECT COUNT(*) as total FROM pregunta WHERE estado = 'activa'";
        $stmt = $this->database->prepare($queryTotal);
        $stmt->execute();
        $total = $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();


        // Preguntas respondidas por el usuario
        $queryUser = "SELECT COUNT(*) as respondidas FROM usuario_pregunta WHERE usuario_id = ?";
        $stmt = $this->database->prepare($queryUser);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $respondidas = $stmt->get_result()->fetch_assoc()['respondidas'];
        $stmt->close();

        // Si respondió todas, las borramos de la tabla
        if ($total == $respondidas) {
            $queryDelete = "DELETE FROM usuario_pregunta WHERE usuario_id = ?";
            $stmt = $this->database->prepare($queryDelete);
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $stmt->close();

            return true;
        }

        return false;
    }

    public function reportarPregunta($idPregunta){

        $usuarioId = $_SESSION['usuario_id'];

        $query = "INSERT INTO reporte (estado, usuario_id, pregunta_id) VALUES ('pendiente', ?, ?)";
        $stmt = $this->database->prepare($query);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->database->error);
        }
        $stmt->bind_param("ii", $usuarioId, $idPregunta);
        $stmt->execute();
        $stmt->close();
    }



}