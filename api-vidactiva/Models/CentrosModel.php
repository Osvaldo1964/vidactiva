<?php

class CentrosModel extends Mysql
{
    private $intIdCentro;
    private $strNombre;
    private $strTelefono;
    private $strEmail;
    private $intDpto;
    private $intMuni;
    private $strDireccion;
    private $intPoblacion;
    private $intStatus;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectCentros()
    {
        $sql = "SELECT * FROM centros WHERE estado_centro != 0";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectCentro(int $idcentro)
    {
        $this->intIdCentro = $idcentro;
        $sql = "SELECT * FROM centros WHERE id_centro = $this->intIdCentro";
        $request = $this->select($sql);
        return $request;
    }

    public function insertCentro(string $nombre, string $telefono, string $email, int $dpto, int $muni, string $direccion, int $poblacion, int $status)
    {
        $this->strNombre = $nombre;
        $this->strTelefono = $telefono;
        $this->strEmail = $email;
        $this->intDpto = $dpto;
        $this->intMuni = $muni;
        $this->strDireccion = $direccion;
        $this->intPoblacion = $poblacion;
        $this->intStatus = $status;

        // Check availability (optional, based on logic)
        $sql = "SELECT * FROM centros WHERE email_centro = '{$this->strEmail}' AND estado_centro != 0";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO centros(nombre_centro, telefono_centro, email_centro, dpto_centro, muni_centro, direccion_centro, poblacion_centro, estado_centro) VALUES(?,?,?,?,?,?,?,?)";
            $arrData = array($this->strNombre, $this->strTelefono, $this->strEmail, $this->intDpto, $this->intMuni, $this->strDireccion, $this->intPoblacion, $this->intStatus);
            $request_insert = $this->insert($query_insert, $arrData);
            return $request_insert;
        } else {
            return "exist";
        }
    }

    public function updateCentro(int $idcentro, string $nombre, string $telefono, string $email, int $dpto, int $muni, string $direccion, int $poblacion, int $status)
    {
        $this->intIdCentro = $idcentro;
        $this->strNombre = $nombre;
        $this->strTelefono = $telefono;
        $this->strEmail = $email;
        $this->intDpto = $dpto;
        $this->intMuni = $muni;
        $this->strDireccion = $direccion;
        $this->intPoblacion = $poblacion;
        $this->intStatus = $status;

        $sql = "SELECT * FROM centros WHERE email_centro = '{$this->strEmail}' AND id_centro != $this->intIdCentro AND estado_centro != 0";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $sql = "UPDATE centros SET nombre_centro = ?, telefono_centro = ?, email_centro = ?, dpto_centro = ?, muni_centro = ?, direccion_centro = ?, poblacion_centro = ?, estado_centro = ? WHERE id_centro = $this->intIdCentro";
            $arrData = array($this->strNombre, $this->strTelefono, $this->strEmail, $this->intDpto, $this->intMuni, $this->strDireccion, $this->intPoblacion, $this->intStatus);
            $request = $this->update($sql, $arrData);
            return $request;
        } else {
            return "exist";
        }
    }

    public function deleteCentro(int $idcentro)
    {
        $this->intIdCentro = $idcentro;
        $sql = "UPDATE centros SET estado_centro = ? WHERE id_centro = $this->intIdCentro";
        $arrData = array(0);
        $request = $this->update($sql, $arrData);
        return $request;
    }
}
