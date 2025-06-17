<?php
session_start();

if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) {
    header("Location: login.php?redirect_to=comprar&message=login_required");
    exit;
}

$chave_pix_aleatoria = "7ee55f61-2970-4be5-96e2-37034de4f8c4";
$usuario_logado = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Alien Cheats - Finalizar Compra</title>

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Copiando todos os estilos da index.php para manter a consistência */
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
        
        /* HEADER E FOOTER (Estilos da index.php) */
        header {
            position: sticky; top: 0; width: 100%; z-index: 1000; padding: 20px 0;
            background-color: rgba(0, 0, 0, 0.7); backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .header-container { display: flex; justify-content: space-between; align-items: center; max-width: 1400px; margin: 0 auto; padding: 0 40px; }
        .logo { font-family: var(--fonte-titulo); font-size: 32px; font-weight: 900; color: var(--cor-texto-principal); text-decoration: none; }
        .logo span { color: var(--cor-texto-muted); }
        nav ul { display: flex; list-style: none; gap: 40px; }
        nav a { color: var(--cor-texto-principal); text-decoration: none; font-weight: 600; position: relative; padding: 5px 0; transition: color 0.3s ease; }
        nav a::after { content: ''; position: absolute; bottom: -5px; left: 0; width: 0; height: 2px; background-color: var(--cor-texto-principal); transition: width 0.3s ease; }
        nav a:hover, nav a.active { color: var(--cor-texto-secundario); }
        nav a:hover::after, nav a.active::after { width: 100%; }
        .header-actions { display: flex; align-items: center; gap: 25px; }
        .login-button, .logout-button {
            padding: 10px 25px; border: 1px solid var(--cor-texto-principal); border-radius: 50px; background: transparent;
            color: var(--cor-texto-principal); font-weight: 700; text-decoration: none; transition: 0.3s ease;
            font-size: 14px; display: flex; align-items: center; gap: 8px;
        }
        .login-button:hover, .logout-button:hover { background-color: var(--cor-texto-principal); color: var(--cor-fundo); }
        .profile-link { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--cor-texto-secundario); font-weight: 600; transition: color 0.3s ease; }
        .profile-link:hover { color: var(--cor-texto-principal); }
        .cart-icon-container { position: relative; }
        .cart-icon { font-size: 24px; cursor: pointer; color: var(--cor-texto-principal); }
        .cart-count {
            position: absolute; top: -8px; right: -10px; background-color: var(--cor-texto-principal); color: var(--cor-fundo);
            border-radius: 50%; width: 22px; height: 22px; display: flex; justify-content: center; align-items: center;
            font-size: 12px; font-weight: 700;
        }
        footer { background-color: #050505; padding: 80px 0; border-top: 1px solid var(--cor-borda); }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; }
        .footer-col h5 { font-family: var(--fonte-titulo); font-size: 20px; margin-bottom: 20px; }
        .footer-col ul { list-style: none; }
        .footer-col li { margin-bottom: 12px; }
        .footer-col a { color: var(--cor-texto-muted); text-decoration: none; transition: color 0.3s ease; }
        .footer-col a:hover { color: var(--cor-texto-principal); }
        .social-icons a { font-size: 24px; margin-right: 15px; }
        .footer-bottom { text-align: center; margin-top: 80px; padding-top: 40px; border-top: 1px solid var(--cor-borda); color: var(--cor-texto-muted); font-size: 14px; }
        .mobile-menu-toggle { display: none; } /* Escondido em desktop */

        /* ==================================
         ESTILOS ESPECÍFICOS DO CHECKOUT
        ================================== */
        main.checkout-page {
            padding: 80px 0;
        }
        .checkout-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 50px;
            align-items: flex-start;
        }
        .checkout-main {
            display: flex;
            flex-direction: column;
            gap: 40px;
        }
        .checkout-step {
            background-color: var(--cor-card);
            border: 1px solid var(--cor-borda);
            border-radius: var(--raio-borda);
            padding: 30px;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInStep 0.8s ease-out forwards;
        }
        .checkout-step:nth-child(2) { animation-delay: 0.2s; }
        
        @keyframes fadeInStep {
            to { opacity: 1; transform: translateY(0); }
        }

        .step-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--cor-borda);
        }
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--cor-texto-principal);
            color: var(--cor-fundo);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: var(--fonte-titulo);
            font-size: 20px;
            font-weight: 700;
        }
        .step-header h3 {
            font-family: var(--fonte-titulo);
            font-size: 24px;
            margin: 0;
        }

        /* Formulário */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--cor-texto-secundario); }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--cor-borda);
            border-radius: 8px;
            background-color: var(--cor-fundo-secundario);
            color: var(--cor-texto-principal);
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--cor-texto-principal);
        }
        
        /* Sidebar de Resumo */
        .checkout-sidebar {
            position: sticky;
            top: 120px; /* Header height + a little space */
        }
        .order-summary-box {
            background-color: var(--cor-card);
            border: 1px solid var(--cor-borda);
            border-radius: var(--raio-borda);
            padding: 30px;
        }
        .order-summary-box h3 {
            font-family: var(--fonte-titulo);
            font-size: 22px;
            text-align: center;
            margin-bottom: 20px;
        }
        .order-items-list {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
            padding-right: 5px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--cor-borda);
            font-size: 15px;
        }
        .order-item:last-child { border: none; }
        .order-item-name { color: var(--cor-texto-secundario); }
        .order-item-price { font-weight: 700; }
        .order-total {
            display: flex;
            justify-content: space-between;
            font-size: 20px;
            font-weight: 700;
            padding-top: 20px;
            margin-top: 10px;
            border-top: 1px solid var(--cor-texto-principal);
        }

        /* Seção do Pix */
        .pix-details { text-align: center; }
        .pix-details img {
            width: 200px; height: 200px; border: 2px solid white;
            border-radius: var(--raio-borda); margin: 15px auto; padding: 10px; background-color: white;
        }
        .pix-details p { color: var(--cor-texto-secundario); margin-bottom: 15px; }
        .pix-key-wrapper {
            display: flex;
            background-color: var(--cor-fundo-secundario);
            border: 1px solid var(--cor-borda);
            border-radius: 8px;
            margin-top: 10px;
            overflow: hidden;
        }
        .pix-key {
            padding: 12px;
            font-family: monospace;
            word-break: break-all;
            flex-grow: 1;
            text-align: left;
            color: var(--cor-texto-secundario);
        }
        .copy-pix-key-btn {
            background-color: var(--cor-borda);
            border: none;
            color: var(--cor-texto-principal);
            padding: 0 15px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .copy-pix-key-btn:hover { background-color: #444; }

        /* Botão Finalizar Compra */
        .finalize-purchase-btn {
            width: 100%; padding: 16px; border-radius: 8px;
            border: none; background-color: var(--cor-texto-principal);
            color: var(--cor-fundo); font-size: 18px; font-weight: 700;
            cursor: pointer; transition: 0.3s ease;
            margin-top: 10px;
        }
        .finalize-purchase-btn:hover { background-color: var(--cor-texto-secundario); }

        /* Mensagem de Confirmação Final */
        .final-confirmation-container {
            display: none; /* Escondido por padrão */
            text-align: center;
            padding: 100px 20px;
            animation: fadeInStep 1s ease-out;
        }
        .final-confirmation-container .icon {
            font-size: 80px;
            color: #4CAF50; /* Verde sucesso */
            margin-bottom: 30px;
        }
        .final-confirmation-container h2 { font-family: var(--fonte-titulo); font-size: 42px; margin-bottom: 20px; }
        .final-confirmation-container p { color: var(--cor-texto-secundario); max-width: 600px; margin: 0 auto 30px auto; font-size: 18px; }
        .cta-button {
          font-family: var(--fonte-titulo); font-size: 16px; padding: 15px 40px; border-radius: 50px;
          text-decoration: none; background-color: var(--cor-texto-principal); color: var(--cor-fundo);
          font-weight: 700; transition: transform 0.3s ease, box-shadow 0.3s ease; display: inline-block;
        }
        .cta-button:hover { transform: translateY(-3px); }

        /* Notificação (mesmo estilo da index) */
        .custom-notification {
            display: none; position: fixed; top: 90px; left: 50%;
            transform: translateX(-50%); background-color: #222; color: #eee;
            padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            font-size: 1em; font-weight: bold; z-index: 10000; opacity: 0;
            transition: opacity 0.5s ease-in-out, top 0.5s ease-in-out;
            border-left: 5px solid white;
        }
        .custom-notification.success { border-left-color: #4CAF50; }
        .custom-notification.error { border-left-color: #f44336; }

        /* Responsividade */
        @media(max-width: 992px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
            .checkout-sidebar {
                position: static;
                top: 0;
                margin-top: 40px;
            }
        }
        @media(max-width: 768px) {
            .header-container { padding: 0 20px; }
            main.checkout-page { padding: 40px 0; }
        }

    </style>
</head>
<body>

    <header>
      <div class="header-container">
        <a href="index.php" class="logo">ALIEN<span>CHEATS</span></a>
        <div class="header-actions">
          <a href="index.php#produtos" class="profile-link" title="Voltar para a loja">
              <i class="fas fa-store fa-lg"></i>
          </a>
          <div class="cart-icon-container">
            <i class="fas fa-shopping-cart cart-icon"></i>
            <span class="cart-count">0</span>
          </div>
        </div>
      </div>
    </header>

    <main class="checkout-page">
        <div class="container">
            <div class="checkout-grid" id="checkout-grid">
                <div class="checkout-main">
                    <div class="checkout-step">
                        <div class="step-header">
                            <span class="step-number">1</span>
                            <h3>Identificação</h3>
                        </div>
                        <form id="customer-form">
                            <div class="form-group">
                                <label for="nome">Nome Completo</label>
                                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario_logado['nome']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario_logado['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="telefone">Telefone (WhatsApp)</label>
                                <input type="tel" id="telefone" name="telefone" placeholder="(XX) XXXXX-XXXX" value="<?php echo htmlspecialchars($usuario_logado['telefone'] ?? ''); ?>" required>
                            </div>
                        </form>
                    </div>

                    <div class="checkout-step">
                        <div class="step-header">
                            <span class="step-number">2</span>
                            <h3>Pagamento</h3>
                        </div>
                        <div class="pix-details">
                            <p>Para finalizar, escaneie o QR Code abaixo com o app do seu banco ou use a chave Pix.</p>
                            <img src="assets/images/medico.png" alt="QR Code Pix" />
                            <div class="pix-key-wrapper">
                                <span class="pix-key" id="pix-copy-key"><?php echo $chave_pix_aleatoria; ?></span>
                                <button class="copy-pix-key-btn" id="copy-pix-key-btn" title="Copiar Chave Pix"><i class="fas fa-copy"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="checkout-sidebar">
                    <div class="order-summary-box">
                        <h3>Resumo do Pedido</h3>
                        <div class="order-items-list" id="checkout-order-items">
                            </div>
                        <div class="order-total">
                            <span>Total</span>
                            <span>R$ <span id="checkout-total-value">0.00</span></span>
                        </div>
                        <button class="finalize-purchase-btn" id="finalize-purchase-btn">
                            <i class="fas fa-check-circle"></i> Já Paguei, Finalizar
                        </button>
                    </div>
                </div>
            </div>

            <div class="final-confirmation-container" id="final-confirmation-container">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <h2>Pedido Registrado com Sucesso!</h2>
                <p>Obrigado pela sua compra! Para receber seu produto, por favor, abra um ticket em nosso Discord e envie o comprovante de pagamento. Nossa equipe fará a liberação o mais rápido possível.</p>
                <a href="https://discord.gg/tCaBdZSWfe" target="_blank" class="cta-button"><i class="fab fa-discord"></i> Abrir Ticket no Discord</a>
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

    <div id="custom-notification" class="custom-notification"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkoutOrderItems = document.getElementById('checkout-order-items');
        const checkoutTotalValue = document.getElementById('checkout-total-value');
        const finalizePurchaseBtn = document.getElementById('finalize-purchase-btn');
        const copyPixKeyBtn = document.getElementById('copy-pix-key-btn');
        const customNotificationElement = document.getElementById('custom-notification');
        
        const checkoutGrid = document.getElementById('checkout-grid');
        const finalConfirmationContainer = document.getElementById('final-confirmation-container');

        // Função de notificação (mesma da index)
        function showCustomNotification(message, type = 'info', duration = 3000) {
            customNotificationElement.textContent = message;
            customNotificationElement.className = `custom-notification ${type}`;
            customNotificationElement.style.display = 'block';
            setTimeout(() => { customNotificationElement.style.opacity = '1'; customNotificationElement.style.top = '110px'; }, 10);
            setTimeout(() => {
                customNotificationElement.style.opacity = '0';
                customNotificationElement.style.top = '90px';
                setTimeout(() => { customNotificationElement.style.display = 'none'; }, 500);
            }, duration);
        }

        // Carregar e exibir itens do carrinho
        let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

        function displayCheckoutItems() {
            checkoutOrderItems.innerHTML = '';
            let total = 0;

            if (cartItems.length === 0) {
                checkoutOrderItems.innerHTML = '<p style="text-align:center; color: var(--cor-texto-muted);">Seu carrinho está vazio.</p>';
                checkoutTotalValue.textContent = '0.00';
                finalizePurchaseBtn.disabled = true;
                finalizePurchaseBtn.style.opacity = '0.5';
                return;
            }

            cartItems.forEach(item => {
                const orderItemDiv = document.createElement('div');
                orderItemDiv.classList.add('order-item');
                orderItemDiv.innerHTML = `
                    <span class="order-item-name">${item.name}</span>
                    <span class="order-item-price">R$ ${item.price.toFixed(2).replace('.', ',')}</span>
                `;
                checkoutOrderItems.appendChild(orderItemDiv);
                total += item.price;
            });

            checkoutTotalValue.textContent = total.toFixed(2).replace('.', ',');
            finalizePurchaseBtn.disabled = false;
        }

        // Evento para finalizar a compra
        finalizePurchaseBtn.addEventListener('click', function(event) {
            event.preventDefault();
            
            const nome = document.getElementById('nome').value.trim();
            const email = document.getElementById('email').value.trim();
            const telefone = document.getElementById('telefone').value.trim();
            
            if (!nome || !email || !telefone) {
                showCustomNotification("Por favor, preencha todas as suas informações!", 'error');
                return;
            }
            
            // Esconde a área de checkout e mostra a mensagem final
            checkoutGrid.style.display = 'none';
            finalConfirmationContainer.style.display = 'block';
            
            // Rola a página para o topo para ver a mensagem
            window.scrollTo(0, 0);

            // Limpa o carrinho
            localStorage.removeItem('cartItems');
            document.querySelector('.cart-count').textContent = '0';
        });

        // Evento para copiar a chave Pix
        copyPixKeyBtn.addEventListener('click', function() {
            const pixKey = document.getElementById('pix-copy-key').textContent;
            navigator.clipboard.writeText(pixKey).then(() => {
                showCustomNotification('Chave Pix copiada!', 'success');
            }).catch(err => {
                showCustomNotification('Erro ao copiar a chave.', 'error');
            });
        });
        
        // --- INICIALIZAÇÃO ---
        displayCheckoutItems();
        // Atualiza o contador de carrinho no header
        document.querySelector('.cart-count').textContent = cartItems.length;

        // Adiciona um listener no ícone do carrinho no header para voltar para a loja
        document.querySelector('.cart-icon').addEventListener('click', () => {
            window.location.href = 'index.php';
        });

    });
    </script>
</body>
</html>