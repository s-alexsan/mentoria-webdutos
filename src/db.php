<?php

function conectar_banco()
{
    $host = "db:/firebird/data/agenda.fdb";
    $user = "SYSDBA";
    $password = "masterkey";

    $pdo = new PDO("firebird:dbname=$host", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
}