<?php

// Nome: Marcos Paulo da Silva


session_start();
require_once 'conexao.php';

$erro = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? "";
    $senha = $_POST['senha'] ?? "";

    $sql = "SELECT * FROM usuario WHERE email = :email"; 
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario'] = $usuario['nome'];
        $_SESSION['perfil'] = $usuario['id_perfil'];
        $_SESSION['id_usuario'] = $usuario['id_usuario'];

        if ($usuario['senha_temporaria']) {
            header('Location: alterar_senha.php');
            exit();
        }
        header('Location: principal.php');
        exit();
    } else {
        $erro = "E-mail ou senha inválidos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seja bem-vindo(a) | Login</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;500&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #fff 0%, #b30000 100%);
            min-height: 100vh;
            font-family: 'Montserrat', Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-minimal {
            background: #fff;
            border-radius: 18px;
            border: 2.5px solid #b30000;
            box-shadow: 0 0 34px #b3000017;
            max-width: 370px;
            width: 100%;
            padding: 38px 30px 28px 30px;
            margin: 60px 0;
            animation: fadein 1.1s cubic-bezier(.23,1.51,.55,.93);
        }
        @keyframes fadein {
            from { opacity: 0; transform: translateY(38px);}
            to { opacity: 1; transform: none;}
        }
        .login-minimal h1 {
            color: #b30000;
            font-weight: 700;
            font-size: 2em;
            margin-bottom: 5px;
        }
        .login-minimal .subtitle {
            color: #b30000b0;
            font-size: 1.13em;
            margin-bottom: 28px;
            font-weight: 500;
        }
        .form-label {
            color: #b30000;
            font-weight: 600;
        }
        .form-control:focus {
            border-color: #b30000;
            box-shadow: 0 0 0 0.17rem #b3000033;
        }
        .btn-login {
            background: #b30000;
            color: #fff;
            font-weight: 700;
            border-radius: 12px;
            padding: 12px 0;
            font-size: 1.13em;
            letter-spacing: 1px;
            border: none;
            width: 100%;
            box-shadow: 0 4px 13px #b3000015;
            transition: background .22s, color .22s, transform .18s;
        }
        .btn-login:hover {
            background: #fff;
            color: #b30000;
            border: 1.3px solid #b30000;
            transform: translateY(-2px) scale(1.03);
        }
        .msg-erro {
            color: #fff;
            background: linear-gradient(90deg, #ff2222 60%, #b30000 100%);
            border-radius: 9px;
            padding: 12px;
            margin-bottom: 15px;
            font-weight: 700;
            text-align: center;
            box-shadow: 0 2px 9px #b3000020;
        }
        .esqueci {
            display: block;
            margin-top: 18px;
            color: #b30000;
            font-weight: 600;
            text-decoration: none;
            font-size: 1.04em;
            transition: color .18s;
        }
        .esqueci:hover {
            color: #fff;
            background: #b30000;
            border-radius: 8px;
            padding: 3px 9px;
            text-decoration: underline;
        }
        @media (max-width: 500px) {
            .login-minimal {
                padding: 15px 5vw;
            }
        }
    </style>
</head>
<body>
    <div class="login-minimal shadow">
        <h1>Seja bem-vindo(a)!</h1>
        <div class="subtitle">Entre com seus dados para acessar o sistema</div>
        <?php if ($erro): ?>
            <div class="msg-erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST" autocomplete="off">
            <div class="mb-3 text-start">
                <label for="email" class="form-label">E-mail</label>
                <input type="email"
                       class="form-control form-control-lg"
                       id="email"
                       name="email"
                       placeholder="Digite seu e-mail"
                       required autofocus>
            </div>
            <div class="mb-3 text-start">
                <label for="senha" class="form-label">Senha</label>
                <input type="password"
                       class="form-control form-control-lg"
                       id="senha"
                       name="senha"
                       placeholder="Digite sua senha"
                       required>
            </div>
            <button type="submit" class="btn-login mt-2">Entrar</button>
        </form>
        <a href="recuperar_senha.php" class="esqueci">Esqueci minha senha</a>
    </div>
</body>
</html>