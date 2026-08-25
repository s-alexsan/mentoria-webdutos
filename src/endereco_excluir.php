<?php

require 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use DELETE.']);
    exit;
}

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if ($id === false || $id <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'Informe um ID de endereco valido.']);
    exit;
}

try {
    $pdo = conectar_banco();
    $sql = 'DELETE FROM ENDERECO WHERE ID_ENDERECO = :id_endereco';
    $stm = $pdo->prepare($sql);
    $stm->bindValue(':id_endereco', $id, PDO::PARAM_INT);
    $stm->execute();

    if ($stm->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['erro' => 'Endereco nao encontrado.']);
        exit;
    }

    echo json_encode(['mensagem' => 'Endereco excluido com sucesso.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao excluir endereco',
        'detalhe' => $e->getMessage()
    ]);
}
