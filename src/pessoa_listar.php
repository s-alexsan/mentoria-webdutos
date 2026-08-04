<?php

require 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use GET.']);
    exit;
}

try {
    $pdo = conectar_banco();
    $sql = 'SELECT ID_PESSOA, NOME FROM PESSOA ORDER BY ID_PESSOA';
    $stm = $pdo->prepare($sql);
    $stm->execute();

    $pessoas = $stm->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($pessoas);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao listar pessoas', 'detalhe' => $e->getMessage()]);
}
