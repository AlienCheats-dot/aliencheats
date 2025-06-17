<?php
// Variáveis de conexão com o banco de dados
$host = "127.0.0.1";          // Host do MySQL
$port = "3307";               // Porta do MySQL no XAMPP (pode ser 3306 ou 3307)
$user = "root";               // Usuário padrão do XAMPP
$password = "";               // Senha padrão é vazia no XAMPP
$dbname = "loja";             // Nome do banco que você criou (ajuste conforme necessário)

$conn = new mysqli($host, $user, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
