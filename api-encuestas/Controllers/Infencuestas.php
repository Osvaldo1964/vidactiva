<?php
class Infencuestas extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getSurveys()
    {
        $arrData = $this->model->selectEncuestas();
        if (empty($arrData)) {
            jsonResponse(array('status' => false, 'msg' => 'No hay encuestas disponibles.'), 200);
        } else {
            jsonResponse(array('status' => true, 'data' => $arrData), 200);
        }
        die();
    }

    public function getReporte($idSurvey)
    {
        $idSurvey = intval($idSurvey);
        if ($idSurvey > 0) {
            // 1. Fetch Columns (Questions)
            $arrQuestions = $this->model->selectQuestions($idSurvey);

            // 2. Fetch Data (Answers)
            $arrAnswers = $this->model->selectRespuestas($idSurvey);

            if (empty($arrQuestions) || empty($arrAnswers)) {
                jsonResponse(array('status' => false, 'msg' => 'No hay datos para esta encuesta.'), 200);
                die();
            }

            // 3. Pivot Data
            // We need a structured JSON: { columns: [...], data: [...] }

            // Build Columns
            // Fixed columns first: Secuencia, Fecha
            $columns = array();
            $columns[] = array('title' => 'Secuencia', 'data' => 'sequence');
            $columns[] = array('title' => 'Fecha', 'data' => 'date');

            // Dynamic columns from Questions
            // Map question ID to a clean key for data row
            $qMap = array();
            foreach ($arrQuestions as $q) {
                // Use ID as key to map answers strictly
                $key = 'q_' . $q['id_bsurvey'];
                $qMap[$q['id_bsurvey']] = $key;

                $columns[] = array(
                    'title' => $q['question_bsurvey'],
                    'data' => $key,
                    'defaultContent' => '' // Handle empty answers
                );
            }

            // Build Data Rows
            $rows = array();
            $tempRow = array();
            $currentSeq = -1;

            foreach ($arrAnswers as $ans) {
                $seq = $ans['sequence_answer'];

                // New Sequence -> New Row
                if ($seq != $currentSeq) {
                    if ($currentSeq != -1) {
                        $rows[] = $tempRow;
                    }
                    $currentSeq = $seq;
                    $tempRow = array();
                    $tempRow['sequence'] = $seq;
                    $tempRow['date'] = $ans['date_created_answer'];
                    // IMPORTANT: Add 'id_primary' for Frontend Actions
                    $tempRow['id_primary'] = $seq;
                }

                // Add Answer to current Row
                $qKey = isset($qMap[$ans['id_bsurvey_answer']]) ? $qMap[$ans['id_bsurvey_answer']] : null;

                if ($qKey) {
                    $val = trim($ans['detail_answer']); // Trim to remove whitespace

                    if ($val !== '') { // Only process if value is not empty
                        // 1. JSON Formatting Logic
                        // Check if value is a JSON string (starts with { and ends with })
                        if (strpos($val, '{') === 0 && substr($val, -1) === '}') {
                            $jsonObj = json_decode($val, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonObj)) {
                                $formattedParts = [];
                                foreach ($jsonObj as $k => $v) {
                                    // Skip empty values or technical placeholders if needed
                                    if ($v !== "" && $v !== "-" && $v !== "null") {
                                        // Human readable key: Capitalize first letter
                                        $label = ucfirst($k);
                                        $formattedParts[] = "$label: $v";
                                    }
                                }
                                // Join with comma or newline. Comma is safer for Datatables default.
                                $val = implode(", ", $formattedParts);
                            }
                        }

                        // 2. Concatenation & Deduplication Logic
                        if (isset($tempRow[$qKey]) && $tempRow[$qKey] !== '') {
                            // Check if the value is already present to avoid duplicates (e.g. "Nombre | Nombre")
                            // We explode existing content to check individual parts
                            $existingParts = explode(" | ", $tempRow[$qKey]);
                            if (!in_array($val, $existingParts)) {
                                $tempRow[$qKey] .= " | " . $val;
                            }
                        } else {
                            $tempRow[$qKey] = $val;
                        }
                    }
                }
            }
            // Add last row
            if (!empty($tempRow)) {
                $rows[] = $tempRow;
            }

            $arrResponse = array(
                'status' => true,
                'columns' => $columns, // For DataTables definition
                'data' => $rows
            );

            jsonResponse($arrResponse, 200);
        } else {
            jsonResponse(array('status' => false, 'msg' => 'ID Encuesta inválido.'), 200);
        }
        die();
    }

    // Obtener datos para el formulario de edición
    public function getEncuestado($params)
    {
        // El Router pasa los parámetros como una cadena separada por comas ej: "3,1"
        $arrParams = explode(',', $params);
        $idSurvey = isset($arrParams[0]) ? intval($arrParams[0]) : 0;
        $sequence = isset($arrParams[1]) ? intval($arrParams[1]) : 0;

        if ($idSurvey > 0 && $sequence > 0) {
            // 1. Obtener estructura de preguntas (para renderizar form)
            $arrQuestions = $this->model->selectQuestions($idSurvey);

            // 2. Obtener respuestas guardadas
            $arrAnswers = $this->model->selectRespuestasPorSecuencia($idSurvey, $sequence);

            if (empty($arrQuestions)) {
                echo json_encode(array('status' => false, 'msg' => 'Datos no encontrados.'));
                die();
            }

            // 3. Mapear respuestas por ID de pregunta para fácil acceso en JS
            $mappedAnswers = array();
            foreach ($arrAnswers as $ans) {
                // Si es checkbox multiple, podríamos tener varias filas o un CSV? 
                // Asumimos 1 fila por opción seleccionada si la lógica de inserción fue así.
                // Si la lógica inserta múltiples filas para checkbox:
                if (!isset($mappedAnswers[$ans['id_bsurvey_answer']])) {
                    $mappedAnswers[$ans['id_bsurvey_answer']] = array();
                }
                $mappedAnswers[$ans['id_bsurvey_answer']][] = array(
                    'value' => $ans['detail_answer'],
                    'id_answer' => $ans['id_answer']
                );
            }

            $arrResponse = array(
                'status' => true,
                'questions' => $arrQuestions,
                'answers' => $mappedAnswers
            );

            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('status' => false, 'msg' => 'Parámetros inválidos.'));
        }
        die();
    }

    // Guardar cambios del encuestado
    public function setEncuestado()
    {
        if ($_POST) {
            if (empty($_POST['idSurvey']) || empty($_POST['sequence'])) {
                echo json_encode(array('status' => false, 'msg' => 'Error de datos.'));
                die();
            }

            $idSurvey = intval($_POST['idSurvey']);
            $sequence = intval($_POST['sequence']);

            // 1. Obtener Tipos de Preguntas para saber cómo procesar
            $arrQuestions = $this->model->selectQuestions($idSurvey);
            $typesMap = array();
            foreach ($arrQuestions as $q) {
                $typesMap[$q['id_bsurvey']] = $q['type_bsurvey'];
            }

            // Recorrer POST para encontrar campos q_ID
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'q_') === 0) {
                    $idQuestion = intval(str_replace('q_', '', $key));

                    if (isset($typesMap[$idQuestion])) {
                        $type = $typesMap[$idQuestion];

                        // Si es array (Checkbox - Tipo 4 o Compuesta - Tipo 5 si se envía como array)
                        if (is_array($value)) {
                            // Borrar anteriores para evitar duplicados o datos viejos
                            $this->model->deleteRespuestasPorPregunta($idSurvey, $sequence, $idQuestion);

                            // Insertar nuevas opciones
                            foreach ($value as $val) {
                                // Si el valor está vacío, saltar? Depende.
                                if (!empty($val)) {
                                    $this->model->insertRespuesta($idSurvey, $sequence, $idQuestion, $val, $type);
                                }
                            }
                        }
                        // Valor simple (Texto, Fecha, Radio - Tipos 1, 2, 3)
                        else {
                            // Podríamos hacer Delete/Insert también para estandarizar, 
                            // pero Update es eficiente si ya existe fila.
                            // Riesgo: Si antes era checkbox (error de datos) y hay multiples filas, Update actualizaría todas a lo mismo.
                            // Por seguridad de integridad, Delete/Insert es mejor si cambiamos lógica de visualización,
                            // Pero Update conserva metadatos si los hubiera (id_answer, fecha original...).
                            // Mantengamos Update para simples.
                            $this->model->updateRespuestaByPregunta($idSurvey, $sequence, $idQuestion, $value);
                        }
                    }
                }
            }

            echo json_encode(array('status' => true, 'msg' => 'Datos actualizados correctamente.'));
        }
        die();
    }
    public function delRespuesta($params)
    {
        if ($_SERVER['REQUEST_METHOD'] == "DELETE" || $_SERVER['REQUEST_METHOD'] == "POST") {
            $arrParams = explode(',', $params);
            $idSurvey = isset($arrParams[0]) ? intval($arrParams[0]) : 0;
            $sequence = isset($arrParams[1]) ? intval($arrParams[1]) : 0;

            if ($idSurvey <= 0 || $sequence <= 0) {
                jsonResponse(array('status' => false, 'msg' => 'Datos incorrectos.'), 200);
                die();
            }

            $request = $this->model->deleteRespuestas($idSurvey, $sequence);
            if ($request) {
                jsonResponse(array('status' => true, 'msg' => 'Registro eliminado con éxito.'), 200);
            } else {
                jsonResponse(array('status' => false, 'msg' => 'Error al eliminar registro.'), 200);
            }
        } else {
            jsonResponse(array('status' => false, 'msg' => 'Método no permitido.'), 405);
        }
        die();
    }
}
