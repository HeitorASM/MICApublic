<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="uploads/LOGO MICA.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Configurações | MICA </title>
    <link rel="stylesheet" href="style/configuraçoes/configuraçoes.css">
</head>
<body>
    <?php
    session_start();
    require 'php/conexao.php';
    
    if (!isset($_SESSION['auth_id'])) {
        header('Location: index.html');
        exit;
    }
    
    $id_usuario = $_SESSION['auth_id'];
    $sql = "SELECT nome, email, telefone, localizacao FROM logins WHERE id_logins = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('i', $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    
    $nome = $usuario['nome'] ?? '';
    $email = $usuario['email'] ?? '';
    $telefone = $usuario['telefone'] ?? '';
    $localizacao = $usuario['localizacao'] ?? '';
    ?>
    
    <header>
        <div class="logo-placeholder">
            <h1>MICA</h1>
        </div>
        <nav>
            <ul>
                <li><a href="home.html"><i class="fa fa-home"></i> Home</a></li>
                <li><a href="sobre.html"><i class="fa fa-address-card" aria-hidden="true"></i> Sobre</a></li>
                <li><a href="chatbot.html" class="active"><i class="fas fa-robot"></i> Assistente MICA</a></li>
                <li><a href="perfil.php"><i class="fa fa-user" aria-hidden="true"></i> Perfil</a></li>
                <li><a href="configuraçoes.php"><i class="fa fa-cog"></i> Configurações</a></li>
                <li><a class="danger" href="php/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Sair</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Configurações da Conta</h2>
        <p class="subtitle">Deseja mudar alguma informação?</p>
        
        <div class="card">
            <h3>Informações Pessoais</h3>
            <form id="userSettingsForm" action="php/atualizar_perfil.php" method="POST">
                <div class="form-group">
                    <label for="name">Nome</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($nome); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Telefone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($telefone); ?>" placeholder="(00) 00000-0000">
                </div>
                
                <div class="form-actions">
                    <button type="button" id="cancelBtn" class="secondary-btn">Cancelar</button>
                    <button type="submit" class="primary-btn">Salvar Alterações</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h3>Alterar Senha</h3>
            <p class="sub-description">Deseja mudar a senha?</p>
            <form id="passwordForm" action="php/alterar_senha.php" method="POST">
                <div class="form-group">
                    <label for="currentPassword">Senha Atual</label>
                    <input type="password" id="currentPassword" name="currentPassword" required>
                </div>
                
                <div class="form-group">
                    <label for="newPassword">Nova Senha</label>
                    <input type="password" id="newPassword" name="newPassword" required>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirmar Nova Senha</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="secondary-btn">Cancelar</button>
                    <button type="submit" class="primary-btn">Salvar Nova Senha</button>
                </div>
            </form>
        </div>

                </div>
            </div>
        </div>

        <div class="danger-zone">
            <h3><i class="fas fa-exclamation-triangle"></i> Zona de Perigo</h3>
            <p>Ações nesta seção são irreversíveis. Por favor, proceda com cautela.</p>
            
            <div class="form-actions">
                <button type="button" id="deleteDataBtn" class="apagarmeusdados"><i class="fas fa-trash-alt"></i> Apagar meus Dados</button>
                <button type="button" id="logoutBtn" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Fazer Logout</button>
            </div>
        </div>
    </main>
    <script src="js/configuracoes.js"></script>
</body>
</html>