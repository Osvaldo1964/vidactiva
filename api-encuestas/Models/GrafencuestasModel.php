<?php
class GrafencuestasModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectEncuestas()
    {
        $sql = "SELECT id_hsurvey as id, name_hsurvey as label FROM hsurveys WHERE status_hsurvey = 'Activo'";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectQuestions(int $idSurvey)
    {
        // Select detailed info to maybe filter by type if needed (e.g. skip open text questions for charts? Text questions don't chart well)
        $sql = "SELECT id_bsurvey, name_bsurvey as question_bsurvey, type_bsurvey 
                FROM bsurveys 
                WHERE id_hsurvey_bsurvey = $idSurvey AND status_bsurvey = 'Activo' 
                ORDER BY order_bsurvey ASC, id_bsurvey ASC";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectRespuestaConteo(int $idSurvey, int $idQuestion)
    {
        // Simple aggregation by answer value
        // Note: For open text questions (Type 1), this might result in many unique bars.
        // It's up to the user to choose appropriate charts.
        $sql = "SELECT detail_answer as label, COUNT(*) as cantidad 
                FROM answers 
                WHERE id_hsurvey_answer = $idSurvey AND id_bsurvey_answer = $idQuestion 
                GROUP BY detail_answer 
                ORDER BY cantidad DESC";
        $request = $this->select_all($sql);
        return $request;
    }
}
