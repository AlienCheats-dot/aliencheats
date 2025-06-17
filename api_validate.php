<?php
header('Content-Type: application/json');
include('assets/includes/db.php'); // Seu arquivo de conexão com o DB

// Função para enviar resposta JSON e terminar o script
function send_response($status, $message, $data = []) {
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response('error', 'Método inválido. Use POST.');
}

// Pega os dados enviados pelo cliente
$license_key = $_POST['license_key'] ?? '';
$hwid = $_POST['hwid'] ?? '';

if (empty($license_key) || empty($hwid)) {
    send_response('error', 'Chave de licença ou HWID não fornecidos.');
}

// Procura a chave no banco de dados
$stmt = $conn->prepare("SELECT * FROM `keys` WHERE license_key = ?");
$stmt->bind_param("s", $license_key);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    send_response('error', 'Chave de licença inválida.');
}

$key_data = $result->fetch_assoc();
$stmt->close();

// --- LÓGICA DE VALIDAÇÃO ---

// 1. Verifica o status da chave
if ($key_data['status'] === 'banned') {
    send_response('error', 'Esta chave foi banida.');
}

// 2. Verifica se a chave já expirou (para chaves já usadas)
if ($key_data['status'] === 'expired' || ($key_data['expires_at'] && new DateTime() > new DateTime($key_data['expires_at']))) {
    // Se o status ainda não for 'expired', atualiza no banco
    if ($key_data['status'] !== 'expired') {
        $update_stmt = $conn->prepare("UPDATE `keys` SET status = 'expired' WHERE id = ?");
        $update_stmt->bind_param("i", $key_data['id']);
        $update_stmt->execute();
        $update_stmt->close();
    }
    send_response('error', 'Esta chave expirou.');
}

// 3. Primeira ativação da chave
if ($key_data['status'] === 'active') {
    $expires_at = (new DateTime())
        ->add(new DateInterval("P{$key_data['duration_days']}D"))
        ->format('Y-m-d H:i:s');
    
    $update_stmt = $conn->prepare("UPDATE `keys` SET status = 'used', hwid = ?, activated_at = NOW(), expires_at = ? WHERE id = ?");
    $update_stmt->bind_param("ssi", $hwid, $expires_at, $key_data['id']);
    
    if ($update_stmt->execute()) {
        send_response('success', 'Chave ativada com sucesso!', [
            'product' => $key_data['product_name'],
            'expires_at' => $expires_at
        ]);
    } else {
        send_response('error', 'Falha ao ativar a chave.');
    }
    $update_stmt->close();
}

// 4. Validação de um usuário existente
if ($key_data['status'] === 'used') {
    if ($key_data['hwid'] !== $hwid) {
        send_response('error', 'HWID não corresponde. A chave está em uso em outro computador.');
    } else {
        // Tudo certo, o usuário é válido
        send_response('success', 'Validação bem-sucedida.', [
            'product' => $key_data['product_name'],
            'expires_at' => $key_data['expires_at']
        ]);
    }
}

// Se chegar aqui, algo deu errado
send_response('error', 'Ocorreu um erro desconhecido na validação.');