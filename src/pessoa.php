<?php

header('Content-Type: application/json; charset=utf-8');

$metodo = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

if ($metodo === 'GET' && empty($id)) {
    require 'pessoa_listar.php';
    exit;
}

if ($metodo === 'GET' && !empty($id)) {
    require 'pessoa_buscar.php';
    exit;
}

if ($metodo === 'POST') {
    require 'pessoa_criar.php';
    exit;
}

if ($metodo === 'PUT') {
    require 'pessoa_atualizar.php';
    exit;
}

if ($metodo === 'DELETE') {
    require 'pessoa_excluir.php';
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Metodo nao permitido']);
