<?php
// validar.php
session_start();
require __DIR__ . '/conexao.php';

// Garante que só aceita requisições POST
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ../index.html');
    exit;
}

$nome  = trim($_POST['nome']  ?? '');
$senha = trim($_POST['senha'] ?? '');

// Se algum campo estiver vazio, volta pro login
if ($nome === '' || $senha === '') {
    header('Location: ../index.html');
    exit;
}

// Consulta o usuário no banco
$sql = "SELECT id_logins, nome FROM logins WHERE nome = ? AND senha = ? LIMIT 1";
$stmt = $conexao->prepare($sql);

if (!$stmt) {
    die("Erro na preparação da query: " . $conexao->error);
}

$stmt->bind_param('ss', $nome, $senha);
$stmt->execute();
$stmt->store_result();

// Se não encontrou usuário -> volta pro login
if ($stmt->num_rows === 0) {
    header('Location: ../index.html?erro=1');
    exit;
}

// Login ok -> cria sessão
$stmt->bind_result($id_usuario, $nome_usuario);
$stmt->fetch();

$_SESSION['auth_id'] = $id_usuario;
$_SESSION['auth_nome'] = $nome_usuario;
header('Location: ../perfil.php');
exit;
?>