<?php
require_once(__DIR__ . '/../vendor/jpgraph-4.4.2/src/jpgraph.php');
require_once(__DIR__ . '/../vendor/jpgraph-4.4.2/src/jpgraph_bar.php');
require_once(__DIR__ . '/../vendor/jpgraph-4.4.2/src/jpgraph_pie.php');
use Dompdf\Dompdf;
use Dompdf\Options;

class AdminController {
    private $model;
    private $view;

    public function __construct($adminModel, $viewer) {
        $this->model = $adminModel;
        $this->view = $viewer;
    }

    public function show() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login/show');
            exit;
        }


        $filtro = $_POST['filtro']?? 'dia';




        $jugadores = $this->model->getCantidadJugadores();
        $partidas = $this->model->getCantidadPartidas($filtro);
        $preguntas_totales = $this->model->getCantidadPreguntas($filtro);
        $preguntas_creadas = $this->model->getCantidadPreguntasCreadas($filtro);
        $usuarios_nuevos = $this->model->getUsuariosNuevos($filtro);
        $usuarios_por_pais = $this->model->getUsuariosPorPais($filtro);
        $usuarios_por_sexo= $this->model->getUsuariosPorSexo($filtro);
        $usuarios_por_edad = $this->model->getUsuariosPorEdad($filtro);
        $porcentaje_correctas = $this->model->getPorcentajeCorrectasPorUsuario($filtro);

        echo $this->view->render('admin', [
            'jugadores' => $jugadores,
            'partidas' => $partidas,
            'preguntas_totales' => $preguntas_totales,
            'preguntas_creadas' => $preguntas_creadas,
            'usuarios_nuevos' => $usuarios_nuevos,
            'usuarios_por_pais' => $usuarios_por_pais,
            'usuarios_por_sexo' => $usuarios_por_sexo,
            'usuarios_por_edad' => $usuarios_por_edad,
            'porcentaje_correctas' => $porcentaje_correctas,
            'filtro' => $filtro,
            "filtro_{$filtro}" => true
        ]);

    }

    public function graficoPorcentajeCorrectas() {

        $filtro = $_GET['filtro'] ?? 'dia';

        $porcentaje_correctas = $this->model->getPorcentajeCorrectasPorUsuario($filtro);

        if (empty($porcentaje_correctas)) {
            $this->mostrarMensajeSinDatos("");
            return;
        }

        $datos = array_column($porcentaje_correctas, 'porcentaje');
        $nombres = array_column($porcentaje_correctas, 'usuario');

        // Crear gráfico con JpGraph
        $graph = new Graph(600, 400);
        $graph->SetScale('textlin');

        $graph->xaxis->SetTickLabels($nombres);

        $barplot = new BarPlot($datos);
        $barplot->SetFillColor('orange');
        $graph->Add($barplot);

        header('Content-Type: image/png');
        $graph->Stroke();
        exit;
    }

    public function graficoUsuariosPorPais() {

        $filtro = $_GET['filtro'] ?? 'dia';
        $usuarios_por_pais = $this->model->getUsuariosPorPais($filtro);

        if (empty($usuarios_por_pais)) {
            $this->mostrarMensajeSinDatos("");
            return;
        }

        $datos = array_column($usuarios_por_pais, 'total');
        $nombres = array_column($usuarios_por_pais, 'pais');

        $graph = new Graph(600, 400);
        $graph->SetScale('textlin');

        $graph->xaxis->SetTickLabels($nombres);

        $barplot = new BarPlot($datos);
        $barplot->SetFillColor('steelblue');
        $graph->Add($barplot);

        header('Content-Type: image/png');
        $graph->Stroke();
        exit;
    }

    public function graficoUsuariosPorSexo() {


        $filtro = $_GET['filtro'] ?? 'dia';
        $datosSexo = $this->model->getUsuariosPorSexo($filtro);

        if (empty($datosSexo)) {
            $this->mostrarMensajeSinDatos("");
            return;
        }

        $valores = array_column($datosSexo, 'total');
        $labels = array_column($datosSexo, 'sexo');

        $graph = new PieGraph(600, 400);

        $pie = new PiePlot($valores);
        $pie->SetLegends($labels);
        $pie->SetCenter(0.5, 0.5);

        $graph->Add($pie);

        header('Content-Type: image/png');
        $graph->Stroke();
        exit;
    }

    public function graficoUsuariosPorEdad() {


        $filtro = $_GET['filtro'] ?? 'dia';
        $datosEdad = $this->model->getUsuariosPorEdad($filtro);

        if (empty($datosEdad)) {
            $this->mostrarMensajeSinDatos("");
            return;
        }

        $valores = array_column($datosEdad, 'total');
        $labels = array_column($datosEdad, 'grupo');

        $graph = new PieGraph(600, 400);

        $pie = new PiePlot($valores);
        $pie->SetLegends($labels);
        $pie->SetCenter(0.5, 0.5);

        $graph->Add($pie);

        header('Content-Type: image/png');
        $graph->Stroke();
        exit;
    }

    private function mostrarMensajeSinDatos($titulo = 'Sin datos') {
        $graph = new Graph(600, 400);
        $graph->SetScale('textlin');

        $graph->title->Set($titulo);
        $graph->SetMargin(10, 10, 10, 10);

        $barplot = new BarPlot([0]);
        $barplot->SetFillColor('white');
        $graph->Add($barplot);

        $txt = new \Text("No hay datos disponibles para este periodo.");
        $txt->SetFont(FF_FONT1, FS_BOLD);
        $txt->SetPos(0.5, 0.5, 'center');
        $graph->AddText($txt);

        header('Content-Type: image/png');
        $graph->Stroke();
        exit;
    }

    private function generarGraficoBase64Porcentaje($filtro) {
        $porcentaje_correctas = $this->model->getPorcentajeCorrectasPorUsuario($filtro);
        if (empty($porcentaje_correctas)) return null;

        $datos = array_column($porcentaje_correctas, 'porcentaje');
        $nombres = array_column($porcentaje_correctas, 'usuario');

        $graph = new Graph(600, 400);
        $graph->SetScale('textlin');
        $graph->xaxis->SetTickLabels($nombres);

        $barplot = new BarPlot($datos);
        $barplot->SetFillColor('orange');
        $graph->Add($barplot);

        ob_start();
        $graph->Stroke();
        $imageData = ob_get_clean();

        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    private function generarGraficoBase64Pais($filtro) {
        $usuarios_por_pais = $this->model->getUsuariosPorPais($filtro);
        if (empty($usuarios_por_pais)) return null;

        $datos = array_column($usuarios_por_pais, 'total');
        $nombres = array_column($usuarios_por_pais, 'pais');

        $graph = new Graph(600, 400);
        $graph->SetScale('textlin');
        $graph->xaxis->SetTickLabels($nombres);

        $barplot = new BarPlot($datos);
        $barplot->SetFillColor('steelblue');
        $graph->Add($barplot);

        ob_start();
        $graph->Stroke();
        $imageData = ob_get_clean();

        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    private function generarGraficoBase64Sexo($filtro) {
        $datosSexo = $this->model->getUsuariosPorSexo($filtro);
        if (empty($datosSexo)) return null;

        $valores = array_column($datosSexo, 'total');
        $labels = array_column($datosSexo, 'sexo');

        $graph = new PieGraph(600, 400);
        $pie = new PiePlot($valores);
        $pie->SetLegends($labels);
        $pie->SetCenter(0.5, 0.5);
        $graph->Add($pie);

        ob_start();
        $graph->Stroke();
        $imageData = ob_get_clean();

        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    private function generarGraficoBase64Edad($filtro) {
        $datosEdad = $this->model->getUsuariosPorEdad($filtro);
        if (empty($datosEdad)) return null;

        $valores = array_column($datosEdad, 'total');
        $labels = array_column($datosEdad, 'grupo');

        $graph = new PieGraph(600, 400);
        $pie = new PiePlot($valores);
        $pie->SetLegends($labels);
        $pie->SetCenter(0.5, 0.5);
        $graph->Add($pie);

        ob_start();
        $graph->Stroke();
        $imageData = ob_get_clean();

        return 'data:image/png;base64,' . base64_encode($imageData);
    }


    public function descargarPDF() {
        $filtro = $_GET['filtro'] ?? 'dia';

        // Obtener los datos numéricos para mostrar en el resumen general
        $jugadores = $this->model->getCantidadJugadores();
        $partidas = $this->model->getCantidadPartidas($filtro);
        $preguntas_totales = $this->model->getCantidadPreguntas($filtro);
        $preguntas_creadas = $this->model->getCantidadPreguntasCreadas($filtro);
        $usuarios_nuevos = $this->model->getUsuariosNuevos($filtro);

        $base64Porcentaje = $this->generarGraficoBase64Porcentaje($filtro);
        $base64Pais = $this->generarGraficoBase64Pais($filtro);
        $base64Sexo = $this->generarGraficoBase64Sexo($filtro);
        $base64Edad = $this->generarGraficoBase64Edad($filtro);

        $html = "
    <h1 style='text-align:center;'>Estadísticas del Juego</h1>
    <h3 style='text-align:center;'>Filtro: $filtro</h3>

    <div style='margin-bottom: 30px;'>
        <div style='display: flex; justify-content: space-between; text-align: center; font-family: Arial, sans-serif;'>
            <div style='width: 30%; border: 1px solid #ccc; padding: 10px; box-shadow: 1px 1px 5px #ddd;'>
                <h4>Jugadores totales</h4>
                <p style='font-weight: bold; font-size: 24px;'>$jugadores</p>
            </div>
            <div style='width: 30%; border: 1px solid #ccc; padding: 10px; box-shadow: 1px 1px 5px #ddd;'>
                <h4>Partidas jugadas</h4>
                <p style='font-weight: bold; font-size: 24px;'>$partidas</p>
            </div>
            <div style='width: 30%; border: 1px solid #ccc; padding: 10px; box-shadow: 1px 1px 5px #ddd;'>
                <h4>Preguntas en el juego</h4>
                <p style='font-weight: bold; font-size: 24px;'>$preguntas_totales</p>
            </div>
        </div>

        <div style='display: flex; justify-content: space-between; text-align: center; margin-top: 20px; font-family: Arial, sans-serif;'>
            <div style='width: 45%; border: 1px solid #ccc; padding: 10px; box-shadow: 1px 1px 5px #ddd;'>
                <h4>Preguntas Creadas</h4>
                <p style='font-weight: bold; font-size: 24px;'>$preguntas_creadas</p>
            </div>
            <div style='width: 45%; border: 1px solid #ccc; padding: 10px; box-shadow: 1px 1px 5px #ddd;'>
                <h4>Usuarios Nuevos por $filtro</h4>
                <p style='font-weight: bold; font-size: 24px;'>$usuarios_nuevos</p>
            </div>
        </div>
    </div>

    <h4>Porcentaje de Respuestas Correctas</h4>
    <img src='$base64Porcentaje' style='width: 500px; height: auto; margin-bottom: 20px;' />

    <h4>Usuarios por País</h4>
    <img src='$base64Pais' style='width: 500px; height: auto; margin-bottom: 20px;' />

    <h4>Usuarios por Sexo</h4>
    <img src='$base64Sexo' style='width: 500px; height: auto; margin-bottom: 20px;' />

    <h4>Usuarios por Edad</h4>
    <img src='$base64Edad' style='width: 500px; height: auto;' />
    ";

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("estadisticas_$filtro.pdf", ["Attachment" => true]);
        exit;
    }
}