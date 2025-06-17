<?php
session_start();
include('assets/includes/db.php'); // Seu arquivo de conexão com o DB

// Segurança: Apenas admins podem acessar
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$feedback = '';

// --- LÓGICA PARA GERAR UMA NOVA CHAVE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_key'])) {
    $product_name = trim($_POST['product_name']);
    $duration_days = intval($_POST['duration_days']);

    if (!empty($product_name) && $duration_days > 0) {
        // Gera uma chave única no formato XXXX-XXXX-XXXX-XXXX
        $key_part1 = strtoupper(bin2hex(random_bytes(2)));
        $key_part2 = strtoupper(bin2hex(random_bytes(2)));
        $key_part3 = strtoupper(bin2hex(random_bytes(2)));
        $key_part4 = strtoupper(bin2hex(random_bytes(2)));
        $new_key = "$key_part1-$key_part2-$key_part3-$key_part4";

        $stmt = $conn->prepare("INSERT INTO `keys` (license_key, product_name, duration_days) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $new_key, $product_name, $duration_days);
        
        if ($stmt->execute()) {
            $feedback = "✅ Chave '$new_key' gerada com sucesso para o produto '$product_name'!";
        } else {
            $feedback = "❌ Erro ao gerar a chave.";
        }
        $stmt->close();
    } else {
        $feedback = "❌ Por favor, preencha todos os campos corretamente.";
    }
}

// --- LÓGICA PARA DELETAR UMA CHAVE ---
if (isset($_GET['delete_key'])) {
    $key_id = intval($_GET['delete_key']);
    $stmt = $conn->prepare("DELETE FROM `keys` WHERE id = ?");
    $stmt->bind_param("i", $key_id);
    if ($stmt->execute()) {
        $feedback = "✅ Chave deletada com sucesso.";
    } else {
        $feedback = "❌ Erro ao deletar a chave.";
    }
    $stmt->close();
    // Redireciona para limpar a URL
    header("Location: admin_keys.php");
    exit;
}


// --- BUSCAR TODAS AS CHAVES PARA EXIBIÇÃO ---
$result = $conn->query("SELECT * FROM `keys` ORDER BY created_at DESC");
$keys = [];
if ($result) {
    $keys = $result->fetch_all(MYSQLI_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Chaves - Admin</title>
    <style>
        /* Estilos básicos para o painel */
        body { font-family: sans-serif; background: #121212; color: #fff; padding: 20px; }
        .container { max-width: 1200px; margin: auto; }
        .card { background: #1e1e1e; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        h1, h2 { text-align: center; color: #fff; }
        form { display: flex; gap: 10px; margin-bottom: 20px; }
        input, button { padding: 10px; border-radius: 5px; border: 1px solid #444; background: #333; color: #fff; }
        button { cursor: pointer; background: #007bff; border: none; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid #333; text-align: left; }
        th { background: #2a2a2a; }
        .feedback { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .feedback.success { background: #28a745; }
        .feedback.error { background: #dc3545; }
        .status { padding: 5px 10px; border-radius: 15px; font-weight: bold; font-size: 0.9em; }
        .status.active { background-color: #007bff; color: white; }
        .status.used { background-color: #28a745; color: white; }
        .status.expired { background-color: #ffc107; color: black; }
        .status.banned { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gerenciador de Chaves</h1>

        <?php if ($feedback): ?>
            <div class="feedback <?php echo strpos($feedback, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($feedback); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Gerar Nova Chave</h2>
            <form method="POST">
                <input type="text" name="product_name" placeholder="Nome do Produto (ex: CheatFF)" required>
                <input type="number" name="duration_days" placeholder="Duração (em dias)" required>
                <button type="submit" name="generate_key">Gerar Chave</button>
            </form>
        </div>

        <div class="card">
            <h2>Chaves Existentes</h2>
            <table>
                <thead>
                    <tr>
                        <th>Chave de Licença</th>
                        <th>Produto</th>
                        <th>Status</th>
                        <th>Expira em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($keys as $key): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($key['license_key']); ?></td>
                        <td><?php echo htmlspecialchars($key['product_name']); ?></td>
                        <td><span class="status <?php echo $key['status']; ?>"><?php echo ucfirst($key['status']); ?></span></td>
                        <td><?php echo $key['expires_at'] ? date('d/m/Y H:i', strtotime($key['expires_at'])) : 'Nunca'; ?></td>
                        <td>
                            <a href="?delete_key=<?php echo $key['id']; ?>" onclick="return confirm('Tem certeza que deseja deletar esta chave?');">Deletar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>