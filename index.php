<?php
session_start();
$is_logged_in = isset($_SESSION['usuario']) && is_array($_SESSION['usuario']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Alien Cheats - Loja</title>

  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* ==================================
DESIGN SYSTEM E ESTILOS GLOBAIS
==================================
*/
:root {
    --cor-fundo: #000;
    --cor-fundo-secundario: #111;
    --cor-card: #1a1a1a;
    --cor-borda: #333;
    --cor-texto-principal: #eee;
    --cor-texto-secundario: #bbb;
    --cor-texto-muted: #777;
    --fonte-titulo: 'Orbitron', sans-serif;
    --fonte-corpo: 'Roboto', sans-serif;
    --raio-borda: 12px;
    --transicao-rapida: 0.2s ease-in-out;
    --transicao-suave: 0.3s ease-in-out;
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
.section { padding: 100px 0; overflow: hidden; }
.section-content {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity var(--transicao-suave), transform var(--transicao-suave);
}
.section-content.visible {
    opacity: 1;
    transform: translateY(0);
}
.section-title {
    font-family: var(--fonte-titulo);
    font-size: 42px;
    text-align: center;
    margin-bottom: 15px;
    color: var(--cor-texto-principal);
}
.section-subtitle {
    text-align: center;
    font-size: 16px;
    color: var(--cor-texto-secundario);
    max-width: 700px;
    margin: 0 auto 60px auto;
}

/* ==================================
CABEÇALHO (HEADER) - EFEITO DE VIDRO REFINADO
==================================
*/
header {
    position: sticky; top: 0; width: 100%; z-index: 1000; padding: 15px 0;
    transition: background-color var(--transicao-rapida);
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}
.header-container {
    display: flex; justify-content: space-between; align-items: center;
    max-width: 1400px; margin: 0 auto; padding: 0 30px;
}
.logo {
    font-family: var(--fonte-titulo); font-size: 28px; font-weight: 900;
    color: var(--cor-texto-principal); text-decoration: none;
}
.logo span { color: var(--cor-texto-muted); }
nav ul {
    display: flex; list-style: none; gap: 30px;
}
nav a {
    color: var(--cor-texto-principal); text-decoration: none; font-weight: 500;
    position: relative; padding: 8px 0; transition: color var(--transicao-rapida);
}
nav a::after {
    content: ''; position: absolute; bottom: -3px; left: 0; width: 0; height: 2px;
    background-color: var(--cor-texto-principal); transition: width var(--transicao-rapida);
}
nav a:hover, nav a.active { color: var(--cor-texto-secundario); }
nav a:hover::after, nav a.active::after { width: 100%; }
.header-actions { display: flex; align-items: center; gap: 20px; }
.login-button, .logout-button {
    padding: 8px 20px; border: 1px solid var(--cor-texto-principal); border-radius: 50px;
    background: transparent; color: var(--cor-texto-principal); font-weight: 600;
    text-decoration: none; transition: all var(--transicao-rapida); font-size: 14px;
    display: flex; align-items: center; gap: 6px;
}
.login-button:hover, .logout-button:hover {
    background-color: var(--cor-texto-principal); color: var(--cor-fundo);
}
.profile-link {
    display: flex; align-items: center; gap: 8px; text-decoration: none;
    color: var(--cor-texto-secundario); font-weight: 500; transition: color var(--transicao-rapida);
}
.profile-link:hover { color: var(--cor-texto-principal); }
.cart-icon-container { position: relative; }
.cart-icon { font-size: 20px; cursor: pointer; color: var(--cor-texto-principal); }
.cart-count {
    position: absolute; top: -6px; right: -8px; background-color: var(--cor-texto-principal);
    color: var(--cor-fundo); border-radius: 50%; width: 18px; height: 18px;
    display: flex; justify-content: center; align-items: center; font-size: 10px; font-weight: 700;
}
.mobile-menu-toggle {
    display: none; font-size: 24px; background: none; border: none;
    color: var(--cor-texto-principal); cursor: pointer;
}

/* ==================================
SEÇÃO HERO - DESTAQUE NO TÍTULO
==================================
*/
.hero {
    min-height: 70vh; display: flex; align-items: center; text-align: center;
    padding: 80px 0;
    background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
    overflow: hidden; position: relative;
}
.hero-content { z-index: 2; animation: fadeInHero 1.2s ease-out forwards; }
@keyframes fadeInHero {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.hero h2 {
    font-family: var(--fonte-titulo); font-size: 60px; font-weight: 900;
    margin-bottom: 15px; letter-spacing: 1px;
    background: linear-gradient(45deg, #eee, #ccc);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}
.hero p { font-size: 18px; color: var(--cor-texto-secundario); margin-bottom: 40px; }
.cta-button {
    font-family: var(--fonte-titulo); font-size: 16px; padding: 15px 40px;
    border-radius: 50px; text-decoration: none;
    background-color: var(--cor-texto-principal); color: var(--cor-fundo);
    font-weight: 700; transition: transform var(--transicao-rapida), box-shadow var(--transicao-rapida);
    display: inline-block; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}
.cta-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}

/* ==================================
SEÇÕES: DIFERENCIAIS, AVALIAÇÕES, SOBRE, CONTATO
==================================
*/
#diferenciais, #sobre, #contato { background-color: var(--cor-fundo-secundario); }
.features-grid, .testimonials-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
}
.feature-item, .testimonial-card {
    background-color: var(--cor-card); padding: 30px; border-radius: var(--raio-borda);
    border: 1px solid var(--cor-borda); transition: transform var(--transicao-rapida), border-color var(--transicao-rapida);
}
.feature-item:hover, .testimonial-card:hover {
    transform: translateY(-5px);
    border-color: var(--cor-texto-principal);
}
.feature-item .icon { font-size: 36px; margin-bottom: 15px; color: var(--cor-texto-principal); }
.feature-item h4 { font-family: var(--fonte-titulo); font-size: 20px; margin-bottom: 10px; }
.feature-item p, .testimonial-card p { color: var(--cor-texto-secundario); font-size: 15px; }
.testimonial-card p { font-style: italic; margin-bottom: 15px; }
.testimonial-author { display: flex; align-items: center; gap: 10px; }
.testimonial-author img { width: 40px; height: 40px; border-radius: 50%; }
.testimonial-author span { font-weight: 600; font-size: 14px; }
.about-content { display: flex; gap: 40px; align-items: center; text-align: left; }
.about-content img { max-width: 350px; border-radius: var(--raio-borda); }

/* ==================================
SEÇÃO DE PRODUTOS - CARDS MODERNIZADOS
==================================
*/
.produtos-categorias {
    display: flex; justify-content: center; gap: 15px; margin-bottom: 40px;
    flex-wrap: wrap;
}
.produtos-categorias a {
    color: var(--cor-texto-secundario); text-decoration: none; font-weight: 600;
    padding: 10px 25px; border: 1px solid var(--cor-borda); border-radius: 50px;
    transition: all var(--transicao-rapida); font-size: 14px;
}
.produtos-categorias a:hover {
    background-color: var(--cor-borda); color: var(--cor-texto-principal);
}
.produtos-categorias a.active {
    background-color: var(--cor-texto-principal); color: var(--cor-fundo);
    border-color: var(--cor-texto-principal);
}
.produtos-grupo {
    display: none;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    animation: fadeIn 0.4s ease-out forwards;
}
.produtos-grupo.active { display: grid; }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    background: var(--cor-card); border-radius: var(--raio-borda);
    padding: 20px; display: flex; flex-direction: column; text-align: center;
    overflow: hidden; position: relative; border: 1px solid var(--cor-borda);
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}
.card:hover {
    transform: translateY(-8px) rotateZ(-1deg);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3), 0 0 10px rgba(255, 255, 255, 0.05);
}
.card-image {
    width: 100%; height: 180px; border-radius: calc(var(--raio-borda) - 5px);
    overflow: hidden; margin-bottom: 15px;
}
.card-image img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform 0.3s ease-in-out;
}
.card:hover .card-image img { transform: scale(1.03); }
.card h4 { font-family: var(--fonte-titulo); font-size: 22px; margin-bottom: 10px; color: var(--cor-texto-principal); }
.card .description { color: var(--cor-texto-secundario); font-size: 14px; margin-bottom: 20px; flex-grow: 1; padding: 0 5px; }
.card .price-container { margin-top: auto; width: 100%; }
.card select.product-option {
    width: 100%; padding: 10px; border-radius: 6px;
    border: 1px solid var(--cor-borda);
    background-color: var(--cor-fundo-secundario);
    color: var(--cor-texto-principal); font-size: 14px; margin-bottom: 10px;
    -webkit-appearance: none; appearance: none;
    background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%204%205%22%3E%3Cpath%20fill%3D%22%23ffffff%22%20d%3D%22M2%200L0%202h4zm0%205L0%203h4z%22%2F%3E%3C%2Fsvg%3E');
    background-repeat: no-repeat; background-position: right 8px center; background-size: 8px;
}
.card .buy-button {
    width: 100%; padding: 12px; border-radius: 6px; border: none;
    background-color: var(--cor-texto-principal); color: var(--cor-fundo);
    font-size: 14px; font-weight: 700; cursor: pointer;
    transition: background-color var(--transicao-rapida);
}
.card .buy-button:hover { background-color: var(--cor-texto-secundario); }
.stock-info-message { font-size: 12px; font-weight: 500; margin-top: 10px; }
.stock-info-message.out-of-stock { color: #f44336; }

/* ==================================
RODAPÉ - MAIS ESCURO E CLEAN
==================================
*/
footer {
    background-color: #080808; padding: 60px 0;
    border-top: 1px solid var(--cor-borda);
}
.footer-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 30px;
}
.footer-col h5 { font-family: var(--fonte-titulo); font-size: 18px; margin-bottom: 15px; color: var(--cor-texto-principal); }
.footer-col ul { list-style: none; }
.footer-col li { margin-bottom: 10px; }
.footer-col a { color: var(--cor-texto-muted); text-decoration: none; transition: color var(--transicao-rapida); font-size: 14px; }
.footer-col a:hover { color: var(--cor-texto-principal); }
.social-icons a { font-size: 20px; margin-right: 10px; color: var(--cor-texto-muted); transition: color var(--transicao-rapida); }
.social-icons a:hover { color: var(--cor-texto-principal); }
.footer-bottom {
    text-align: center; margin-top: 60px; padding-top: 30px;
    border-top: 1px solid var(--cor-borda); color: var(--cor-texto-muted); font-size: 12px;
}

/* ==================================
MODAL DO CARRINHO (Ajustes visuais)
==================================
*/
.cart-modal-overlay {
    display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background-color: rgba(0, 0, 0, 0.7); backdrop-filter: blur(6px);
    z-index: 1001; justify-content: center; align-items: center;
    animation: fadeInOverlay var(--transicao-rapida);
}
.cart-modal-overlay.active { display: flex; }
@keyframes fadeInOverlay { from { opacity: 0; } to { opacity: 1; } }
.cart-modal {
    background-color: var(--cor-card); border: 1px solid var(--cor-borda);
    border-radius: var(--raio-borda); padding: 25px; width: 90%; max-width: 550px;
    max-height: 80vh; overflow-y: auto; box-shadow: 0 0 30px rgba(0, 0, 0, 0.6);
    position: relative; animation: slideInModal 0.3s ease-out;
}
@keyframes slideInModal {
    from { transform: translateY(-30px) scale(0.9); opacity: 0; }
    to { transform: translateY(0) scale(1); opacity: 1; }
}
.cart-modal h4 {
    font-family: var(--fonte-titulo); font-size: 28px; margin-bottom: 20px;
    text-align: center; color: var(--cor-texto-principal);
}
.cart-items-list { margin-bottom: 15px; max-height: 350px; overflow-y: auto; padding-right: 5px; }
.cart-item {
    display: flex; justify-content: space-between; align-items: center;
    background-color: #222; border: 1px solid #444; border-radius: 6px;
    padding: 12px; margin-bottom: 8px; font-size: 14px; color: var(--cor-texto-secundario);
}
.cart-item-info { flex-grow: 1; }
.cart-item-name { font-weight: 600; margin-bottom: 3px; color: var(--cor-texto-principal); }
.cart-item-price { font-weight: 400; }
.cart-item-remove {
    background: none; border: none; color: var(--cor-texto-secundario);
    font-size: 18px; cursor: pointer; margin-left: 10px; transition: color var(--transicao-rapida);
}
.cart-item-remove:hover { color: #f44336; }
.cart-total {
    text-align: right; font-size: 18px; font-weight: 700;
    margin-top: 20px; padding-top: 10px; border-top: 1px solid #444; color: var(--cor-texto-principal);
}
.cart-actions { display: flex; justify-content: space-between; margin-top: 20px; gap: 10px; }
.cart-actions button {
    padding: 10px 20px; border: 1px solid var(--cor-texto-secundario); border-radius: 30px;
    background: transparent; color: var(--cor-texto-secundario); font-weight: 600; cursor: pointer;
    transition: all var(--transicao-rapida); text-transform: uppercase; font-size: 12px; flex-grow: 1;
}
.cart-actions button:hover { background-color: var(--cor-texto-secundario); color: var(--cor-fundo); }
.cart-actions .checkout-btn { background-color: var(--cor-texto-principal); color: var(--cor-fundo); border-color: var(--cor-texto-principal); }
.cart-actions .checkout-btn:hover { background-color: var(--cor-texto-secundario); }
.empty-cart-message { text-align: center; font-size: 16px; color: var(--cor-texto-muted); padding: 40px 0; }
.close-modal-btn {
    position: absolute; top: 10px; right: 15px; background: none; border: none;
    color: var(--cor-texto-secundario); font-size: 24px; cursor: pointer; transition: color var(--transicao-rapida);
}
.close-modal-btn:hover { color: var(--cor-texto-muted); }
.custom-notification {
    display: none; position: fixed; top: 70px; left: 50%;
    transform: translateX(-50%); background-color: #222; color: #eee;
    padding: 12px 20px; border-radius: 6px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    font-size: 0.9em; font-weight: bold; z-index: 10000; opacity: 0;
    transition: opacity 0.4s ease-in-out, top 0.4s ease-in-out;
    border-left: 4px solid white;
}
.custom-notification.success { border-left-color: #4CAF50; }
.custom-notification.error { border-left-color: #f44336; }

/* ==================================
RESPONSIVIDADE - Ajustes finos
==================================
*/
@media (max-width: 992px) {
    nav {
        display: none; /* Esconde a navegação principal */
    }
    .mobile-menu-toggle {
        display: block; /* Mostra o botão hamburguer */
    }
    .header-container { padding: 0 20px; }
    .hero h2 { font-size: 48px; }
    .hero p { font-size: 16px; }
    .about-content { flex-direction: column; text-align: center; }
}

@media (max-width: 768px) {
    .section { padding: 80px 0; }
    .section-title { font-size: 32px; }
    .section-subtitle { font-size: 14px; }
    .hero h2 { font-size: 36px; }
    .logo { font-size: 24px; }
    nav {
        position: fixed; top: 0; right: -100%; width: 250px; height: 100vh;
        background-color: #050505; display: flex; flex-direction: column;
        justify-content: center; align-items: center; transition: right 0.3s ease-in-out;
        box-shadow: -5px 0 15px rgba(0,0,0,0.7);
    }
    nav.active { right: 0; }
    nav ul { flex-direction: column; text-align: center; }
    nav ul li { margin: 10px 0; }
    nav a { font-size: 16px; }
    .close-nav-btn {
        display: block; position: absolute; top: 20px; right: 25px;
        font-size: 28px; background: none; border: none; color: var(--cor-texto-principal);
    }
}
</style>
</head>

<body>
<header>
  <div class="header-container">
    <a href="#" class="logo">ALIEN<span>CHEATS</span></a>
    
    <nav>
        <button class="mobile-menu-toggle close-nav-btn" style="position: absolute; top: 30px; right: 30px;">
            <i class="fas fa-times"></i>
        </button>
      <ul>
        <li><a href="#home" class="active">Home</a></li>
        <li><a href="#diferenciais">Diferenciais</a></li>
        <li><a href="#produtos">Produtos</a></li>
        <li><a href="#avaliacoes">Avaliações</a></li>
        <li><a href="#contato">Contato</a></li>
      </ul>
    </nav>

    <div class="header-actions">
      <?php if ($is_logged_in): 
        $nome_usuario = htmlspecialchars($_SESSION['usuario']['nome']);
        $profile_link_url = ($_SESSION['usuario']['role'] === 'admin') ? 'dashboard.php' : 'perfil.php';
      ?>
        <a href="<?php echo $profile_link_url; ?>" class="profile-link" title="Ver Perfil">
            <i class="fas fa-user-circle fa-lg"></i>
        </a>
        <a href="logout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i></a>
      <?php else: ?>
        <a href="login.php" class="login-button">
          <i class="fas fa-sign-in-alt"></i> Login
        </a>
      <?php endif; ?>
      
      <div class="cart-icon-container">
        <i class="fas fa-shopping-cart cart-icon"></i>
        <span class="cart-count">0</span>
      </div>

      <button class="mobile-menu-toggle open-nav-btn">
          <i class="fas fa-bars"></i>
      </button>
    </div>
  </div>
</header>

<main>
    <section class="hero" id="home">
      <div class="container hero-content">
        <h2>DOMINE O JOGO</h2>
        <p>A Vantagem Competitiva que Você Procura, com Segurança e Performance.</p>
        <a href="#produtos" class="cta-button">Ver Produtos</a>
      </div>
    </section>
    
    <section class="section" id="diferenciais">
      <div class="container section-content">
        <h3 class="section-title">Nossos Diferenciais</h3>
        <p class="section-subtitle">Entenda por que a Alien Cheats é a escolha número um dos jogadores que buscam excelência.</p>
        <div class="features-grid">
          <div class="feature-item">
            <div class="icon"><i class="fas fa-shield-alt"></i></div>
            <h4>Segurança Avançada</h4>
            <p>Nossos produtos são desenvolvidos com múltiplas camadas de proteção, garantindo o menor risco de detecção e a maior segurança para sua conta.</p>
          </div>
          <div class="feature-item">
            <div class="icon"><i class="fas fa-headset"></i></div>
            <h4>Suporte 24/7</h4>
            <p>Nossa equipe de suporte está disponível a qualquer hora via Discord para te auxiliar com instalações, configurações e qualquer dúvida que surgir.</p>
          </div>
          <div class="feature-item">
            <div class="icon"><i class="fas fa-rocket"></i></div>
            <h4>Performance Otimizada</h4>
            <p>Focamos em otimização para garantir que nossos cheats não impactem o desempenho do seu jogo, proporcionando uma experiência fluida e sem lag.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section" id="produtos">
      <div class="container section-content">
          <h3 class="section-title">Nossos Produtos</h3>
          <p class="section-subtitle">Soluções de alta performance para os jogos mais populares. Escolha sua plataforma e domine.</p>
          
          <div class="produtos-categorias">
            <a href="#" data-category="freefire" class="active">Free Fire</a>
          </div>
          
          <div class="produtos-container">
              <div class="produtos-grupo active" id="produtos-freefire">
                  <div class="card" data-product-id="alien-legit-planos">
                      <div class="card-image"><img src="assets/images/ALIEN_LEGIT.png" alt="Alien Legit"></div>
                      <h4>ALIEN LEGIT</h4>
                      <p class="description">O cheat mais seguro e indetectável para uma experiência de jogo autêntica e vitoriosa.</p>
                      <div class="price-container">
                          <form>
                              <select class="product-option" data-product-name="ALIEN LEGIT">
                                  <option value="9.90" data-plan-name="3 Dias" data-stock="4">3 Dias - R$9,90 (estoque: 4)</option>
                                  <option value="19.99" data-plan-name="7 Dias" data-stock="5">7 Dias - R$19,99 (estoque: 5)</option>
                                  <option value="37.90" data-plan-name="31 Dias" data-stock="5">31 Dias - R$37,90 (estoque: 5)</option>
                                  <option value="60.00" data-plan-name="91 Dias" data-stock="5">91 Dias - R$60,00 (estoque: 5)</option>
                              </select>
                              <button type="button" class="buy-button add-to-cart-btn"><i class="fas fa-shopping-cart"></i> Adicionar ao Carrinho</button>
                          </form>
                      </div>
                  </div>
                  <div class="card" data-product-id="alien-external-planos">
                      <div class="card-image"><img src="assets/images/ALIEN_EXTERNAL1.png" alt="Alien External"></div>
                      <h4>ALIEN EXTERNAL</h4>
                      <p class="description">A solução externa mais poderosa para performance máxima e controle total no jogo.</p>
                      <div class="price-container">
                          <form>
                              <select class="product-option" data-product-name="ALIEN EXTERNAL">
                                  <option value="14.90" data-plan-name="3 Dias" data-stock="0">3 Dias (Esgotado)</option>
                                  <option value="29.90" data-plan-name="7 Dias" data-stock="0">7 Dias (Esgotado)</option>
                                  <option value="74.90" data-plan-name="31 Dias" data-stock="0">31 Dias (Esgotado)</option>
                                  <option value="179.90" data-plan-name="91 Dias" data-stock="0">91 Dias (Esgotado)</option>
                              </select>
                              <button type="button" class="buy-button add-to-cart-btn"><i class="fas fa-shopping-cart"></i> Adicionar ao Carrinho</button>
                          </form>
                      </div>
                  </div>
              </div>
              <div class="produtos-grupo" id="produtos-fivem">...</div>
              <div class="produtos-grupo" id="produtos-android">...</div>
              <div class="produtos-grupo" id="produtos-ios">...</div>
          </div>
      </div>
    </section>

    <section class="section" id="avaliacoes">
        <div class="container section-content">
            <h3 class="section-title">O que nossos clientes dizem</h3>
            <p class="section-subtitle">A satisfação de quem usa nossos produtos é a nossa maior conquista.(FAKE)
            </p>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <p>"O suporte é incrível, me ajudaram na instalação em poucos minutos. O produto é super seguro, recomendo demais!"</p>
                    <div class="testimonial-author">
                        <img src="https://i.pravatar.cc/50?u=a042581f4e29026704d" alt="Avatar">
                        <span>Luan gameplayz</span>
                    </div>
                </div>
                <div class="testimonial-card">
                    <p>"Finalmente um cheat que não derruba meu FPS. A performance é otimizada de verdade. Vale cada centavo."</p>
                    <div class="testimonial-author">
                        <img src="https://i.pravatar.cc/50?u=a042581f4e29026702d" alt="Avatar">
                        <span>Mariana_x7</span>
                    </div>
                </div>
                <div class="testimonial-card">
                    <p>"Estava com receio, mas a segurança é o ponto forte. Jogo há meses sem nenhum problema. A Alien Cheats ganhou minha confiança."</p>
                    <div class="testimonial-author">
                        <img src="https://i.pravatar.cc/50?u=a042581f4e29026706d" alt="Avatar">
                        <span>Carlos_Sniper</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="section" id="sobre">
        <div class="container section-content">
            <h3 class="section-title">Sobre a Alien Cheats</h3>
            <div class="about-content">
                <img src="https://via.placeholder.com/800x600/111111/FFFFFF?text=ALIEN" alt="Sobre a Alien Cheats">
                <div>
                    <p>Na Alien Cheats, somos apaixonados por inovação e performance. Desenvolvemos soluções de software de alta qualidade, seguras e eficientes, garantindo que você tenha a melhor experiência possível em seus jogos favoritos.</p>
                    <p>Nossa equipe de especialistas trabalha incansavelmente para oferecer cheats indetectáveis e constantemente atualizados, com foco na sua segurança e satisfação. Sua vitória é a nossa missão.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="contato">
        <div class="container section-content">
            <h3 class="section-title">Precisa de Ajuda?</h3>
            <p class="section-subtitle">Nossa equipe de suporte está online 24/7, pronta para te ajudar. Entre na nossa comunidade no Discord para um atendimento rápido e personalizado.</p>
            <a href="https://discord.gg/tCaBdZSWfe" target="_blank" class="cta-button">
                <i class="fab fa-discord"></i> Entrar no Discord
            </a>
        </div>
    </section>
</main>

<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h5>ALIEN<span>CHEATS</span></h5>
                <p style="color: var(--cor-texto-muted);">A vantagem competitiva que você procura, com segurança e performance.</p>
            </div>
            <div class="footer-col">
                <h5>Navegação</h5>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#produtos">Produtos</a></li>
                    <li><a href="#diferenciais">Diferenciais</a></li>
                    <li><a href="#avaliacoes">Avaliações</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Suporte</h5>
                <ul>
                    <li><a href="#contato">Contato via Discord</a></li>
                    <li><a href="#">Termos de Serviço</a></li>
                    <li><a href="#">Política de Privacidade</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Conecte-se</h5>
                <div class="social-icons">
                    <a href="https://discord.gg/tCaBdZSWfe" target="_blank" title="Discord"><i class="fab fa-discord"></i></a>
                    </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> Alien Cheats. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>

<div class="cart-modal-overlay">
    <div class="cart-modal">
        <button class="close-modal-btn"><i class="fas fa-times"></i></button>
        <h4>Seu Carrinho</h4>
        <div class="cart-items-list">
            <p class="empty-cart-message">Seu carrinho está vazio.</p>
        </div>
        <div class="cart-total">
            Total: R$ <span id="cart-total-value">0.00</span>
        </div>
        <div class="cart-actions">
            <button class="clear-cart-btn">Limpar Carrinho</button>
            <button class="checkout-btn">Finalizar Compra</button>
        </div>
    </div>
</div>
<div id="custom-notification" class="custom-notification"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // =====================================
    // NOVO SCRIPT PARA ANIMAÇÕES E MENU MOBILE
    // =====================================

    // Animação de scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.section-content').forEach(section => {
        observer.observe(section);
    });

    // Menu Mobile
    const nav = document.querySelector('nav');
    const openMenuBtn = document.querySelector('.open-nav-btn');
    const closeMenuBtn = document.querySelector('.close-nav-btn');
    const navLinks = document.querySelectorAll('nav a');

    openMenuBtn.addEventListener('click', () => {
        nav.classList.add('active');
    });
    closeMenuBtn.addEventListener('click', () => {
        nav.classList.remove('active');
    });
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            nav.classList.remove('active');
        });
    });


    // =====================================
    // SEU SCRIPT ORIGINAL (SEM ALTERAÇÕES NA LÓGICA)
    // =====================================
    const categoryLinks = document.querySelectorAll('.produtos-categorias a');
    const productGroups = document.querySelectorAll('.produtos-grupo');
    const cartCountSpan = document.querySelector('.cart-count');
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    const cartModalOverlay = document.querySelector('.cart-modal-overlay');
    const cartIcon = document.querySelector('.cart-icon');
    const closeModalBtn = document.querySelector('.close-modal-btn');
    const cartItemsList = document.querySelector('.cart-items-list');
    const cartTotalValue = document.getElementById('cart-total-value');
    const clearCartBtn = document.querySelector('.clear-cart-btn');
    const checkoutBtn = document.querySelector('.checkout-btn');
    const isUserLoggedIn = <?php echo json_encode($is_logged_in); ?>;
    const customNotificationElement = document.getElementById('custom-notification');
    
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
    
    let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
    function updateCartCount() { cartCountSpan.textContent = cartItems.length; }
    
    function addToCart(product) {
        if (!isUserLoggedIn) {
            showCustomNotification("Você precisa estar logado para comprar.", 'error');
            setTimeout(() => { window.location.href = 'login.php'; }, 2000);
            return;
        }
        cartItems.push(product);
        localStorage.setItem('cartItems', JSON.stringify(cartItems));
        updateCartCount();
        showCustomNotification(`${product.name} adicionado ao carrinho!`, 'success');
        if (cartModalOverlay.classList.contains('active')) { displayCartItems(); }
    }
    
    function removeFromCart(index) {
        cartItems.splice(index, 1);
        localStorage.setItem('cartItems', JSON.stringify(cartItems));
        updateCartCount();
        displayCartItems();
        showCustomNotification("Item removido.", 'info');
    }
    
    function clearCart() {
        cartItems = [];
        localStorage.setItem('cartItems', JSON.stringify(cartItems));
        updateCartCount();
        displayCartItems();
    }
    
    function displayCartItems() {
        cartItemsList.innerHTML = '';
        let total = 0;
        if (cartItems.length === 0) {
            cartItemsList.innerHTML = '<p class="empty-cart-message">Seu carrinho está vazio.</p>';
            cartTotalValue.textContent = '0.00';
            return;
        }
        cartItems.forEach((item, index) => {
            const cartItemDiv = document.createElement('div');
            cartItemDiv.classList.add('cart-item');
            cartItemDiv.innerHTML = `<div class="cart-item-info"><div class="cart-item-name">${item.name}</div><div class="cart-item-price">R$ ${item.price.toFixed(2).replace('.', ',')}</div></div><button class="cart-item-remove" data-index="${index}"><i class="fas fa-trash-alt"></i></button>`;
            cartItemsList.appendChild(cartItemDiv);
            total += item.price;
        });
        cartTotalValue.textContent = total.toFixed(2).replace('.', ',');
        document.querySelectorAll('.cart-item-remove').forEach(button => {
            button.addEventListener('click', function() { removeFromCart(parseInt(this.dataset.index)); });
        });
    }

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (!isUserLoggedIn) {
                showCustomNotification("Faça login para adicionar ao carrinho.", 'error');
                setTimeout(() => { window.location.href = 'login.php'; }, 2000);
                return;
            }
            const card = this.closest('.card');
            const selectElement = card.querySelector('.product-option');
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const stock = parseInt(selectedOption.dataset.stock);

            if (stock <= 0) {
                showCustomNotification("Este item está esgotado.", "error");
                return;
            }
            const product = {
                id: card.dataset.productId + '-' + selectedOption.dataset.planName,
                name: `${selectElement.dataset.productName} - ${selectedOption.dataset.planName}`,
                price: parseFloat(selectedOption.value)
            };
            addToCart(product);
        });
    });

    cartIcon.addEventListener('click', function() {
        if (!isUserLoggedIn) {
            showCustomNotification("Faça login para ver o carrinho.", 'error');
            setTimeout(() => { window.location.href = 'login.php'; }, 2000);
            return;
        }
        displayCartItems();
        cartModalOverlay.classList.add('active');
    });

    closeModalBtn.addEventListener('click', () => cartModalOverlay.classList.remove('active'));
    cartModalOverlay.addEventListener('click', (e) => { if(e.target === cartModalOverlay) cartModalOverlay.classList.remove('active')});
    clearCartBtn.addEventListener('click', clearCart);
    checkoutBtn.addEventListener('click', function() {
        if (cartItems.length > 0) {
            window.location.href = 'comprar.php';
        } else {
            showCustomNotification("Seu carrinho está vazio.", 'error');
        }
    });

    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.dataset.category;
            categoryLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            productGroups.forEach(g => g.classList.remove('active'));
            document.getElementById(`produtos-${category}`).classList.add('active');
        });
    });

    updateCartCount();
});
</script>

</body>
</html>