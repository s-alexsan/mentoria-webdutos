<?php

require 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use POST.']);
    exit;
}

$idPessoa = filter_var($_POST['id_pessoa'] ?? null, FILTER_VALIDATE_INT);
$idCidade = filter_var($_POST['id_cidade'] ?? null, FILTER_VALIDATE_INT);
$logradouro = trim($_POST['logradouro'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');

if ($idPessoa === false || $idPessoa <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID da pessoa e obrigatorio.']);
    exit;
}

if ($idCidade === false || $idCidade <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID da cidade e obrigatorio.']);
    exit;
}

if ($logradouro === '') {
    http_response_code(400);
    echo json_encode(['erro' => 'Endereco e obrigatorio.']);
    exit;
}

try {
    $pdo = conectar_banco();

    $stm = $pdo->prepare('SELECT ID_PESSOA FROM PESSOA WHERE ID_PESSOA = :id_pessoa');
    $stm->bindValue(':id_pessoa', $idPessoa, PDO::PARAM_INT);
    $stm->execute();

    if (!$stm->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Pessoa nao encontrada.']);
        exit;
    }

    $stm = $pdo->prepare('SELECT ID_CIDADE FROM CIDADE WHERE ID_CIDADE = :id_cidade');
    $stm->bindValue(':id_cidade', $idCidade, PDO::PARAM_INT);
    $stm->execute();

    if (!$stm->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Cidade nao encontrada.']);
        exit;
    }

    $sql = 'INSERT INTO ENDERECO
                (ID_PESSOA, ID_CIDADE, LOGRADOURO, TELEFONE)
            VALUES
                (:id_pessoa, :id_cidade, :logradouro, :telefone)
            RETURNING ID_ENDERECO';
    $stm = $pdo->prepare($sql);
    $stm->bindValue(':id_pessoa', $idPessoa, PDO::PARAM_INT);
    $stm->bindValue(':id_cidade', $idCidade, PDO::PARAM_INT);
    $stm->bindValue(':logradouro', $logradouro, PDO::PARAM_STR);
    $stm->bindValue(
        ':telefone',
        $telefone !== '' ? $telefone : null,
        $telefone !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL
    );
    $stm->execute();

    $novoRegistro = $stm->fetch(PDO::FETCH_ASSOC);

    http_response_code(201);
    echo json_encode($novoRegistro, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao criar endereco',
        'detalhe' => $e->getMessage()
    ]);
}
