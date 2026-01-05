<?php

class EncuestasModel extends Mysql
{
    private $intIdSurvey;
    private $intIdOwner;
    private $strName;
    private $strObs;
    private $strBeginDate;
    private $strEndDate;
    private $strStatus;
    private $strDateCreated;
    private $intIdQuestion;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectEncuestas()
    {
        // Extraer todas las encuestas activas
        $sql = "SELECT id_hsurvey, id_owner_hsurvey, name_hsurvey, obs_hsurvey, begindate_hsurvey, enddate_hsurvey, status_hsurvey, date_created_hsurvey 
                FROM hsurveys 
                WHERE status_hsurvey = 'Activo'";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectEncuesta(int $idSurvey)
    {
        // Buscar una encuesta por ID
        $this->intIdSurvey = $idSurvey;
        $sql = "SELECT id_hsurvey, id_owner_hsurvey, name_hsurvey, obs_hsurvey, begindate_hsurvey, enddate_hsurvey, status_hsurvey, date_created_hsurvey 
                FROM hsurveys 
                WHERE id_hsurvey = $this->intIdSurvey";
        $request = $this->select($sql, array());
        return $request;
    }

    public function insertEncuesta(int $idOwner, string $name, string $obs, string $beginDate, string $endDate)
    {
        $this->intIdOwner = $idOwner;
        $this->strName = $name;
        $this->strObs = $obs;
        $this->strBeginDate = $beginDate;
        $this->strEndDate = $endDate;
        $this->strStatus = 'Activo';
        $this->strDateCreated = date("Y-m-d"); // Fecha actual
        $return = 0;

        // Validar si ya existe una encuesta con el mismo nombre (opcional, pero buena práctica)
        $sql = "SELECT * FROM hsurveys WHERE name_hsurvey = '{$this->strName}'";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO hsurveys(id_owner_hsurvey, name_hsurvey, obs_hsurvey, begindate_hsurvey, enddate_hsurvey, status_hsurvey, date_created_hsurvey) VALUES(?,?,?,?,?,?,?)";
            $arrData = array($this->intIdOwner, $this->strName, $this->strObs, $this->strBeginDate, $this->strEndDate, $this->strStatus, $this->strDateCreated);
            $request_insert = $this->insert($query_insert, $arrData);
            $return = $request_insert;
        } else {
            $return = "exist";
        }
        return $return;
    }

    public function updateEncuesta(int $idSurvey, int $idOwner, string $name, string $obs, string $beginDate, string $endDate)
    {
        $this->intIdSurvey = $idSurvey;
        $this->intIdOwner = $idOwner;
        $this->strName = $name;
        $this->strObs = $obs;
        $this->strBeginDate = $beginDate;
        $this->strEndDate = $endDate;

        // Validar que el nombre no pertenezca a otra encuesta
        $sql = "SELECT * FROM hsurveys WHERE name_hsurvey = '{$this->strName}' AND id_hsurvey != $this->intIdSurvey";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $sql = "UPDATE hsurveys SET id_owner_hsurvey = ?, name_hsurvey = ?, obs_hsurvey = ?, begindate_hsurvey = ?, enddate_hsurvey = ? WHERE id_hsurvey = $this->intIdSurvey";
            $arrData = array($this->intIdOwner, $this->strName, $this->strObs, $this->strBeginDate, $this->strEndDate);
            $request = $this->update($sql, $arrData);
        } else {
            $request = "exist";
        }
        return $request;
    }

    public function deleteEncuesta(int $idSurvey)
    {
        $this->intIdSurvey = $idSurvey;
        // Borrado lógico: status = 'Inactivo'
        $sql = "UPDATE hsurveys SET status_hsurvey = ? WHERE id_hsurvey = $this->intIdSurvey";
        $arrData = array('Inactivo');
        $request = $this->update($sql, $arrData);
        return $request;
    }

    // ----------------------------------------------------------------------------------
    // Métodos para Preguntas (bsurveys)
    // ----------------------------------------------------------------------------------

    // ----------------------------------------------------------------------------------
    // Métodos para Preguntas (bsurveys)
    // ----------------------------------------------------------------------------------

    public function selectQuestionsBySurvey(int $idSurvey)
    {
        // Ajustado a la estructura real: id_hsurvey_bsurvey, name_bsurvey, etc.
        $sql = "SELECT id_bsurvey, id_hsurvey_bsurvey, name_bsurvey as question_bsurvey, type_bsurvey, detail_bsurvey as options_bsurvey 
                FROM bsurveys 
                WHERE id_hsurvey_bsurvey = $idSurvey AND status_bsurvey = 'Activo' 
                ORDER BY order_bsurvey ASC, id_bsurvey ASC";
        $request = $this->select_all($sql);
        return $request;
    }

    public function insertQuestion(int $idSurvey, string $question, int $type, $options)
    {
        $this->intIdSurvey = $idSurvey;
        $strQuestion = $question;
        $intType = $type;
        $jsonOptions = ""; // detail_bsurvey NO permite NULL en el insert según estructura, usaremos string vacío si no hay detalle.

        if (!empty($options)) {
            $jsonOptions = json_encode($options, JSON_UNESCAPED_UNICODE);
        }

        // Campos adicionales requeridos por la tabla bsurveys
        $intOrder = 0; // Por defecto al final, lógica de orden se puede refinar luego
        $strStatus = 'Activo';
        $strDateCreated = date("Y-m-d");

        // Ajustado los nombres de columnas
        $query_insert = "INSERT INTO bsurveys(id_hsurvey_bsurvey, order_bsurvey, name_bsurvey, type_bsurvey, detail_bsurvey, status_bsurvey, date_created_bsurvey) VALUES(?,?,?,?,?,?,?)";
        $arrData = array($this->intIdSurvey, $intOrder, $strQuestion, $intType, $jsonOptions, $strStatus, $strDateCreated);
        $request_insert = $this->insert($query_insert, $arrData);
        return $request_insert;
    }

    public function selectQuestion(int $idQuestion)
    {
        $sql = "SELECT id_bsurvey, id_hsurvey_bsurvey, name_bsurvey as question_bsurvey, type_bsurvey, detail_bsurvey as options_bsurvey 
                FROM bsurveys 
                WHERE id_bsurvey = $idQuestion";
        $request = $this->select($sql, array());
        return $request;
    }

    public function updateQuestion(int $idQuestion, string $question, int $type, $options)
    {
        $this->intIdQuestion = $idQuestion;
        $strQuestion = $question;
        $intType = $type;
        $jsonOptions = "";

        if (!empty($options)) {
            $jsonOptions = json_encode($options, JSON_UNESCAPED_UNICODE);
        }

        $sql = "UPDATE bsurveys SET name_bsurvey = ?, type_bsurvey = ?, detail_bsurvey = ? WHERE id_bsurvey = $this->intIdQuestion";
        $arrData = array($strQuestion, $intType, $jsonOptions);
        $request = $this->update($sql, $arrData);
        return $request;
    }

    public function deleteQuestion(int $idQuestion)
    {
        // Borrado lógico ya que el esquema tiene columna status_bsurvey
        $sql = "UPDATE bsurveys SET status_bsurvey = ? WHERE id_bsurvey = $idQuestion";
        $arrData = array('Inactivo');
        $request = $this->update($sql, $arrData);
        return $request;
    }

    public function updateQuestionOrder(int $idQuestion, int $order)
    {
        $sql = "UPDATE bsurveys SET order_bsurvey = ? WHERE id_bsurvey = $idQuestion";
        $arrData = array($order);
        $request = $this->update($sql, $arrData);
        return $request;
    }
}
