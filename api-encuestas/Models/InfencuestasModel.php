<?php
class InfencuestasModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    // Get Active Surveys for Dropdown
    public function selectEncuestas()
    {
        $sql = "SELECT id_hsurvey as id, name_hsurvey as label FROM hsurveys WHERE status_hsurvey = 'Activo'";
        $request = $this->select_all($sql);
        return $request;
    }

    // Get Questions for Column Headers and Edit Form
    public function selectQuestions(int $idSurvey)
    {
        $sql = "SELECT id_bsurvey, name_bsurvey as question_bsurvey, type_bsurvey, detail_bsurvey as options_bsurvey 
                FROM bsurveys 
                WHERE id_hsurvey_bsurvey = $idSurvey AND status_bsurvey = 'Activo' 
                ORDER BY order_bsurvey ASC, id_bsurvey ASC";
        $request = $this->select_all($sql);
        return $request;
    }

    // Get All Answers for the Survey
    public function selectRespuestas(int $idSurvey)
    {
        // We select key fields to pivot later
        // Added 'id_answer' if needed, but 'sequence_answer' is the grouping key.
        // We need to return enough data to build the row and have an ID for the edit button.
        // Assuming sequence_answer is the unique identifier for a single "submission".
        $sql = "SELECT sequence_answer, id_bsurvey_answer, detail_answer, type_answer, date_created_answer, id_answer
                FROM answers 
                WHERE id_hsurvey_answer = $idSurvey
                ORDER BY sequence_answer ASC, id_bsurvey_answer ASC";
        $request = $this->select_all($sql);
        return $request;
    }

    // Get specific answers for a sequence (frontend edit)
    public function selectRespuestasPorSecuencia(int $idSurvey, int $sequence)
    {
        $sql = "SELECT id_answer, id_bsurvey_answer, detail_answer, type_answer 
                FROM answers 
                WHERE id_hsurvey_answer = $idSurvey AND sequence_answer = $sequence";
        $request = $this->select_all($sql);
        return $request;
    }

    // Update a single answer
    public function updateRespuesta(int $idAnswer, string $detail)
    {
        $sql = "UPDATE answers SET detail_answer = ? WHERE id_answer = $idAnswer";
        $arrData = array($detail);
        $request = $this->update($sql, $arrData);
        return $request;
    }

    // Update answers by Question ID and Sequence
    public function updateRespuestaByPregunta(int $idSurvey, int $sequence, int $idQuestion, string $detail)
    {
        $sql = "UPDATE answers SET detail_answer = ? 
                WHERE id_hsurvey_answer = $idSurvey 
                AND sequence_answer = $sequence 
                AND id_bsurvey_answer = $idQuestion";
        $arrData = array($detail);
        $request = $this->update($sql, $arrData);
        return $request;
    }

    // Delete answers for a specific question (used for refreshing multi-value answers)
    public function deleteRespuestasPorPregunta(int $idSurvey, int $sequence, int $idQuestion)
    {
        $sql = "DELETE FROM answers 
                WHERE id_hsurvey_answer = ? 
                AND sequence_answer = ? 
                AND id_bsurvey_answer = ?";
        $arrData = array($idSurvey, $sequence, $idQuestion);
        $request = $this->delete($sql, $arrData);
        return $request;
    }

    // Insert a new single answer
    public function insertRespuesta(int $idSurvey, int $sequence, int $idQuestion, string $detail, int $type)
    {
        $date = date("Y-m-d"); // Or pass as param if needed
        $sql = "INSERT INTO answers (id_hsurvey_answer, sequence_answer, id_bsurvey_answer, detail_answer, type_answer, date_created_answer) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $arrData = array($idSurvey, $sequence, $idQuestion, $detail, $type, $date);
        $request = $this->insert($sql, $arrData);
        return $request;
    }
    // Delete all answers for a specific person/sequence in a survey
    public function deleteRespuestas(int $idSurvey, int $sequence)
    {
        // Using prepared statements
        $sql = "DELETE FROM answers WHERE id_hsurvey_answer = ? AND sequence_answer = ?";
        $arrData = array($idSurvey, $sequence);
        $request = $this->delete($sql, $arrData);
        return $request;
    }
}
