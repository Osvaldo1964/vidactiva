<?php
class Grafencuestas extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getSurveys()
    {
        $arrData = $this->model->selectEncuestas();
        if (empty($arrData)) {
            jsonResponse(array('status' => false, 'msg' => 'No hay encuestas activas.'), 200);
        } else {
            jsonResponse(array('status' => true, 'data' => $arrData), 200);
        }
        die();
    }

    public function getQuestions($idSurvey)
    {
        if (empty($idSurvey)) {
            jsonResponse(array('status' => false, 'msg' => 'Datos inválidos.'), 200);
            die();
        }

        $arrData = $this->model->selectQuestions($idSurvey);
        if (empty($arrData)) {
            jsonResponse(array('status' => false, 'msg' => 'No hay preguntas para esta encuesta.'), 200);
        } else {
            jsonResponse(array('status' => true, 'data' => $arrData), 200);
        }
        die();
    }

    public function getData($params)
    {
        $arrParams = explode(',', $params);
        $idSurvey = isset($arrParams[0]) ? $arrParams[0] : "";
        $idQuestion = isset($arrParams[1]) ? $arrParams[1] : "";

        if (empty($idSurvey) || empty($idQuestion)) {
            jsonResponse(array('status' => false, 'msg' => 'Datos inválidos.'), 200);
            die();
        }

        $arrData = $this->model->selectRespuestaConteo($idSurvey, $idQuestion);

        // Prepare data specifically for Chart.js
        $labels = [];
        $counts = [];
        $colors = [];

        foreach ($arrData as $row) {
            $labels[] = $row['label'];
            $counts[] = $row['cantidad'];
            // Generate a random color or use a palette generator in JS. 
            // Sending generic data is better, let JS handle colors.
        }

        if (empty($arrData)) {
            jsonResponse(array('status' => false, 'msg' => 'No hay respuestas registradas para esta pregunta.'), 200);
        } else {
            jsonResponse(array(
                'status' => true,
                'data' => $arrData,
                'chartData' => [
                    'labels' => $labels,
                    'counts' => $counts
                ]
            ), 200);
        }
        die();
    }
}
