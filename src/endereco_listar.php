<?php

require 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use GET.']);
    exit;
}

$idPessoa = $_GET['id_pessoa'] ?? null;

if ($idPessoa !== null) {
    $idPessoa = filter_var($idPessoa, FILTER_VALIDATE_INT);

    if ($idPessoa === false || $idPessoa <= 0) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID da pessoa invalido.']);
        exit;
    }
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
            INNER JOIN CIDADE C ON C.ID_CIDADE = E.ID_CIDADE';

    if ($idPessoa !== null) {
        $sql .= ' WHERE E.ID_PESSOA = :id_pessoa';
    }

    $sql .= ' ORDER BY E.ID_ENDERECO';
    $stm = $pdo->prepare($sql);

    if ($idPessoa !== null) {
        $stm->bindValue(':id_pessoa', $idPessoa, PDO::PARAM_INT);
    }

    $stm->execute();
    $enderecos = $stm->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($enderecos, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao listar enderecos',
        'detalhe' => $e->getMessage()
    ]);
}
