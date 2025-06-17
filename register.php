<?php
include('assets/includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha_limpa = $_POST['senha'];
    
    if(empty($nome) || empty($email) || empty($senha_limpa)) {
        $erro = "Todos os campos são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "O formato do e-mail é inválido.";
    } elseif (strlen($senha_limpa) < 6) {
        $erro = "A senha deve ter no mínimo 6 caracteres.";
    } else {
        $senha_hashed = password_hash($senha_limpa, PASSWORD_DEFAULT);

        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $erro = "Este e-mail já está registrado.";
        } else {
            $role = 'user'; 
            $stmt_insert = $conn->prepare("INSERT INTO users (nome, email, senha, role) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("ssss", $nome, $email, $senha_hashed, $role);

            if ($stmt_insert->execute()) {
                header("Location: login.php?registered=true");
                exit;
            } else {
                $erro = "Erro ao registrar. Tente novamente.";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alien Cheats - Registrar</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* CSS idêntico ao de login.php para consistência total */
        :root {
            --cor-fundo-secundario: #111;
            --cor-card: #1a1a1a;
            --cor-borda: #333;
            --cor-texto-principal: #fff;
            --cor-texto-secundario: #ccc;
            --fonte-titulo: 'Orbitron', sans-serif;
            --fonte-corpo: 'Roboto', sans-serif;
            --raio-borda: 12px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: var(--fonte-corpo);
            color: var(--cor-texto-principal);
            background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .auth-container {
            width: 100%;
            max-width: 450px;
            padding: 40px;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--raio-borda);
            text-align: center;
            animation: fadeInForm 1s ease-out forwards;
        }
        @keyframes fadeInForm {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .site-logo {
            font-family: var(--fonte-titulo);
            font-size: 38px;
            font-weight: 900;
            color: var(--cor-texto-principal);
            text-decoration: none;
            display: block;
            margin-bottom: 20px;
        }
        .site-logo span { color: #888; }
        .auth-container h1 {
            font-family: var(--fonte-titulo);
            font-size: 32px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }
        .form-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 1px solid var(--cor-borda);
            border-radius: 8px;
            background-color: var(--cor-fundo-secundario);
            color: var(--cor-texto-principal);
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--cor-texto-principal);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
        }
        .submit-btn {
            width: 100%;
            padding: 15px;
            border-radius: 8px;
            border: none;
            background-color: var(--cor-texto-principal);
            color: #000;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .submit-btn:hover {
            background-color: var(--cor-texto-secundario);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        .form-footer { margin-top: 30px; color: var(--cor-texto-secundario); }
        .form-footer a { color: var(--cor-texto-principal); text-decoration: none; font-weight: 600; }
        .form-footer a:hover { text-decoration: underline; }
        
        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .message.error { background-color: rgba(220, 53, 69, 0.15); border: 1px solid #dc3545; color: #f8d7da; }

        .back-to-store {
            display: inline-block;
            margin-top: 30px;
            color: var(--cor-texto-secundario);
            text-decoration: none;
            font-size: 14px;
        }
        .back-to-store:hover { color: var(--cor-texto-principal); }
    </style>
</head>
<body>
    <div class="auth-container">
        <a href="index.php" class="site-logo">Alien<span>Cheats</span></a>
        <h1>Criar Conta</h1>

        <?php if(isset($erro)): ?>
            <div class="message error"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="nome" placeholder="Seu nome completo" required value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
            </div>
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Seu email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="senha" placeholder="Crie uma senha" required>
            </div>
            <button type="submit" class="submit-btn">Registrar</button>
        </form>
        
        <div class="form-footer">
            <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
        </div>
        <a href="index.php" class="back-to-store"><i class="fas fa-arrow-left"></i> Voltar para a loja</a>
    </div>
</body>
</html>