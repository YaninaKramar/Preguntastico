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
        $stmt = $this->db->query("SELECT id, nombre FROM categoria ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function crearPregunta(string $textoPregunta, int $categoriaId, ?int $creadorId, array $respuestas): int
    {
        try {
            $this->db->beginTransaction();

            $sqlPregunta = "INSERT INTO pregunta (texto, estado, categoria_id, creador_id) VALUES (:texto, 'sugerida', :cat, :creador)";
            $stmt = $this->db->prepare($sqlPregunta);
            $stmt->execute([
                ':texto'   => $textoPregunta,
                ':cat'     => $categoriaId,
                ':creador' => $creadorId
            ]);
            $preguntaId = (int)$this->db->lastInsertId();

            $sqlRespuesta = "INSERT INTO respuesta (pregunta_id, numero, texto, es_correcta) VALUES (:pid, :num, :texto, :correcta)";
            $stmtResp    = $this->db->prepare($sqlRespuesta);
            $numero      = 1;
            foreach ($respuestas as $resp) {
                [$texto, $esCorrecta] = $resp;
                $stmtResp->execute([
                    ':pid'      => $preguntaId,
                    ':num'      => $numero++,
                    ':texto'    => $texto,
                    ':correcta' => $esCorrecta ? 1 : 0,
                ]);
            }

            $this->db->commit();
            return $preguntaId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}

