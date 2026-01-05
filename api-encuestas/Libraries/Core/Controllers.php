<?php

class Controllers
{
    public $views;
    public $model;
    public function __construct()
    {
        // Global CORS Handling for Preflight requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            die();
        }

        $this->views = new Views();
        $this->loadModel();
    }

    public function loadModel()
    {
        $model = get_class($this) . "Model";
        $routClass = "Models/" . $model . ".php"; // Models/HomeModel.php
        if (file_exists($routClass)) {
            require_once($routClass);
            $this->model = new $model();
        }
    }
}
