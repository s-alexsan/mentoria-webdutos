<?php

require 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use PUT.']);
    exit;
}

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$dados = json_decode(file_get_contents('php://input'), true);

if (!is_array($dados)) {
    $dados = $_POST;
}

$idPessoa = filter_var($dados['id_pessoa'] ?? null, FILTER_VALIDATE_INT);
$idCidade = filter_var($dados['id_cidade'] ?? null, FILTER_VALIDATE_INT);
$logradouro = trim($dados['logradouro'] ?? '');
$telefone = trim($dados['telefone'] ?? '');

if ($id === false || $id <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'Informe um ID de endereco valido.']);
    exit;
}

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

    $stm = $pdo->prepare('SELECT ID_ENDERECO FROM ENDERECO WHERE ID_ENDERECO = :id_endereco');
    $stm->bindValue(':id_endereco', $id, PDO::PARAM_INT);
    $stm->execute();

    if (!$stm->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(404);
        echo json_encode(['erro' => 'Endereco nao encontrado.']);
        exit;
    }

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

    $sql = 'UPDATE ENDERECO
            SET ID_PESSOA = :id_pessoa,
                ID_CIDADE = :id_cidade,
                LOGRADOURO = :logradouro,
                TELEFONE = :telefone
            WHERE ID_ENDERECO = :id_endereco';
    $stm = $pdo->prepare($sql);
    $stm->bindValue(':id_pessoa', $idPessoa, PDO::PARAM_INT);
    $stm->bindValue(':id_cidade', $idCidade, PDO::PARAM_INT);
    $stm->bindValue(':logradouro', $logradouro, PDO::PARAM_STR);
    $stm->bindValue(
        ':telefone',
        $telefone !== '' ? $telefone : null,
        $telefone !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL
    );
    $stm->bindValue(':id_endereco', $id, PDO::PARAM_INT);
    $stm->execute();

    echo json_encode(['mensagem' => 'Endereco atualizado com sucesso.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao atualizar endereco',
        'detalhe' => $e->getMessage()
    ]);
}
