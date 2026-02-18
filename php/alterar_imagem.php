<?php
// alterar_imagem.php
session_start();
require __DIR__ . '/conexao.php';

if (!isset($_SESSION['auth_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['imagem_pfp'])) {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida']);
    exit;
}

$id_usuario = $_SESSION['auth_id'];
$extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
$extensao = strtolower(pathinfo($_FILES['imagem_pfp']['name'], PATHINFO_EXTENSION));

if (!in_array($extensao, $extensoes_permitidas)) {
    echo json_encode(['success' => false, 'message' => 'Formato de arquivo não permitido']);
    exit;
}

// Verificar se a pasta uploads existe, se não, criar
$upload_dir = __DIR__ . '/../uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Gerar nome único para o arquivo
$nome_arquivo = uniqid() . '.' . $extensao;
$caminho_destino = $upload_dir . $nome_arquivo;

if (move_uploaded_file($_FILES['imagem_pfp']['tmp_name'], $caminho_destino)) {
    // Primeiro, buscar a imagem atual para excluí-la se não for a padrão
    $sql_select = "SELECT imagem_pfp FROM logins WHERE id_logins = ?";
    $stmt_select = $conexao->prepare($sql_select);
    $stmt_select->bind_param('i', $id_usuario);
    $stmt_select->execute();
    $stmt_select->bind_result($imagem_atual);
    $stmt_select->fetch();
    $stmt_select->close();
    
    // Excluir a imagem antiga se não for a padrão
    if ($imagem_atual !== 'default.png' && file_exists($upload_dir . $imagem_atual)) {
        unlink($upload_dir . $imagem_atual);
    }
    
    // Atualizar no banco de dados
    $sql = "UPDATE logins SET imagem_pfp = ? WHERE id_logins = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('si', $nome_arquivo, $id_usuario);
    
    if ($stmt->execute()) {
        // Atualizar a sessão se necessário
        $_SESSION['auth_imagem'] = $nome_arquivo;
        
        echo json_encode(['success' => true, 'nova_imagem' => $nome_arquivo]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar banco de dados']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao fazer upload']);
}
exit;
?>