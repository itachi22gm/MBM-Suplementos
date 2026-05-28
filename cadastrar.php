<?php
// 1. Configurações de ligação ao Banco de Dados
$host = "localhost";
$usuario_db = "root"; 
$senha_db = "Home@spSENAI2025!";       
$nome_db = "mbm_suplementos";

// Criar a ligação
$conexao = new mysqli($host, $usuario_db, $senha_db, $nome_db);

// Verificar se houve falha na ligação
if ($conexao->connect_error) {
    die("Falha na ligação com o banco de dados: " . $conexao->connect_error);
}

// Configurar charset para evitar problemas com acentuação
$conexao->set_charset("utf8mb4");

// 2. Verificar se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Capturar dados de Autenticação (Tabela: usuarios)
    $nome_completo = trim($_POST['nome_completo']); // Note que mudei para bater com o banco
    $email         = trim($_POST['email']);
    $senha         = $_POST['senha'];
    
    // Capturar dados de Perfil e Entrega (Tabela: clientes)
    // Usamos o operador ?? "" para evitar erros caso o campo venha vazio do formulário
    $cpf         = preg_replace('/[^0-9]/', '', $_POST['cpf']); // Remove pontos e traços do CPF
    $telefone    = $_POST['telefone'] ?? null;
    $cep         = preg_replace('/[^0-9]/', '', $_POST['cep'] ?? ''); // Remove hífen do CEP
    $endereco    = $_POST['endereco'] ?? null;
    $numero      = $_POST['numero'] ?? null;
    $complemento = $_POST['complemento'] ?? null;
    $bairro      = $_POST['bairro'] ?? null;
    $cidade      = $_POST['cidade'] ?? null;
    $estado      = $_POST['estado'] ?? null;

    // Criptografar a senha (boa prática de segurança)
    $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

    // Iniciar uma transação (Garante que se uma tabela falhar, nenhuma das duas é afetada)
    $conexao->begin_transaction();

    try {
        // --- PASSO 1: Inserir na tabela de 'usuarios' ---
        $sql_usuario = "INSERT INTO usuarios (nome_completo, email, senha, nivel_acesso) VALUES (?, ?, ?, 'cliente')";
        $stmt_usuario = $conexao->prepare($sql_usuario);
        $stmt_usuario->bind_param("sss", $nome_completo, $email, $senha_criptografada);
        $stmt_usuario->execute();

        // Recuperar o ID gerado automaticamente para o usuário recém-criado
        $usuario_id = $conexao->insert_id;

        // --- PASSO 2: Inserir na tabela de 'clientes' usando o ID obtido ---
        $sql_cliente = "INSERT INTO clientes (usuario_id, cpf, telefone, cep, endereco, numero, complemento, bairro, cidade, estado) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_cliente = $conexao->prepare($sql_cliente);
        $stmt_cliente->bind_param(
            "isssssssss", 
            $usuario_id, $cpf, $telefone, $cep, $endereco, $numero, $complemento, $bairro, $cidade, $estado
        );
        $stmt_cliente->execute();

        // Se ambos os inserts deram certo, confirma as alterações no banco de dados
        $conexao->commit();

        // Envia o utilizador para a tela de produtos (atualizado para .php já que é dinâmica)
        header("Location: produtos.php");
        exit();

    } catch (mysqli_sql_exception $e) {
        // Se algo falhar, desfaz qualquer inserção parcial para não quebrar a lógica do banco
        $conexao->rollback();

        // Tratamento de erro para campos UNIQUE duplicados (E-mail ou CPF)
        if ($conexao->errno == 1062) {
            // Verifica se o termo duplicado foi o CPF ou o Email
            if (strpos($e->getMessage(), 'cpf') !== false) {
                echo "<script>alert('Este CPF já está registado!'); window.history.back();</script>";
            } else {
                echo "<script>alert('Este e-mail já está registado!'); window.history.back();</script>";
            }
        } else {
            echo "Erro crítico ao cadastrar: " . $e->getMessage();
        }
    }
}

// Fechar a ligação para libertar memória
$conexao->close();
?>