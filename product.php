<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Produto</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <h2>Detalhes do Produto</h2>
  <p id="produto-info">Carregando...</p>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    document.getElementById('produto-info').innerText = `Você está vendo o produto ID ${id}`;
  </script>
</body>
</html>
