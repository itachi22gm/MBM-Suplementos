<?php
// 1. Configurações de ligação ao Banco de Dados
$host = "localhost";
$usuario_db = "root"; // Altera se o teu utilizador do MySQL for diferente
$senha_db = "";       // Coloca a senha do teu MySQL se houver
$nome_db = "mbm_suplementos";

// Criar a ligação
$conexao = new mysqli($host, $usuario_db, $senha_db, $nome_db);

// Verificar se houve falha na ligação
if ($conexao->connect_error) {
    die("Falha na ligação com o banco de dados: " . $conexao->connect_error);
}

// 2. Verificar se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Capturar os dados do formulário com proteção básica contra invasões
    $nome = $conexao->real_escape_string($_POST['nome']);
    $email = $conexao->real_escape_string($_POST['email']);
    $senha = $_POST['senha'];

    // Criptografar a senha (boa prática de segurança, nunca guardes em texto limpo)
    $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

    // 3. Preparar o comando SQL para inserir no banco
    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senha_criptografada')";

    // Executar o comando no banco de dados
    if ($conexao->query($sql) === TRUE) {
        // Se correu bem, envia o utilizador para a tela de produtos
        header("Location: produtos.html");
        exit();
    } else {
        // Se o e-mail já existir (por causa da regra UNIQUE no banco)
        if ($conexao->errno == 1062) {
            echo "<script>alert('Este e-mail já está registado!'); window.history.back();</script>";
        } else {
            echo "Erro ao cadastrar: " . $conexao->error;
        }
    }
}

// Fechar a ligação para libertar memória
$conexao->close();
?>
