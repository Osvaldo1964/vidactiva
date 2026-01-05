<?php

class Encuestas extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getEncuestas()
    {
        $arrData = $this->model->selectEncuestas();

        // Formatear para DataTables y Botones de Acción
        for ($i = 0; $i < count($arrData); $i++) {
            $btnEdit = '';
            $btnDelete = '';

            // Estado (Visual)
            if ($arrData[$i]['status_hsurvey'] == 'Activo') {
                $arrData[$i]['status_hsurvey'] = '<span class="badge badge-success">Activo</span>';
            } else {
                $arrData[$i]['status_hsurvey'] = '<span class="badge badge-danger">Inactivo</span>';
            }

            $btnQuestions = '<button class="btn btn-secondary btn-sm btnQuestions" onClick="fntQuestions(' . $arrData[$i]['id_hsurvey'] . ')" title="Preguntas"><i class="fas fa-list-ul"></i></button>';
            $btnEdit = '<button class="btn btn-primary btn-sm btnEditEncuesta" onClick="fntEditEncuesta(' . $arrData[$i]['id_hsurvey'] . ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
            $btnDelete = '<button class="btn btn-danger btn-sm btnDelEncuesta" onClick="fntDelEncuesta(' . $arrData[$i]['id_hsurvey'] . ')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';

            $arrData[$i]['options'] = '<div class="text-center">' . $btnQuestions . ' ' . $btnEdit . ' ' . $btnDelete . '</div>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getEncuesta($idSurvey)
    {
        $intIdSurvey = intval(strClean($idSurvey));
        if ($intIdSurvey > 0) {
            $arrData = $this->model->selectEncuesta($intIdSurvey);
            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function setEncuesta()
    {
        if ($_POST) {
            if (empty($_POST['txtName']) || empty($_POST['txtBeginDate']) || empty($_POST['txtEndDate'])) {
                $arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
            } else {
                $idSurvey = intval($_POST['idSurvey']);
                $idOwner = 1; // Default ID Owner 1 as requested or assumed for now
                $strName = strClean($_POST['txtName']);
                $strObs = strClean($_POST['txtObs']);
                $strBeginDate = strClean($_POST['txtBeginDate']);
                $strEndDate = strClean($_POST['txtEndDate']);

                if ($idSurvey == 0) {
                    // Crear
                    $request_survey = $this->model->insertEncuesta($idOwner, $strName, $strObs, $strBeginDate, $strEndDate);
                    $option = 1;
                } else {
                    // Actualizar
                    $request_survey = $this->model->updateEncuesta($idSurvey, $idOwner, $strName, $strObs, $strBeginDate, $strEndDate);
                    $option = 2;
                }

                if ($request_survey > 0) {
                    if ($option == 1) {
                        $arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
                    } else {
                        $arrResponse = array('status' => true, 'msg' => 'Datos actualizados correctamente.');
                    }
                } else if ($request_survey == 'exist') {
                    $arrResponse = array('status' => false, 'msg' => '¡Atención! Ya existe una encuesta con ese nombre.');
                } else {
                    $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function delEncuesta()
    {
        if ($_POST) {
            $intIdSurvey = intval($_POST['idSurvey']);
            $requestDelete = $this->model->deleteEncuesta($intIdSurvey);
            if ($requestDelete) {
                $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado la encuesta');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al eliminar la encuesta.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    // ----------------------------------------------------------------------------------
    // Métodos para Preguntas (bsurveys)
    // ----------------------------------------------------------------------------------

    public function getQuestions($idSurvey)
    {
        $intIdSurvey = intval(strClean($idSurvey));
        if ($intIdSurvey > 0) {
            $arrData = $this->model->selectQuestionsBySurvey($intIdSurvey);
            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'No hay preguntas.');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function getQuestion($idQuestion)
    {
        $idQuestion = intval($idQuestion);
        if ($idQuestion > 0) {
            $arrData = $this->model->selectQuestion($idQuestion);
            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function setQuestion()
    {
        if ($_POST) {
            if (empty($_POST['idSurveyQuestion']) || empty($_POST['txtQuestion']) || empty($_POST['listType'])) {
                $arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
            } else {
                $idSurvey = intval($_POST['idSurveyQuestion']);
                $idQuestion = !empty($_POST['idQuestion']) ? intval($_POST['idQuestion']) : 0;

                $strQuestion = strClean($_POST['txtQuestion']);
                $intType = intval($_POST['listType']);
                $arrOptions = array();

                if (isset($_POST['txtOptions'])) {
                    $raw = $_POST['txtOptions'];
                    if (is_string($raw)) {
                        $arrOptions = json_decode($raw, true);
                    } else {
                        $arrOptions = $raw;
                    }
                }

                if ($idQuestion > 0) {
                    // Actualizar
                    $request = $this->model->updateQuestion($idQuestion, $strQuestion, $intType, $arrOptions);
                } else {
                    // Insertar
                    $request = $this->model->insertQuestion($idSurvey, $strQuestion, $intType, $arrOptions);
                }

                if ($request > 0) {
                    if ($idQuestion == 0) {
                        // Solo calcular y actualizar orden si es una inserción nueva
                        $currentQuestions = $this->model->selectQuestionsBySurvey($idSurvey);
                        // El nuevo ya fue insertado, así que el count actual es correcto para el orden final (1-based)
                        // Si antes había 5, ahora hay 6. El orden del nuevo debe ser 6.
                        $nextOrder = count($currentQuestions);
                        $this->model->updateQuestionOrder($request, $nextOrder);
                    }

                    $arrResponse = array('status' => true, 'msg' => 'Pregunta guardada.');
                } else {
                    $arrResponse = array("status" => false, "msg" => 'No es posible guardar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function setOrder()
    {
        if ($_POST) {
            $idQuestion = intval($_POST['idQuestion']);
            $order = intval($_POST['order']);

            if ($idQuestion > 0 && $order > 0) {
                $request = $this->model->updateQuestionOrder($idQuestion, $order);
                $arrResponse = array('status' => true, 'msg' => 'Orden actualizado.');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error de datos.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function delQuestion()
    {
        if ($_POST) {
            $intIdQuestion = intval($_POST['idQuestion']);
            $requestDelete = $this->model->deleteQuestion($intIdQuestion);
            if ($requestDelete) {
                $arrResponse = array('status' => true, 'msg' => 'Pregunta eliminada.');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al eliminar.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
