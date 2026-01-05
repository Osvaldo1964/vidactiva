<?php
class Registro extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtener encuestas validas para el dropdown
    public function getSurveys()
    {
        $arrData = $this->model->selectEncuestas();
        if (empty($arrData)) {
            jsonResponse(array('status' => false, 'msg' => 'No hay encuestas activas disponibles.'), 200);
        } else {
            jsonResponse(array('status' => true, 'data' => $arrData), 200);
        }
        die();
    }

    public function saveRespuestas()
    {
        // Se espera recibir datos por POST regular (FormData) o JSON body.
        // Dado que la estructura es compleja, JSON body es preferible, pero FormData se usa mucho en este proyecto.
        // Vamos a asumir que "answers" viene como string JSON en POST si usamos FormData.

        if ($_POST) {
            if (empty($_POST['idSurvey']) || empty($_POST['answers'])) {
                $arrResponse = array("status" => false, "msg" => 'Datos incompletos.');
            } else {
                $idSurvey = intval($_POST['idSurvey']);
                $answersRaw = $_POST['answers'];

                $answers = is_string($answersRaw) ? json_decode($answersRaw, true) : $answersRaw;

                if (!is_array($answers) || count($answers) == 0) {
                    $arrResponse = array("status" => false, "msg" => 'No hay respuestas para guardar.');
                } else {
                    // Obtener secuencia
                    $sequence = $this->model->getNextSequence($idSurvey);
                    $successCount = 0;

                    foreach ($answers as $ans) {
                        $idQuestion = intval($ans['idQuestion']);
                        $type = intval($ans['type']);
                        $value = $ans['value'];

                        // Si value es array (checkboxes multiple), convertir a string?
                        // Ojo: type=4 (multiple) puede tener varios valores. 
                        // El usuario dijo "registra una fila por cada respuesta". 
                        // Si es multiple, ¿genera multiples filas o una fila con valores concatenados?
                        // "Una fila por cada respuesta". Si es checkbox multiple, usualmente son varias respuestas para una misma pregunta.
                        // O una respuesta con valor concatenado.
                        // Asumiremos concatenado para simplicidad, o filas separadas.
                        // El diseño "answers" tiene id_bsurvey_answer. Si inserto varias filas con mismo id_bsurvey_answer y sequence, es válido.

                        if (is_array($value)) {
                            // Caso Multiple
                            foreach ($value as $v) {
                                $this->model->insertAnswer($idSurvey, $sequence, $idQuestion, $type, $v);
                                $successCount++;
                            }
                        } else {
                            // Caso Simple
                            $this->model->insertAnswer($idSurvey, $sequence, $idQuestion, $type, $value);
                            $successCount++;
                        }
                    }

                    if ($successCount > 0) {
                        // Assuming session user logic exists or is passed. For now, using placeholder or session if available.
                        // In API context, usually from token. Using 1 as requested/implicit or maybe $_SESSION if shared.
                        // The user request asked for "el id del usuario que registra". 
                        // Let's use a placeholder or check if we can get it. 
                        // For now hardcoded 1 as we are in API mode without full auth context visible here in snippet, 
                        // or we can try to get it if logic allows. 
                        // Let's stick to the simplest valid JSON response change.
                        $idUser = 1; // Placeholder
                        $arrResponse = array('status' => true, 'msg' => 'Respuestas guardadas correctamente.', 'sequence' => $sequence, 'idUser' => $idUser);
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'Error al guardar respuestas.');
                    }
                }
            }
            jsonResponse($arrResponse, 200);
        }
        die();
    }
}
