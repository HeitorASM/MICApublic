<?php
// perfil.php
session_start();
require __DIR__ . '/php/conexao.php';

if (!isset($_SESSION['auth_id'])) {
  header('Location: index.html');
  exit;
}

$id_usuario = $_SESSION['auth_id'];
$nome = $_SESSION['auth_nome'];

// Buscar informações adicionais do usuário
$sql_user = "SELECT email, data_cadastro, imagem_pfp FROM logins WHERE id_logins = ?";
$stmt_user = $conexao->prepare($sql_user);
$stmt_user->bind_param('i', $id_usuario);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_info = $result_user->fetch_assoc();

$email = $user_info['email'] ?? '';
$data_cadastro = $user_info['data_cadastro'] ?? '';
$imagem_pfp = $user_info['imagem_pfp'] ?? 'default.png';

// Formatar data de cadastro
if ($data_cadastro) {
    $data_formatada = date('M/Y', strtotime($data_cadastro));
} else {
    $data_formatada = 'Jan/2023';
}

// Buscar anotações do usuário
$anotacoes = [];
$sql_anotacoes = "SELECT id, titulo, conteudo, data_criacao FROM anotacoes WHERE id_usuario = ? ORDER BY data_criacao DESC";
$stmt_anotacoes = $conexao->prepare($sql_anotacoes);
if ($stmt_anotacoes) {
    $stmt_anotacoes->bind_param('i', $id_usuario);
    $stmt_anotacoes->execute();
    $result_anotacoes = $stmt_anotacoes->get_result();
    while ($row = $result_anotacoes->fetch_assoc()) {
        $anotacoes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - MICA</title>
    <link rel="icon" type="image/png" href="uploads/LOGO MICA.png">
    <link rel="stylesheet" href="style/perfil/perfil.css">
    <link rel="stylesheet" href="style/perfil/adicional_perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
</head>
<body>
    <header>
        <div class="logo-placeholder">
            <h1 class="logo"><i class="fas fa-hand-holding-heart"></i> MICA</h1>
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <nav id="navMenu">
            <ul>
                <ul>
        <li><a href="home.html"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="sobre.html"><i class="fa fa-address-card" aria-hidden="true"></i> Sobre</a></li>
        <li><a href="chatbot.html"><i class="fas fa-robot"></i> Assistente MICA</a></li>
        <li><a href="perfil.php"><i class="fa fa-user" aria-hidden="true"></i> Perfil</a></li>
        <li><a href="configuraçoes.php"><i class="fa fa-cog"></i> Configurações</a></li>
        <li><a class= danger href="php/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Sair</a></li>
                </ul>
            </ul>
        </nav>
    </header>

    <main class="perfil-container">
        <section class="perfil-header">
            <div class="avatar">
                <img src="uploads/<?php echo htmlspecialchars($imagem_pfp); ?>" alt="Avatar" id="avatarImage">
                <button class="edit-avatar" onclick="document.getElementById('uploadImagem').click()">
                    <i class="fas fa-camera"></i>
                </button>
                <form id="uploadForm" action="php/alterar_imagem.php" method="POST" enctype="multipart/form-data" style="display: none;">
                    <input type="file" id="uploadImagem" name="imagem_pfp" accept="image/*">
                </form>
            </div>

            <div class="perfil-info">
                <h1 id="nome-usuario"><?php echo htmlspecialchars($nome); ?></h1>
                <p id="email-usuario"><?php echo htmlspecialchars($email); ?></p>
                <div class="status-conta">
                    <span class="membro-desde">Membro desde: <span id="data-cadastro"><?php echo $data_formatada; ?></span></span>
                </div>
            </div>
        </section>

        <section class="perfil-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-sticky-note"></i>
                </div>
                <div class="stat-info">
                    <h3 id="total-anotacoes"><?php echo count($anotacoes); ?></h3>
                    <p>Anotações salvas</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>1</h3>
                    <p>Dias consecutivos</p>
                </div>
            </div>
           
        </section>

        <section class="perfil-content">
            <div class="content-tabs">
                <button class="tab-btn active" data-tab="anotacoes">Anotações</button>
                <button class="tab-btn" data-tab="atividade">Atividade</button>
                <button class="tab-btn" data-tab="configuracoes">Configurações</button>
            </div>

            <div class="tab-content" id="anotacoes-tab">
                <h2>Minhas Anotações</h2>
                
                <div class="nova-anotacao">
                    <input type="text" id="titulo-anotacao" placeholder="Título da anotação">
                    <textarea id="conteudo-anotacao" placeholder="Escreva sua anotação aqui..."></textarea>
                    <button class="settings-btn" onclick="salvarAnotacao()">Salvar Anotação</button>
                </div>
                
                <div class="anotacoes-lista">
                    <?php if (count($anotacoes) > 0): ?>
                        <?php foreach ($anotacoes as $anotacao): ?>
                            <div class="anotacao-item">
                                <h3><?php echo htmlspecialchars($anotacao['titulo']); ?></h3>
                                <p><?php echo nl2br(htmlspecialchars($anotacao['conteudo'])); ?></p>
                                <div class="anotacao-acoes">
                                    <span class="activity-time">Criada em: <?php echo date('d/m/Y H:i', strtotime($anotacao['data_criacao'])); ?></span>
                                    <button class="settings-btn btn-danger" onclick="excluirAnotacao(<?php echo $anotacao['id']; ?>)">Excluir</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Você ainda não tem anotações salvas.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-content hidden" id="atividade-tab">
                <h2>Minha Atividade</h2>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <div class="activity-details">
                        <h3>Login realizado</h3>
                        <p>Você acessou sua conta</p>
                        <span class="activity-time">Hoje às <?php echo date('H:i'); ?></span>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-sticky-note"></i>
                    </div>
                    <div class="activity-details">
                        <h3>Anotação criada</h3>
                        <p>Nova anotação adicionada</p>
                        <span class="activity-time">Ontem às 15:30</span>
                    </div>
                </div>
            </div>

            <div class="tab-content hidden" id="configuracoes-tab">
                <h2>Configurações da Conta</h2>
                <div class="settings-option">
                    <h3>Informações Pessoais</h3>
                    <p>Gerencie suas informações pessoais</p>
                    <button class="settings-btn">Editar</button>
                </div>
                <div class="settings-option">
                    <h3>Privacidade</h3>
                    <p>Controle suas configurações de privacidade</p>
                    <button class="settings-btn">Gerenciar</button>
                </div>
                <div class="settings-option">
                    <h3>Excluir Conta</h3>
                    <p>Exclua permanentemente sua conta e todos os dados</p>
                    <button class="settings-btn btn-danger" onclick="excluirConta()">Excluir Conta</button>
                </div>
            </div>
        </section>
    </main>

    <script>
        // Menu toggle para mobile
        document.getElementById('menuToggle').addEventListener('click', function() {
            const navMenu = document.getElementById('navMenu');
            navMenu.classList.toggle('active');
        });

        // Sistema de abas
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', () => {
                // Remover classe active de todos os botões
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Adicionar classe active ao botão clicado
                button.classList.add('active');
                
                // Esconder todos os conteúdos de abas
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Mostrar o conteúdo da aba correspondente
                const tabId = button.getAttribute('data-tab');
                document.getElementById(tabId + '-tab').classList.remove('hidden');
            });
        });

        // Upload de imagem
        document.getElementById('uploadImagem').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                // Verificar tamanho do arquivo (máximo 5MB)
                if (this.files[0].size > 5 * 1024 * 1024) {
                    alert('A imagem deve ter no máximo 5MB');
                    this.value = ''; // Limpa o input
                    return;
                }
                
                const formData = new FormData();
                formData.append('imagem_pfp', this.files[0]);
                
                fetch('php/alterar_imagem.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualizar a imagem exibida com timestamp para evitar cache
                        document.getElementById('avatarImage').src = 'uploads/' + data.nova_imagem + '?t=' + new Date().getTime();
                        alert('Imagem alterada com sucesso!');
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao enviar a imagem');
                })
                .finally(() => {
                    // Limpa o input file após o uso
                    this.value = '';
                });
            }
        });

        // Funções para anotações
        async function salvarAnotacao() {
            const titulo = document.getElementById('titulo-anotacao').value;
            const conteudo = document.getElementById('conteudo-anotacao').value;
            
            if (!titulo || !conteudo) {
                alert('Por favor, preencha o título e o conteúdo da anotação.');
                return;
            }
            
            try {
                const response = await fetch('php/salvar_anotacao.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ titulo, conteudo })
                });
                
                const resultado = await response.json();
                
                if (resultado.success) {
                    // Recarregar a página para mostrar a nova anotação
                    location.reload();
                } else {
                    alert('Erro ao salvar anotação: ' + resultado.message);
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao conectar com o servidor');
            }
        }
        
        async function excluirAnotacao(id) {
            if (!confirm('Tem certeza que deseja excluir esta anotação?')) {
                return;
            }
            
            try {
                const response = await fetch('php/excluir_anotacao.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id })
                });
                
                const resultado = await response.json();
                
                if (resultado.success) {
                    // Recarregar a página para atualizar a lista
                    location.reload();
                } else {
                    alert('Erro ao excluir anotação: ' + resultado.message);
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao conectar com o servidor');
            }
        }
        
        // Função para excluir conta
        async function excluirConta() {
            if (!confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita e todos os seus dados serão perdidos.')) {
                return;
            }
            
            try {
                const response = await fetch('php/excluir_conta.php', {
                    method: 'POST'
                });
                
                const resultado = await response.json();
                
                if (resultado.success) {
                    alert('Conta excluída com sucesso. Você será redirecionado para a página inicial.');
                    window.location.href = 'index.html';
                } else {
                    alert('Erro ao excluir conta: ' + resultado.message);
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao conectar com o servidor');
            }
        }
    </script>
</body>
</html>