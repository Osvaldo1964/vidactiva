<?php
class Centros extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCentros()
    {
        $arrData = $this->model->selectCentros();
        for ($i = 0; $i < count($arrData); $i++) {
            $btnView = '';
            $btnEdit = '';
            $btnDelete = '';

            if (intval($arrData[$i]['estado_centro']) == 1) {
                $arrData[$i]['estado_centro'] = '<span class="badge badge-success">Activo</span>';
            } else {
                $arrData[$i]['estado_centro'] = '<span class="badge badge-danger">Inactivo</span>';
            }

            $btnEdit = '<button class="btn btn-primary btn-sm btnEditCentro" onClick="fntEditCentro(' . $arrData[$i]['id_centro'] . ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
            $btnDelete = '<button class="btn btn-danger btn-sm btnDelCentro" onClick="fntDelCentro(' . $arrData[$i]['id_centro'] . ')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';

            $arrData[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . ' ' . $btnDelete . '</div>';
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getCentro($idcentro)
    {
        $idcentro = intval($idcentro);
        if ($idcentro > 0) {
            $arrData = $this->model->selectCentro($idcentro);
            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function setCentro()
    {
        if ($_POST) {
            if (empty($_POST['txtNombre']) || empty($_POST['txtTelefono']) || empty($_POST['txtEmail']) || empty($_POST['listDpto']) || empty($_POST['listMuni'])) {
                $arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
            } else {
                $idCentro = intval($_POST['idCentro']);
                $strNombre = strClean($_POST['txtNombre']);
                $strTelefono = strClean($_POST['txtTelefono']);
                $strEmail = strClean($_POST['txtEmail']);
                $intDpto = intval($_POST['listDpto']);
                $intMuni = intval($_POST['listMuni']);
                $strDireccion = strClean($_POST['txtDireccion']);
                $intPoblacion = intval($_POST['txtPoblacion']);
                $intStatus = intval($_POST['listStatus']);

                if ($idCentro == 0) {
                    $option = 1;
                    $request_centro = $this->model->insertCentro($strNombre, $strTelefono, $strEmail, $intDpto, $intMuni, $strDireccion, $intPoblacion, $intStatus);
                } else {
                    $option = 2;
                    $request_centro = $this->model->updateCentro($idCentro, $strNombre, $strTelefono, $strEmail, $intDpto, $intMuni, $strDireccion, $intPoblacion, $intStatus);
                }

                if ($request_centro > 0) {
                    if ($option == 1) {
                        $arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
                    } else {
                        $arrResponse = array('status' => true, 'msg' => 'Datos Actualizados correctamente.');
                    }
                } else if ($request_centro == 'exist') {
                    $arrResponse = array('status' => false, 'msg' => '¡Atención! El email ya existe.');
                } else {
                    $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function delCentro()
    {
        if ($_POST) {
            $intIdCentro = intval($_POST['idCentro']);
            $requestDelete = $this->model->deleteCentro($intIdCentro);
            if ($requestDelete) {
                $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el centro');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al eliminar el centro.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function getConfig()
    {
        // Set CORS headers manually for this response
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET");
        header("Content-Type: application/json");

        $jsonFile = "Json/Config.json";
        if (file_exists($jsonFile)) {
            $jsonContent = file_get_contents($jsonFile);
            echo $jsonContent;
        } else {
            echo json_encode(['error' => 'Config file not found']);
        }
        die();
    }
}

