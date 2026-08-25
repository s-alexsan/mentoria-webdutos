<?php

require 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use GET.']);
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
    $sql = 'SELECT E.ID_ENDERECO,
                   E.ID_PESSOA,
                   E.ID_CIDADE,
                   E.LOGRADOURO,
                   E.TELEFONE,
                   P.NOME AS NOME_PESSOA,
                   C.NOME AS NOME_CIDADE,
                   C.UF
            FROM ENDERECO E
            INNER JOIN PESSOA P ON P.ID_PESSOA = E.ID_PESSOA
            INNER JOIN CIDADE C ON C.ID_CIDADE = E.ID_CIDADE
            WHERE E.ID_ENDERECO = :id_endereco';
    $stm = $pdo->prepare($sql);
    $stm->bindValue(':id_endereco', $id, PDO::PARAM_INT);
    $stm->execute();

    $endereco = $stm->fetch(PDO::FETCH_ASSOC);

    if (!$endereco) {
        http_response_code(404);
        echo json_encode(['erro' => 'Endereco nao encontrado.']);
        exit;
    }

    echo json_encode($endereco, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao buscar endereco',
        'detalhe' => $e->getMessage()
    ]);
}
