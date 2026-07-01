<?php

Class DB
{
    private $host = "db:/firebird/data/agenda.fdb";
    private $user = "SYSDBA";
    private $password = "masterkey";
    

    function conectar()
    {
        $pdo = new PDO("firebird:dbname=$this->host", $this->user, $this->password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (!$pdo) {
            die("Falha na conexão: " . $pdo->errorInfo()[2]);
        }
        return $pdo;
    }
}