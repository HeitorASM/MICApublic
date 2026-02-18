<?php
// cadastro.php
session_start();
require __DIR__ . '/conexao.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ../index.html');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if ($nome === '' || $email === '' || $senha === '') {
    header('Location: ../index.html');
    exit;
}

// Processar upload da imagem
$imagem_pfp = 'default.png';
if (isset($_FILES['imagem_pfp']) && $_FILES['imagem_pfp']['error'] === UPLOAD_ERR_OK) {
    $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
    $extensao = strtolower(pathinfo($_FILES['imagem_pfp']['name'], PATHINFO_EXTENSION));
    
    if (in_array($extensao, $extensoes_permitidas)) {
        $nome_arquivo = uniqid() . '.' . $extensao;
        $caminho_destino = __DIR__ . '/../uploads/' . $nome_arquivo;
        
        if (move_uploaded_file($_FILES['imagem_pfp']['tmp_name'], $caminho_destino)) {
            $imagem_pfp = $nome_arquivo;
        }
    }
}

// Tenta inserir. Se já existir (UNIQUE), só ignora.
$sql = "INSERT INTO logins (nome, email, senha, imagem_pfp) VALUES (?, ?, ?, ?)";
$stmt = $conexao->prepare($sql);
$stmt->bind_param('ssss', $nome, $email, $senha, $imagem_pfp);
$ok = $stmt->execute();

if (!$ok) {
    // Verifica se é erro de duplicata (código 1062)
    if ($stmt->errno == 1062) {
        // Usuário já existe, vamos fazer index
        $sql_login = "SELECT id_logins FROM logins WHERE nome = ? AND senha = ? LIMIT 1";
        $stmt_login = $conexao->prepare($sql_login);
        $stmt_login->bind_param('ss', $nome, $senha);
        $stmt_login->execute();
        $stmt_login->store_result();
        
        if ($stmt_login->num_rows > 0) {
            $stmt_login->bind_result($id_usuario);
            $stmt_login->fetch();
            
            $_SESSION['auth_nome'] = $nome;
            $_SESSION['auth_id'] = $id_usuario;
            header('Location: ../perfil.php');
            exit;
        }
    }
    
    // Se não for erro de duplicata ou não conseguir fazer index
    header('Location: ../index.html?erro=1');
    exit;
}

$_SESSION['auth_nome'] = $nome;
$_SESSION['auth_id'] = $stmt->insert_id;
header('Location: ../perfil.php');
exit;
?>