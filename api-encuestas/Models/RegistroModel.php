<?php
class RegistroModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectEncuestas()
    {
        // Select surveys that are active and within date range
        $today = date("Y-m-d");
        $sql = "SELECT id_hsurvey, name_hsurvey 
                FROM hsurveys 
                WHERE status_hsurvey = 'Activo' 
                AND '$today' BETWEEN begindate_hsurvey AND enddate_hsurvey";
        $request = $this->select_all($sql);
        return $request;
    }

    public function getNextSequence(int $idSurvey)
    {
        $sql = "SELECT MAX(sequence_answer) as max_seq FROM answers WHERE id_hsurvey_answer = $idSurvey";
        $request = $this->select($sql, array()); // select single row
        if (empty($request) || empty($request['max_seq'])) {
            return 1;
        }
        return $request['max_seq'] + 1;
    }

    public function insertAnswer(int $idHsurvey, int $sequence, int $idBsurvey, int $type, string $detail)
    {
        $dateCreated = date("Y-m-d");
        $sql = "INSERT INTO answers(id_hsurvey_answer, sequence_answer, id_bsurvey_answer, type_answer, detail_answer, date_created_answer) 
                VALUES(?, ?, ?, ?, ?, ?)";
        $arrData = array($idHsurvey, $sequence, $idBsurvey, $type, $detail, $dateCreated);
        $request = $this->insert($sql, $arrData);
        return $request;
    }
}
