<?php

require 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use POST.']);
    exit;
}

$nome = $_POST['nome'] ?? null;

if (empty($nome)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Nome e obrigatorio']);
    exit;
}

try {
    $pdo = conectar_banco();
    $sql = 'INSERT INTO PESSOA (NOME) VALUES (:nome) RETURNING ID_PESSOA';
    $stm = $pdo->prepare($sql);
    $stm->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stm->execute();

    $novoRegistro = $stm->fetch(PDO::FETCH_ASSOC);

    http_response_code(201);
    echo json_encode($novoRegistro);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao criar pessoa', 'detalhe' => $e->getMessage()]);
}
