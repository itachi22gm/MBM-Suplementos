<!DOCTYPE case 'example':
    if ($method === 'GET') {
        handleGetExample($db);
    }
    if ($method === 'GET' && isset($_GET['id'])) {
        handleGetExampleById($db, $_GET['id']);
    } elseif ($method === 'PUT' && isset($_GET['id'])) {
        exampleUpdate($db, $_GET['id']);
    } elseif ($method === 'DELETE' && isset($_GET['id'])) {
        exampleDelete($db, $_GET['id']);
    } else {
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed for example']);
    }
    break;>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>MBM Suplementos - Login / Cadastro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-container {
            display: flex;
            background: var(--card-bg);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 4rem auto;
        }
        .auth-box { flex: 1; padding: 2.5rem; }
        .auth-box:first-child { border-right: 1px solid #dee2e6; background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <div class="auth-box">
                <h2>Já sou Cliente</h2>
                <form action="produtos.html" method="GET">
                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" placeholder="seu@email.com" required>
                    </div>
                    <div class="form-group">
                        <label>Senha</label>
                        <input type="password" placeholder="Sua senha" required>
                    </div>
                    <button type="submit" class="btn">Entrar</button>
                </form>
            </div>
            
            <div class="auth-box">
                <h2>Criar Conta</h2>
                <form action="produtos.html" method="GET">
                    <div class="form-group">
                        <label>Nome Completo</label>
                        <input type="text" placeholder="Seu nome" required>
                    </div>
                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" placeholder="seu@email.com" required>
                    </div>
                    <div class="form-group">
                        <label>Crie uma Senha</label>
                        <input type="password" placeholder="Mínimo 6 dígitos" required>
                    </div>
                    <button type="submit" class="btn btn-accent">Cadastrar e Entrar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

