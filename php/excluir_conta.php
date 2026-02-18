<?php
// excluir_conta.php
session_start();
require __DIR__ . '/conexao.php';

if (!isset($_SESSION['auth_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

$id_usuario = $_SESSION['auth_id'];

// Excluir locais associados ao usuário
$sql_locais = "DELETE FROM locais WHERE id_usuario = ?";
$stmt_locais = $conexao->prepare($sql_locais);
$stmt_locais->bind_param('i', $id_usuario);
$stmt_locais->execute();

// Excluir o usuário
$sql_usuario = "DELETE FROM logins WHERE id_logins = ?";
$stmt_usuario = $conexao->prepare($sql_usuario);
$stmt_usuario->bind_param('i', $id_usuario);

if ($stmt_usuario->execute()) {
    // Encerrar a sessão
    session_unset();
    session_destroy();
    
    echo json_encode(['success' => true, 'message' => 'Conta excluída com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir conta']);
}

exit;
?>