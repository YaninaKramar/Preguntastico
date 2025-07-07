<?php
class SugerirPreguntaModel
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function getCategorias(): array
    {
        return $this->db->query("SELECT id, nombre FROM categoria ORDER BY nombre");
    }

    public function crearPregunta(string $textoPregunta, int $categoriaId, int $creadorId, array $respuestas): int
    {
        $conn = $this->db->getConnection();  // obtener la conexión mysqli

        $conn->begin_transaction();

        try {
            $sqlPregunta = "INSERT INTO pregunta (texto, estado, categoria_id, creador_id) VALUES (?, 'sugerida', ?, ?)";
            $stmt = $conn->prepare($sqlPregunta);
            $stmt->bind_param("sii", $textoPregunta, $categoriaId, $creadorId);
            $stmt->execute();
            $preguntaId = $stmt->insert_id;

            $sqlRespuesta = "INSERT INTO respuesta (pregunta_id, numero, texto, es_correcta) VALUES (?, ?, ?, ?)";
            $stmtResp = $conn->prepare($sqlRespuesta);

            $numero = 1;
            foreach ($respuestas as $resp) {
                [$texto, $esCorrecta] = $resp;
                $esCorrectaInt = $esCorrecta ? 1 : 0;
                $stmtResp->bind_param("iisi", $preguntaId, $numero, $texto, $esCorrectaInt);
                $stmtResp->execute();
                $numero++;
            }

            $conn->commit();

            return $preguntaId;
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }

}

