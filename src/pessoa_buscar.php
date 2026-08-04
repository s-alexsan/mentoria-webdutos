<?php

require 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use GET.']);
    exit;
}

$id = $_GET['id'] ?? null;

if (empty($id)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Informe o parametro id']);
    exit;
}

try {
    $pdo = conectar_banco();
    $sql = 'SELECT ID_PESSOA, NOME FROM PESSOA WHERE ID_PESSOA = :id';
    $stm = $pdo->prepare($sql);
    $stm->bindParam(':id', $id, PDO::PARAM_INT);
    $stm->execute();

    $pessoa = $stm->fetch(PDO::FETCH_ASSOC);

    if (!$pessoa) {
        http_response_code(404);
        echo json_encode(['erro' => 'Pessoa nao encontrada']);
        exit;
    }

    echo json_encode($pessoa);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar pessoa', 'detalhe' => $e->getMessage()]);
}
