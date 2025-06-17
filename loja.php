<?php
session_start();
include('assets/includes/db.php');

$sql = "SELECT * FROM produtos ORDER BY id DESC";
$result = $conn->query($sql);
?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="container" style="max-width: 900px; margin-top: 40px;">
    <h1>🛒 Produtos Disponíveis</h1>

    <?php while($produto = $result->fetch_assoc()) { ?>
        <div style="background:#2a2a2a; padding:20px; border-radius:10px; margin-bottom:15px;">
            <h2><?php echo htmlspecialchars($produto['nome']); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>
            <p><strong>Preço:</strong> R$ <?php echo number_format($produto['preco'],2,',','.'); ?></p>
            <p><strong>Estoque:</strong> <?php echo $produto['estoque']; ?></p>

            <?php if ($produto['estoque'] > 0): ?>
                <a href="comprar.php?id=<?php echo $produto['id']; ?>">
                    <button style="background:#00ff99; color:#121212; border:none; padding:10px 18px; border-radius:6px; cursor:pointer;">Comprar</button>
                </a>
            <?php else: ?>
                <button disabled style="background:#555; color:#999; border:none; padding:10px 18px; border-radius:6px;">Esgotado</button>
            <?php endif; ?>
        </div>
    <?php } ?>
</div>
