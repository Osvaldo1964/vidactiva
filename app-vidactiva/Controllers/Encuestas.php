<?php
class Encuestas extends Controllers
{
    public function __construct()
    {
        // Iniciar sesión
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        parent::__construct();

        // Validar Login
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        // Validar Permisos (Permiso 5 para Encuestas según nav_admin.php)
        getPermisos(5);
    }

    public function encuestas()
    {
        $data['page_tag'] = "Encuestas";
        $data['page_title'] = "GESTIÓN DE ENCUESTAS <small>Sistema Electoral</small>";
        $data['page_name'] = "encuestas";
        $data['page_functions_js'] = "functions_encuestas.js";
        $this->views->getView($this, "encuestas", $data);
    }
}
