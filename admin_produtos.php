<?php
session_start();
include('assets/includes/db.php');

// Só admin pode acessar
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Variáveis para mensagens
$mensagem = '';
$erro = '';

// Função para limpar input
function clean($conn, $input) {
    return $conn->real_escape_string(trim($input));
}

// Ações: adicionar, editar, excluir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ADICIONAR PRODUTO
    if (isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
        $nome = clean($conn, $_POST['nome']);
        $descricao = clean($conn, $_POST['descricao']);
        $preco = floatval($_POST['preco']);
        $estoque = intval($_POST['estoque']);

        $imagem_url = ''; 
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "assets/images/"; 
            $image_name = uniqid() . '_' . basename($_FILES["imagem"]["name"]); 
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $allowed_types = array("jpg", "png", "jpeg", "gif");
            if (in_array($imageFileType, $allowed_types)) {
                if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
                    $imagem_url = $image_name; 
                } else {
                    $erro .= "Erro ao fazer upload da imagem.<br>";
                }
            } else {
                $erro .= "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos.<br>";
            }
        }


        if ($nome && $descricao && $preco > 0 && $estoque >= 0) {
            $sql = "INSERT INTO produtos (nome, descricao, preco, estoque, imagem_url) VALUES ('$nome', '$descricao', $preco, $estoque, '$imagem_url')";
            if ($conn->query($sql)) {
                $mensagem = "Produto adicionado com sucesso!";
            } else {
                $erro .= "Erro ao adicionar produto: " . $conn->error;
            }
        } else {
            $erro .= "Preencha todos os campos corretamente.<br>";
        }
    }

    // EDITAR PRODUTO
    if (isset($_POST['acao']) && $_POST['acao'] === 'editar') {
        $id = intval($_POST['id']);
        $nome = clean($conn, $_POST['nome']);
        $descricao = clean($conn, $_POST['descricao']);
        $preco = floatval($_POST['preco']);
        $estoque = intval($_POST['estoque']);
        $imagem_url_existente = clean($conn, $_POST['imagem_url_existente']);

        $imagem_url_nova = $imagem_url_existente;

        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "assets/images/";
            $image_name = uniqid() . '_' . basename($_FILES["imagem"]["name"]);
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $allowed_types = array("jpg", "png", "jpeg", "gif");
            if (in_array($imageFileType, $allowed_types)) {
                if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
                    $imagem_url_nova = $image_name;
                    if (!empty($imagem_url_existente) && file_exists($target_dir . $imagem_url_existente)) {
                        unlink($target_dir . $imagem_url_existente);
                    }
                } else {
                    $erro .= "Erro ao fazer upload da nova imagem.<br>";
                }
            } else {
                $erro .= "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos para a nova imagem.<br>";
            }
        }

        if ($id > 0 && $nome && $descricao && $preco > 0 && $estoque >= 0) {
            $sql = "UPDATE produtos SET nome='$nome', descricao='$descricao', preco=$preco, estoque=$estoque, imagem_url='$imagem_url_nova' WHERE id=$id";
            if ($conn->query($sql)) {
                $mensagem = "Produto editado com sucesso!";
            } else {
                $erro .= "Erro ao editar produto: " . $conn->error;
            }
        } else {
            $erro .= "Preencha todos os campos corretamente para editar.<br>";
        }
    }
}

// EXCLUIR PRODUTO via GET
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    if ($id > 0) {
        $stmt = $conn->prepare("SELECT imagem_url FROM produtos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res_img = $stmt->get_result();
        if ($res_img && $res_img->num_rows > 0) {
            $produto_img = $res_img->fetch_assoc();
            $caminho_imagem = "assets/images/" . $produto_img['imagem_url'];
            if (file_exists($caminho_imagem) && !empty($produto_img['imagem_url'])) {
                unlink($caminho_imagem); 
            }
        }
        $stmt->close();

        $sql = "DELETE FROM produtos WHERE id=$id";
        if ($conn->query($sql)) {
            $mensagem = "Produto excluído com sucesso!";
        } else {
            $erro = "Erro ao excluir produto: " . $conn->error;
        }
    }
}

// Pegar produto para editar (GET)
$editarProduto = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM produtos WHERE id=$id");
    if ($res && $res->num_rows > 0) {
        $editarProduto = $res->fetch_assoc();
    } else {
        $erro = "Produto para edição não encontrado.";
    }
}

// Listar todos produtos
$result = $conn->query("SELECT * FROM produtos ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração de Produtos - Alien Cheats</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body {
            background: #000;
            color: #eee;
            font-family: 'Roboto', sans-serif;
            margin: 0; padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px; /* Maior largura */
            margin: 30px auto;
            padding: 30px;
            background: #111; /* Fundo mais escuro */
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.08); /* Sombra mais suave */
            border: 1px solid #333;
        }
        h1, h2 {
            font-family: 'Orbitron', sans-serif;
            color: #fff; /* Títulos brancos */
            text-align: center;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }
        h1::after, h2::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background: white;
            margin: 10px auto 0;
        }
        form {
            background-color: #1a1a1a; /* Fundo do formulário */
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 1px solid #222;
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #ccc;
        }
        form input[type="text"],
        form input[type="number"],
        form textarea,
        form input[type="file"] { /* Estilo para input file */
            width: 100%;
            padding: 12px;
            margin-top: 4px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #444;
            background-color: #2a2a2a;
            color: #eee;
            font-size: 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        form input[type="text"]:focus,
        form input[type="number"]:focus,
        form textarea:focus {
            outline: none;
            border-color: white;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }
        form textarea {
            resize: vertical;
            min-height: 80px;
        }
        form input[type="file"] {
            padding: 10px; /* Ajuste para input file */
            background-color: #3a3a3a;
            cursor: pointer;
        }
        form button {
            background-color: white;
            color: black;
            border: none;
            padding: 12px 25px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        form button:hover {
            background-color: #eee;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        form .cancel-button {
            background: transparent;
            color: white;
            border: 2px solid white;
            margin-left: 15px;
        }
        form .cancel-button:hover {
            background: #222;
            color: white;
            box-shadow: none;
            transform: none;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background-color: #1a1a1a;
            border-radius: 10px;
            overflow: hidden; /* Garante que as bordas da tabela sejam arredondadas */
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.05);
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        th {
            background: #222; /* Fundo do cabeçalho da tabela */
            color: white;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 14px;
        }
        td {
            color: #bbb;
        }
        tr:nth-child(even) { /* Linhas alternadas */
            background: #111;
        }
        tr:hover {
            background: #252525;
        }
        td img {
            width: 60px; /* Tamanho da imagem na tabela */
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #444;
        }
        a.action-button { /* Botões de ação na tabela */
            background: #007bff; /* Azul para editar */
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin-right: 8px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        a.action-button.edit {
            background: #555; /* Cinza para editar */
        }
        a.action-button.edit:hover {
            background: #666;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        }
        a.action-button.delete {
            background: #cc0000; /* Vermelho escuro para excluir */
        }
        a.action-button.delete:hover {
            background: #ff3300;
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.3);
        }

        .mensagem {
            background: #4CAF50; /* Verde de sucesso */
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .erro {
            background: #f44336; /* Vermelho de erro */
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        /* Responsividade para Admin */
        @media (max-width: 768px) {
            .container {
                margin: 20px 10px;
                padding: 20px;
            }
            h1, h2 {
                font-size: 28px;
            }
            form input, form textarea, form button {
                font-size: 14px;
                padding: 10px;
            }
            form button {
                width: 100%;
                margin-left: 0;
                margin-bottom: 10px;
            }
            form .cancel-button {
                width: 100%;
            }
            table {
                font-size: 14px;
            }
            th, td {
                padding: 10px;
            }
            td img {
                width: 40px;
                height: 40px;
            }
            a.action-button {
                padding: 6px 10px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Administração de Produtos</h1>

    <?php if ($mensagem): ?>
        <div class="mensagem"><i class="fas fa-check-circle"></i> <?php echo $mensagem; ?></div>
    <?php endif; ?>

    <?php if ($erro): ?>
        <div class="erro"><i class="fas fa-exclamation-triangle"></i> <?php echo $erro; ?></div>
    <?php endif; ?>

    <h2><?php echo $editarProduto ? "Editar Produto" : "Adicionar Novo Produto"; ?></h2>
    <form method="POST" enctype="multipart/form-data"> 
        <input type="hidden" name="acao" value="<?php echo $editarProduto ? "editar" : "adicionar"; ?>">
        <?php if ($editarProduto): ?>
            <input type="hidden" name="id" value="<?php echo $editarProduto['id']; ?>">
            <input type="hidden" name="imagem_url_existente" value="<?php echo htmlspecialchars($editarProduto['imagem_url']); ?>">
        <?php endif; ?>

        <label for="nome">Nome do Produto:</label>
        <input type="text" id="nome" name="nome" required value="<?php echo $editarProduto ? htmlspecialchars($editarProduto['nome']) : ''; ?>">

        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao" required><?php echo $editarProduto ? htmlspecialchars($editarProduto['descricao']) : ''; ?></textarea>

        <label for="preco">Preço (ex: 49.90):</label>
        <input type="number" step="0.01" id="preco" name="preco" required value="<?php echo $editarProduto ? $editarProduto['preco'] : ''; ?>">

        <label for="estoque">Estoque:</label>
        <input type="number" id="estoque" name="estoque" required value="<?php echo $editarProduto ? $editarProduto['estoque'] : '0'; ?>">

        <label for="imagem">Imagem do Produto (PNG, JPG, GIF):</label>
        <?php if ($editarProduto && !empty($editarProduto['imagem_url'])): ?>
            <p style="margin-bottom: 10px; color: #bbb;">Imagem atual:</p>
            <img src="assets/images/<?php echo htmlspecialchars($editarProduto['imagem_url']); ?>" alt="Imagem atual do produto" style="max-width: 150px; height: auto; border-radius: 5px; margin-bottom: 15px; border: 1px solid #444;">
            <p style="margin-bottom: 15px; color: #bbb;">Selecione uma nova imagem para substituir:</p>
        <?php endif; ?>
        <input type="file" id="imagem" name="imagem" accept="image/png, image/jpeg, image/gif">


        <button type="submit">
            <?php if ($editarProduto): ?>
                <i class="fas fa-save"></i> Salvar Alterações
            <?php else: ?>
                <i class="fas fa-plus-circle"></i> Adicionar Produto
            <?php endif; ?>
        </button>
        <?php if ($editarProduto): ?>
            <a href="admin_produtos.php" class="cancel-button"><i class="fas fa-times-circle"></i> Cancelar Edição</a>
        <?php endif; ?>
    </form>

    <h2>Produtos Cadastrados</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Imagem</th> <th>Nome</th>
                <th>Preço (R$)</th>
                <th>Estoque</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result && $result->num_rows > 0):
                while ($produto = $result->fetch_assoc()): 
            ?>
                <tr>
                    <td><?php echo $produto['id']; ?></td>
                    <td>
                        <?php if (!empty($produto['imagem_url'])): ?>
                            <img src="assets/images/<?php echo htmlspecialchars($produto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/60x60/333333/FFFFFF?text=SEM+IMG" alt="Sem Imagem">
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($produto['nome']); ?><br><small style="color:#888; font-size:0.9em;"><?php echo htmlspecialchars($produto['descricao']); ?></small></td>
                    <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                    <td><?php echo $produto['estoque']; ?></td>
                    <td>
                        <a href="admin_produtos.php?editar=<?php echo $produto['id']; ?>" class="action-button edit"><i class="fas fa-edit"></i> Editar</a>
                        <a href="admin_produtos.php?excluir=<?php echo $produto['id']; ?>" class="action-button delete" onclick="return confirm('Tem certeza que quer excluir este produto?');"><i class="fas fa-trash-alt"></i> Excluir</a>
                    </td>
                </tr>
            <?php 
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Nenhum produto cadastrado.</td>
                </tr>
            <?php
            endif;
            ?>
        </tbody>
    </table>
</div>

</body>
</html>