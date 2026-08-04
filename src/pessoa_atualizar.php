<?php

require 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use PUT.']);
    exit;
}

$id = $_GET['id'] ?? null;
$dados = json_decode(file_get_contents('php://input'), true);
$nome = $dados['nome'] ?? null;

if (empty($id)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Informe o parametro id']);
    exit;
}

if (empty($nome)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Nome e obrigatorio']);
    exit;
}

try {
    $pdo = conectar_banco();
    $sql = 'UPDATE PESSOA SET NOME = :nome WHERE ID_PESSOA = :id';
    $stm = $pdo->prepare($sql);
    $stm->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stm->bindParam(':id', $id, PDO::PARAM_INT);
    $stm->execute();

    if ($stm->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['erro' => 'Pessoa nao encontrada']);
        exit;
    }

    echo json_encode(['mensagem' => 'Pessoa atualizada com sucesso']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao atualizar pessoa', 'detalhe' => $e->getMessage()]);
}
