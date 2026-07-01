<?php

require 'db.php';


Class Pessoa 
{
    private PDO $pdo;

    public function __construct(PDO $pdo) 
    {
        $this->pdo = $pdo;
    }

    public function getAll() 
    {
        $stm = $this->pdo->prepare('SELECT ID_PESSOA, NOME FROM PESSOA');
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserId(int $id) 
    {
        $stm = $this->pdo->prepare('SELECT ID_PESSOA, NOME FROM PESSOA WHERE ID_PESSOA = :id');
        $stm->bindParam(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    public function create(string $nome) 
    {
        $stm = $this->pdo->prepare('INSERT INTO PESSOA (NOME) VALUES (:nome) RETURNING ID_PESSOA');
        $stm->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stm->execute();
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    public function delete(int $id) 
    {
        $stm = $this->pdo->prepare('DELETE FROM PESSOA WHERE ID_PESSOA = :id');
        $stm->bindParam(':id', $id, PDO::PARAM_INT);
        return $stm->execute();
    }

    public function update(int $id, string $nome) {
        $stm = $this->pdo->prepare('UPDATE PESSOA SET NOME = :nome WHERE ID_PESSOA = :id');
        $stm->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stm->bindParam(':id', $id, PDO::PARAM_INT);
        return $stm->execute();
    }

}


$db = new DB();
$pdo = $db->conectar();
$pessoa = new Pessoa($pdo);

$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // inserir dados
    $nome = $_POST['nome'] ?? null;
    if (!empty($nome)) {
        $result = $pessoa->create($nome);
        http_response_code(201);
        echo json_encode($result);
        exit;
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Nome é obrigatório']);
        exit;
    }
} else if($_SERVER['REQUEST_METHOD'] === 'DELETE') { // deletar dados
    if (!empty($id)) {
        $result = $pessoa->delete($id);
        if ($result) {
            http_response_code(202);
            echo json_encode(['message' => 'Pessoa deletada com sucesso']);
            exit;
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Pessoa não encontrada']);
            exit;
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'ID é obrigatório para deletar']);
        exit;
    }
} else if($_SERVER['REQUEST_METHOD'] === 'GET') { // buscar dados
    
    if(empty($id)) {
        $result = $pessoa->getAll();
    } else {
        $result = $pessoa->getUserId($id);
    }

    if(empty($result)) {
        http_response_code(404);
        echo json_encode(['error' => 'Pessoa não encontrada']);
        exit;
    }

    echo json_encode($result);
    exit;
} else if($_SERVER['REQUEST_METHOD'] === 'PUT') { // atualizar dados
    // $nome = $_PUT['nome'] ?? null;
    $_PUT = json_decode(file_get_contents('php://input'), true);
    
    $nome = $_PUT['nome'];

    if(!empty($id)) {
        $result = $pessoa->update($id, $nome);
        if($result) {
            http_response_code(200);
            echo json_encode(['message' => 'Atualizado com sucesso']);
            exit;
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Erro ao atualizar']);
            exit;
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Id é obrigatório']);
        exit;
    }

} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}
