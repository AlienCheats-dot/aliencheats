document.addEventListener('DOMContentLoaded', function() {
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

  // A variável `isUserLoggedIn` é definida globalmente no `index.php`
  // e já está acessível aqui.

  // --- FUNÇÕES DO SISTEMA DE NOTIFICAÇÃO CUSTOMIZADA ---
  const customNotificationElement = document.getElementById('custom-notification');

  function showCustomNotification(message, type = 'info', duration = 3000) {
      customNotificationElement.textContent = message;
      customNotificationElement.className = `custom-notification ${type}`;
      customNotificationElement.style.display = 'block';
      customNotificationElement.style.opacity = '1';

      setTimeout(() => {
          customNotificationElement.style.opacity = '0';
          setTimeout(() => {
              customNotificationElement.style.display = 'none';
          }, 500);
      }, duration);
  }

  // --- Funções de Carrinho ---
  let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

  // ... Cole aqui TODAS as outras funções JavaScript (updateCartCount, addToCart, etc.) ...
  
  // Inicializações ao carregar a página
  updateCartCount();
  updateProductDisplayStatus();
  showCategory('freefire');
});