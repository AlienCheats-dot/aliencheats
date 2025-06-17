<?php
session_start();
include('assets/includes/db.php');

// 1. Segurança: Se o usuário não estiver logado, redireciona para o login.
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// 2. Pega o ID do usuário da sessão para buscar os dados mais recentes.
$user_id = $_SESSION['usuario']['id'];

// 3. Busca os dados completos do usuário no banco de dados.
$stmt = $conn->prepare("SELECT nome, email, role, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

if (!$user_data) {
    session_destroy();
    header("Location: login.php");
    exit;
}
$stmt->close();

// 4. Formata a data de criação da conta para um formato amigável em português.
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
// Verifica se a data é válida antes de formatar
$data_criacao_formatada = 'Data não disponível';
if (!empty($user_data['created_at'])) {
    $data_criacao_formatada = strftime('%d de %B de %Y', strtotime($user_data['created_at']));
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Meu Perfil - <?php echo htmlspecialchars($user_data['nome']); ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --cor-fundo: #000;
            --cor-fundo-secundario: #111;
            --cor-card: #1a1a1a;
            --cor-borda: #333;
            --cor-texto-principal: #fff;
            --cor-texto-secundario: #ccc;
            --cor-texto-muted: #888;
            --fonte-titulo: 'Orbitron', sans-serif;
            --fonte-corpo: 'Roboto', sans-serif;
            --raio-borda: 12px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            background-color: var(--cor-fundo);
            color: var(--cor-texto-principal);
            font-family: var(--fonte-corpo);
            line-height: 1.6;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        /* Estilos do Header e Footer (Padrão do site) */
        header {
            position: sticky; top: 0; width: 100%; z-index: 1000; padding: 20px 0;
            background-color: rgba(0, 0, 0, 0.7); backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .header-container { display: flex; justify-content: space-between; align-items: center; max-width: 1400px; margin: 0 auto; padding: 0 40px; }
        .logo { font-family: var(--fonte-titulo); font-size: 32px; font-weight: 900; color: var(--cor-texto-principal); text-decoration: none; }
        .logo span { color: var(--cor-texto-muted); }
        .header-actions { display: flex; align-items: center; gap: 25px; }
        .logout-button {
            padding: 10px 25px; border: 1px solid var(--cor-texto-principal); border-radius: 50px; background: transparent;
            color: var(--cor-texto-principal); font-weight: 700; text-decoration: none; transition: 0.3s ease;
            font-size: 14px; display: inline-flex; align-items: center; gap: 8px;
        }
        .logout-button:hover { background-color: var(--cor-texto-principal); color: var(--cor-fundo); }
        footer { background-color: #050505; padding: 60px 0; border-top: 1px solid var(--cor-borda); }
        .footer-bottom { text-align: center; color: var(--cor-texto-muted); font-size: 14px; }
        
        /* Estilos específicos da página de perfil */
        main.profile-page {
            padding: 80px 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: calc(100vh - 200px); /* Garante que o conteúdo não fique colado no rodapé */
        }
        .profile-card {
            width: 100%;
            max-width: 700px;
            background-color: var(--cor-card);
            border: 1px solid var(--cor-borda);
            border-radius: var(--raio-borda);
            padding: 40px;
            animation: fadeInProfile 1s ease-out;
        }
        @keyframes fadeInProfile {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .profile-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 40px;
        }
        .profile-avatar {
            font-size: 80px;
            color: var(--cor-texto-principal);
            margin-bottom: 20px;
        }
        .profile-header h2 {
            font-family: var(--fonte-titulo);
            font-size: 36px;
            font-weight: 700;
            margin: 0;
            color: var(--cor-texto-principal);
        }
        .profile-header .user-role {
            font-size: 14px;
            color: var(--cor-texto-secundario);
            background-color: var(--cor-borda);
            padding: 4px 12px;
            border-radius: 20px;
            margin-top: 10px;
            text-transform: capitalize;
            font-weight: 500;
        }
        
        .profile-details h3 {
            font-family: var(--fonte-titulo);
            font-size: 22px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--cor-borda);
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 10px;
            border-bottom: 1px solid #252525;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 500;
            color: var(--cor-texto-secundario);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .detail-label i {
            width: 20px;
            text-align: center;
        }
        .detail-value {
            font-weight: 500;
            color: var(--cor-texto-principal);
        }

        .profile-actions {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid var(--cor-borda);
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .action-button {
            display: block;
            width: 100%;
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .action-button.primary {
            background-color: var(--cor-texto-principal);
            color: var(--cor-fundo);
        }
        .action-button.primary:hover {
            background-color: var(--cor-texto-secundario);
        }
        .action-button.secondary {
            border: 1px solid var(--cor-borda);
            color: var(--cor-texto-secundario);
        }
        .action-button.secondary:hover {
            border-color: var(--cor-texto-principal);
            color: var(--cor-texto-principal);
        }
    </style>
</head>
<body>

    <header>
      <div class="header-container">
        <a href="index.php" class="logo">ALIEN<span>CHEATS</span></a>
        <div class="header-actions">
          <a href="logout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
      </div>
    </header>

    <main class="profile-page">
        <div class="container">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h2><?php echo htmlspecialchars($user_data['nome']); ?></h2>
                    <span class="user-role"><?php echo htmlspecialchars($user_data['role']); ?></span>
                </div>

                <div class="profile-details">
                    <h3>Detalhes da Conta</h3>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-envelope fa-fw"></i> E-mail</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user_data['email']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-calendar-alt fa-fw"></i> Membro desde</span>
                        <span class="detail-value"><?php echo $data_criacao_formatada; ?></span>
                    </div>
                </div>

                <div class="profile-actions">
                    <?php if ($user_data['role'] === 'admin'): ?>
                        <a href="dashboard.php" class="action-button primary"><i class="fas fa-cogs"></i> Acessar Painel Admin</a>
                    <?php endif; ?>
                    <a href="index.php" class="action-button secondary"><i class="fas fa-store"></i> Voltar para a Loja</a>
                </div>
            </div>
        </div>
    </main>
    
    <footer>
        <div class="container">
             <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> Alien Cheats. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

</body>
</html>