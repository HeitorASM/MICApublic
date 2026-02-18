<?php
session_start();
require __DIR__ . '/../conexao.php';

if (!isset($_SESSION['auth_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? 0;
$id_usuario = $_SESSION['auth_id'];

// Verificar se a anotação pertence ao usuário antes de excluir
$sql = "DELETE FROM anotacoes WHERE id = ? AND id_usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param('ii', $id, $id_usuario);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Anotação excluída com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Anotação não encontrada ou não pertence ao usuário']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir anotação']);
}

$stmt->close();
?>