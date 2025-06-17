<?php
session_start();
include('assets/includes/db.php');

// Segurança: Apenas admin pode acessar
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
$admin_user = $_SESSION['usuario'];

// --- CONFIGURAÇÃO DE CAMINHOS PARA OS ARQUIVOS JSON ---
// !!! IMPORTANTE: CERTIFIQUE-SE QUE OS CAMINHOS ESTÃO CORRETOS !!!
$path_ticket_bot       = 'C:/Users/pedov/OneDrive/Documentos/BOTS DA ALIEN/BOT - TICKET/';
$path_verification_bot = 'C:/Users/pedov/captcha-verificacao/'; // <-- Ajuste este caminho

// CORREÇÃO: Montando o caminho do arquivo corretamente
$file_active_tickets   = $path_ticket_bot . 'active_tickets.json';
$file_blacklist        = $path_ticket_bot . 'blacklist.json';
$file_verified_users   = $path_verification_bot . 'verified_users.json';
$file_ticket_config    = $path_ticket_bot . 'config.json';


// --- FUNÇÕES AUXILIARES ---
function read_json_file($filepath, $default_value = []) {
    if (!file_exists($filepath)) {
        return ['error' => "Arquivo não encontrado: " . basename($filepath), 'data' => $default_value];
    }
    $content = file_get_contents($filepath);
    if ($content === false) {
        return ['error' => "Não foi possível ler o arquivo: " . basename($filepath), 'data' => $default_value];
    }
    $data = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => "Erro de formatação JSON no arquivo: " . basename($filepath), 'data' => $default_value];
    }
    return ['data' => $data];
}

function write_json_file($filepath, $data) {
    return file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// --- LÓGICA DE GERENCIAMENTO (POST REQUESTS) ---
$feedback_message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = trim($_POST['user_id'] ?? '');

    if (!empty($user_id) && is_numeric($user_id)) {
        $blacklist_data = read_json_file($file_blacklist, ['data' => []]);
        $blacklist = $blacklist_data['data'];

        if ($action === 'blacklist_add') {
            if (!in_array($user_id, $blacklist)) {
                $blacklist[] = (int)$user_id;
                if (write_json_file($file_blacklist, $blacklist)) {
                    $feedback_message = ['type' => 'success', 'text' => "Usuário $user_id adicionado à blacklist."];
                } else {
                    $feedback_message = ['type' => 'error', 'text' => "Erro ao escrever no arquivo de blacklist."];
                }
            } else {
                $feedback_message = ['type' => 'info', 'text' => "Usuário $user_id já estava na blacklist."];
            }
        } elseif ($action === 'blacklist_remove') {
            if (($key = array_search($user_id, $blacklist)) !== false) {
                unset($blacklist[$key]);
                if (write_json_file($file_blacklist, array_values($blacklist))) {
                    $feedback_message = ['type' => 'success', 'text' => "Usuário $user_id removido da blacklist."];
                } else {
                    $feedback_message = ['type' => 'error', 'text' => "Erro ao escrever no arquivo de blacklist."];
                }
            }
        }
    } elseif (isset($_POST['action'])) {
        $feedback_message = ['type' => 'error', 'text' => 'O ID do usuário é inválido ou não foi fornecido.'];
    }
}


// --- LEITURA DE DADOS PARA EXIBIÇÃO ---
$errors = [];
$tickets_result = read_json_file($file_active_tickets);
$tickets_abertos = $tickets_result['data'];
if (isset($tickets_result['error'])) $errors[] = $tickets_result['error'];

$blacklist_result = read_json_file($file_blacklist, []);
$blacklist_users = $blacklist_result['data'];
if (isset($blacklist_result['error'])) $errors[] = $blacklist_result['error'];

$verified_result = read_json_file($file_verified_users, []);
$verified_users = $verified_result['data'];
if (isset($verified_result['error'])) $errors[] = $verified_result['error'];

$ticket_config_result = read_json_file($file_ticket_config);
$ticket_config = $ticket_config_result['data'];
if (isset($ticket_config_result['error'])) $errors[] = $ticket_config_result['error'];


// --- PROCESSAMENTO DE DADOS PARA O FRONTEND ---
$total_tickets_abertos = count($tickets_abertos);
$total_blacklist = count($blacklist_users);
$total_verified = count($verified_users);

$verified_today_count = 0;
if(is_array($verified_users)) {
    $twenty_four_hours_ago = new DateTime('-24 hours');
    foreach ($verified_users as $user) {
        if (isset($user['timestamp'])) {
            try {
                $verification_date = new DateTime($user['timestamp']);
                if ($verification_date > $twenty_four_hours_ago) {
                    $verified_today_count++;
                }
            } catch (Exception $e) { /* Ignora timestamps inválidos */ }
        }
    }
}
$verified_users_recent = is_array($verified_users) ? array_reverse($verified_users) : [];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Alien Cheats</title>
    
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
            --cor-accent: #fff;
            --cor-green: #28a745;
            --cor-red: #dc3545;
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
        }
        
        /* Layout Principal com Header e Sidebar */
        .admin-wrapper {
            display: flex;
        }
        .sidebar {
            width: 260px;
            background-color: var(--cor-fundo-secundario);
            border-right: 1px solid var(--cor-borda);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            padding-top: 80px; /* Espaço para o header global */
        }
        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 120px 40px 40px 40px; /* Topo, Direita, Baixo, Esquerda */
        }
        
        /* Header Global (copiado da index) */
        header {
            position: fixed; top: 0; width: 100%; z-index: 1001; padding: 20px 0;
            background-color: rgba(0, 0, 0, 0.7); backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .header-container { display: flex; justify-content: space-between; align-items: center; max-width: 100%; margin: 0; padding: 0 40px; }
        .logo { font-family: var(--fonte-titulo); font-size: 32px; font-weight: 900; color: var(--cor-texto-principal); text-decoration: none; }
        .logo span { color: var(--cor-texto-muted); }
        .header-actions { display: flex; align-items: center; gap: 25px; }
        .header-actions a { color: var(--cor-texto-principal); text-decoration: none; font-weight: 600; font-size: 16px; transition: color 0.3s; }
        .header-actions a:hover { color: var(--cor-texto-secundario); }

        /* Sidebar Conteúdo */
        .sidebar-user { text-align: center; padding: 20px; border-bottom: 1px solid var(--cor-borda); }
        .sidebar-user .icon { font-size: 40px; margin-bottom: 10px; }
        .sidebar-user h5 { font-size: 18px; margin: 0; color: var(--cor-texto-principal); }
        .sidebar-user span { font-size: 14px; color: var(--cor-texto-muted); }
        .sidebar-nav { list-style: none; padding: 0; margin: 15px 0; flex-grow: 1; }
        .sidebar-nav a { display: flex; align-items: center; padding: 16px 25px; color: var(--cor-texto-secundario); text-decoration: none; font-size: 16px; font-weight: 500; transition: all 0.3s ease; border-left: 3px solid transparent; }
        .sidebar-nav a:hover { background-color: var(--cor-card); color: var(--cor-texto-principal); }
        .sidebar-nav a.active { color: var(--cor-texto-principal); border-left-color: var(--cor-accent); background-color: var(--cor-card); }
        .sidebar-nav a i { margin-right: 15px; width: 20px; text-align: center; }
        
        /* Componentes do Dashboard */
        .page-title { font-family: var(--fonte-titulo); font-size: 42px; margin-bottom: 40px; }
        .content-section { display: none; animation: fadeIn 0.5s ease; }
        .content-section.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 40px; }
        .stat-card {
            background-color: var(--cor-card); padding: 25px; border-radius: var(--raio-borda);
            border: 1px solid var(--cor-borda); display: flex; align-items: center; gap: 20px;
        }
        .stat-card .icon { font-size: 32px; color: var(--cor-accent); }
        .stat-card .info .value { font-size: 32px; font-weight: 700; font-family: var(--fonte-titulo); }
        .stat-card .info .label { font-size: 14px; color: var(--cor-texto-muted); text-transform: uppercase; letter-spacing: 0.5px; }

        .card-table { background-color: var(--cor-card); border-radius: var(--raio-borda); border: 1px solid var(--cor-borda); overflow: hidden; margin-bottom: 30px; }
        .card-header { padding: 20px; background-color: var(--cor-fundo-secundario); border-bottom: 1px solid var(--cor-borda); font-size: 18px; font-weight: 500; }
        .card-body { padding: 20px; }
        
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid var(--cor-borda); }
        thead { background-color: var(--cor-fundo-secundario); }
        tbody tr:hover { background-color: #222; }
        tr:last-child td { border-bottom: none; }
        
        .btn { padding: 8px 15px; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: 600; transition: opacity 0.3s; display: inline-flex; align-items: center; gap: 8px; }
        .btn:hover { opacity: 0.85; }
        .btn-discord { background-color: #5865F2; color: white; }
        .btn-danger { background-color: var(--cor-red); color: white; }

        .form-inline { display: flex; gap: 10px; }
        .form-inline input[type="text"] {
            flex-grow: 1; padding: 12px; background-color: var(--cor-fundo-secundario);
            border: 1px solid var(--cor-borda); border-radius: 8px; color: var(--cor-texto-principal); font-size: 16px;
        }
        .form-inline button {
            background-color: var(--cor-green); color: white; border: none; padding: 0 25px;
            border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 16px;
        }

        .error-box { background-color: rgba(220, 53, 69, 0.1); border: 1px solid var(--cor-red); color: var(--cor-red); padding: 15px; border-radius: var(--raio-borda); margin-bottom: 20px; }
        .error-box ul { margin: 0; padding-left: 20px; }
        
        .toast {
            position: fixed; bottom: 20px; right: 20px; padding: 15px 25px; z-index: 1002;
            border-radius: 8px; color: white; font-size: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.4);
            opacity: 0; transform: translateY(20px); transition: all 0.4s ease;
        }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.success { background-color: var(--cor-green); }
        .toast.error { background-color: var(--cor-red); }
        .toast.info { background-color: #007bff; }
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <a href="index.php" class="logo" title="Ir para a Loja">ALIEN<span>CHEATS</span></a>
        <div class="header-actions">
            <a href="index.php"><i class="fas fa-store"></i> Ver Loja</a>
        </div>
    </div>
</header>

<div class="admin-wrapper">
    <aside class="sidebar">
        <div class="sidebar-user">
            <div class="icon"><i class="fas fa-user-shield"></i></div>
            <h5><?php echo htmlspecialchars($admin_user['nome']); ?></h5>
            <span>Administrador</span>
        </div>
        <nav class="sidebar-nav">
            <a href="#dashboard" class="nav-link active"><i class="fas fa-tachometer-alt fa-fw"></i> Visão Geral</a>
            <a href="#tickets" class="nav-link"><i class="fas fa-ticket-alt fa-fw"></i> Tickets Abertos</a>
            <a href="#verification" class="nav-link"><i class="fas fa-user-check fa-fw"></i> Log de Verificação</a>
            <a href="#blacklist" class="nav-link"><i class="fas fa-user-slash fa-fw"></i> Blacklist</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt fa-fw"></i> Sair</a>
        </nav>
    </aside>

    <main class="main-content">
        <section id="dashboard" class="content-section active">
            <h1 class="page-title">Visão Geral</h1>
            <?php if (!empty($errors)): ?>
                <div class="error-box">
                    <strong><i class="fas fa-exclamation-triangle"></i> Erros de Leitura de Dados:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-ticket-alt"></i></div>
                    <div class="info">
                        <div class="value"><?php echo $total_tickets_abertos; ?></div>
                        <div class="label">Tickets Abertos</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-user-check"></i></div>
                    <div class="info">
                        <div class="value"><?php echo $verified_today_count; ?></div>
                        <div class="label">Verificados (24h)</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-user-slash"></i></div>
                    <div class="info">
                        <div class="value"><?php echo $total_blacklist; ?></div>
                        <div class="label">Usuários na Blacklist</div>
                    </div>
                </div>
                 <div class="stat-card">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <div class="info">
                        <div class="value"><?php echo $total_verified; ?></div>
                        <div class="label">Total de Verificados</div>
                    </div>
                </div>
            </div>
             <div class="card-table">
                <div class="card-header"><i class="fas fa-link"></i> Links Rápidos para o Discord</div>
                <div class="card-body" style="display: flex; gap: 15px; flex-wrap: wrap;">
                     <a href="https://discord.com/channels/1321329830561841183/1353060410886062082" target="_blank" class="btn btn-discord">Logs de Tickets</a>
                     <a href="https://discord.com/channels/1321329830561841183/1370189916973039626" target="_blank" class="btn btn-discord">Logs de Verificação</a>
                </div>
            </div>
        </section>

        <section id="tickets" class="content-section">
            <h1 class="page-title">Tickets Abertos</h1>
            <div class="card-table">
                <div class="card-header">Total: <?php echo $total_tickets_abertos; ?></div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr><th>ID do Canal</th><th>ID do Usuário</th><th>Ação</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tickets_abertos)): ?>
                                <tr><td colspan="3" style="text-align: center; padding: 40px 0;">Nenhum ticket aberto no momento.</td></tr>
                            <?php else: ?>
                                <?php foreach ($tickets_abertos as $channel_id => $ticket_info): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($channel_id); ?></td>
                                    <td><?php echo htmlspecialchars($ticket_info['user_id']); ?></td>
                                    <td>
                                        <a href="https://discord.com/channels/1321329830561841183/<?php echo htmlspecialchars($channel_id); ?>" target="_blank" class="btn btn-discord">
                                            <i class="fab fa-discord"></i> Ver Ticket
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="verification" class="content-section">
            <h1 class="page-title">Log de Verificação</h1>
            <div class="card-table">
                 <div class="card-header">Mostrando os 50 mais recentes</div>
                <div class="table-responsive">
                    <table>
                        <thead><tr><th>Usuário</th><th>ID do Usuário</th><th>Data e Hora</th></tr></thead>
                        <tbody>
                            <?php if (empty($verified_users_recent)): ?>
                                <tr><td colspan="3" style="text-align: center; padding: 40px 0;">Nenhum usuário verificado ainda.</td></tr>
                            <?php else: ?>
                                <?php foreach (array_slice($verified_users_recent, 0, 50) as $verification): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($verification['userTag']); ?></td>
                                    <td><?php echo htmlspecialchars($verification['userId']); ?></td>
                                    <td><?php echo (new DateTime($verification['timestamp']))->format('d/m/Y H:i:s'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="blacklist" class="content-section">
             <h1 class="page-title">Gerenciar Blacklist</h1>
             <div class="card-table">
                <div class="card-header">Adicionar Usuário</div>
                <div class="card-body">
                    <form method="POST" action="dashboard.php#blacklist" class="form-inline">
                        <input type="hidden" name="action" value="blacklist_add">
                        <input type="text" name="user_id" placeholder="Digite o ID do usuário do Discord" required>
                        <button type="submit"><i class="fas fa-plus"></i> Adicionar</button>
                    </form>
                </div>
             </div>
             <div class="card-table">
                <div class="card-header">Usuários na Blacklist (Total: <?php echo $total_blacklist; ?>)</div>
                <div class="table-responsive">
                    <table>
                        <thead><tr><th>ID do Usuário</th><th>Ação</th></tr></thead>
                        <tbody>
                            <?php if (empty($blacklist_users)): ?>
                                <tr><td colspan="2" style="text-align: center; padding: 40px 0;">A blacklist está vazia.</td></tr>
                            <?php else: ?>
                                <?php foreach ($blacklist_users as $user_id): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user_id); ?></td>
                                    <td>
                                        <form method="POST" action="dashboard.php#blacklist" style="display: inline;">
                                            <input type="hidden" name="action" value="blacklist_remove">
                                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Remover</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
             </div>
        </section>
    </main>
</div>
    
<?php if ($feedback_message): ?>
    <div id="feedback-toast" class="toast <?php echo $feedback_message['type']; ?>">
        <?php echo htmlspecialchars($feedback_message['text']); ?>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('.content-section');
    const toast = document.getElementById('feedback-toast');

    function showSection(hash) {
        const targetHash = hash || '#dashboard';
        
        sections.forEach(section => {
            section.classList.remove('active');
            if ('#' + section.id === targetHash) {
                section.classList.add('active');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === targetHash) {
                link.classList.add('active');
            }
        });
    }

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Permite que o link de logout funcione normalmente
            if(this.getAttribute('href') === 'logout.php') return;
            
            e.preventDefault();
            const targetId = this.getAttribute('href');
            history.pushState(null, null, targetId);
            showSection(targetId);
        });
    });

    showSection(window.location.hash);

    if (toast) {
        setTimeout(() => { toast.classList.add('show'); }, 100);
        setTimeout(() => { toast.classList.remove('show'); }, 5000);
    }
});
</script>

</body>
</html>