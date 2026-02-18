<?php
session_start();
require __DIR__ . '/../conexao.php';

if (!isset($_SESSION['auth_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id_usuario = $_SESSION['auth_id'];
$titulo = $data['titulo'] ?? '';
$conteudo = $data['conteudo'] ?? '';

if (empty($titulo) || empty($conteudo)) {
    echo json_encode(['success' => false, 'message' => 'Título e conteúdo são obrigatórios']);
    exit;
}

// Inserir anotação no banco de dados
$sql = "INSERT INTO anotacoes (id_usuario, titulo, conteudo, data_criacao) VALUES (?, ?, ?, NOW())";
$stmt = $conexao->prepare($sql);
$stmt->bind_param('iss', $id_usuario, $titulo, $conteudo);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Anotação salva com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar anotação']);
}

$stmt->close();
?>