<?php

// Nome: Marcos Paulo da Silva


session_start();
require 'conexao.php';

// Só ADM, Secretaria ou Cliente pode acessar
if (!in_array($_SESSION['perfil'], [1,2,4])) {
    header('Location: principal.php');
    exit();
}

$cliente = null;
$erro = "";
$sucesso = "";

// Busca cliente por ID ou nome
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['busca_cliente'])) {
    $busca = trim($_POST['busca_cliente']);
    if (is_numeric($busca)) {
        $sql = "SELECT * FROM cliente WHERE id_cliente = :busca";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM cliente WHERE nome LIKE :busca_nome";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
    }
    $stmt->execute();
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cliente) $erro = "Cliente não encontrado!";
}

// Alteração de cliente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_cliente']) && isset($_POST['alterar_cliente'])) {
    $id_cliente = $_POST['id_cliente'];
    $nome = trim($_POST['nome'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $telefone = trim($_POST['telefone'] ?? "");
    $endereco = trim($_POST['endereco'] ?? "");

    if (!$nome || !$email || !$telefone || !$endereco) {
        $erro = "Todos os campos são obrigatórios!";
    } elseif (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/u', $nome)) {
        $erro = "O nome deve conter apenas letras e espaços!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido!";
    } else {
        $sql = "UPDATE cliente SET nome = :nome, email = :email, telefone = :telefone, endereco = :endereco WHERE id_cliente = :id_cliente";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':telefone' => $telefone,
            ':endereco' => $endereco,
            ':id_cliente' => $id_cliente
        ])) {
            $sucesso = "Cliente alterado com sucesso!";
            $cliente = ["id_cliente"=>$id_cliente, "nome"=>$nome, "email"=>$email, "telefone"=>$telefone, "endereco"=>$endereco];
        } else {
            $erro = "Erro ao alterar cliente!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Alterar Cliente</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: #fff;
            min-height: 100vh;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .alterar-cliente-container {
            background: #fff;
            border-radius: 18px;
            border: 2.5px solid #b30000;
            box-shadow: 0 0 44px #18181825, 0 2px 10px #b3000022;
            max-width: 480px;
            margin: 54px auto 0 auto;
            padding: 42px 32px 30px 32px;
            animation: fadein 1.1s cubic-bezier(.23,1.51,.55,.93);
        }
        @keyframes fadein {
            from { opacity: 0; transform: translateY(38px);}
            to { opacity: 1; transform: none;}
        }
        h2 {
            color: #b30000;
            font-weight: 700;
            font-size: 2em;
            margin-bottom: 24px;
            letter-spacing: 0.03em;
        }
        .form-label {
            color: #b30000;
            font-weight: 600;
        }
        .form-control:focus {
            border-color: #181818;
            box-shadow: 0 0 0 0.17rem #18181833;
        }
        .btn-main {
            background: #b30000;
            color: #fff;
            font-weight: 700;
            border-radius: 12px;
            padding: 12px 0;
            font-size: 1.13em;
            letter-spacing: 1px;
            border: none;
            width: 100%;
            margin-bottom: 6px;
            box-shadow: 0 4px 13px #18181818;
            transition: background .22s, color .22s, transform .18s;
        }
        .btn-main:hover, .btn-main:focus {
            background: #fff;
            color: #b30000;
            border: 1.3px solid #b30000;
            transform: translateY(-2px) scale(1.03);
        }
        .btn-cancelar {
            background: #fff;
            color: #b30000;
            font-weight: 700;
            border-radius: 12px;
            padding: 12px 0;
            font-size: 1.13em;
            border: 1.5px solid #b30000;
            width: 100%;
            margin-bottom: 6px;
            box-shadow: 0 2px 6px #18181810;
            transition: background .22s, color .22s, transform .18s;
        }
        .btn-cancelar:hover, .btn-cancelar:focus {
            background: #b30000;
            color: #fff;
        }
        .msg-erro {
            color: #fff;
            background: linear-gradient(90deg, #ff2222 60%, #181818 100%);
            border-radius: 9px;
            padding: 12px;
            margin-bottom: 15px;
            font-weight: 700;
            text-align: center;
            box-shadow: 0 2px 9px #18181820;
        }
        .msg-sucesso {
            color: #fff;
            background: linear-gradient(90deg, #b30000 60%, #222 100%);
            border-radius: 9px;
            padding: 12px;
            margin-bottom: 15px;
            font-weight: 700;
            text-align: center;
            box-shadow: 0 2px 12px #18181833;
        }
        .btn-voltar-top-right {
            position: fixed;
            top: 22px;
            right: 28px;
            z-index: 9999;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 27px;
            border-radius: 40px;
            background: #222;
            color: #fff;
            font-size: 1.09rem;
            font-weight: 700;
            border: none;
            box-shadow: 0 8px 28px #18181849;
            cursor: pointer;
            transition: background 0.3s, box-shadow 0.3s, transform 0.22s;
            text-decoration: none;
            outline: none;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .btn-voltar-top-right svg {
            width: 20px;
            height: 20px;
            fill: #fff;
            transition: transform 0.3s;
        }
        .btn-voltar-top-right:hover {
            background: #b30000;
            color: #fff;
            box-shadow: 0 12px 44px #b3000022;
            transform: translateY(-2px) scale(1.06);
        }
        .btn-voltar-top-right:hover svg {
            fill: #fff;
            transform: translateX(-6px) scale(1.1);
        }
        @media (max-width: 500px) {
            .alterar-cliente-container { padding: 15px 5vw; }
            .btn-voltar-top-right { top: 8px; right: 8px;}
        }
    </style>
    <script>
    function somenteLetras(e) {
        let char = String.fromCharCode(e.which);
        if (!/^[A-Za-zÀ-ÖØ-öø-ÿ\s]$/.test(char) && e.keyCode !== 8 && e.keyCode !== 9) {
            e.preventDefault();
        }
    }
    function formatarTelefone(e) {
        let input = e.target;
        let value = input.value.replace(/\D/g, "");
        let formatted = "";
        if(value.length === 0) {
            formatted = "";
        } else if(value.length < 3) {
            formatted = value;
        } else if(value.length < 7) {
            formatted = "(" + value.substring(0,2) + ") " + value.substring(2);
        } else if(value.length === 10) {
            formatted = "(" + value.substring(0,2) + ") " + value.substring(2,6) + "-" + value.substring(6,10);
        } else if(value.length >= 11) {
            formatted = "(" + value.substring(0,2) + ") " + value.substring(2,7) + "-" + value.substring(7,11);
        } else {
            formatted = "(" + value.substring(0,2) + ") " + value.substring(2);
        }
        input.value = formatted;
    }
    function validarEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    function validarFormulario(e) {
        var email = document.getElementById('email').value.trim();
        if (!validarEmail(email)) {
            alert('E-mail inválido!');
            document.getElementById('email').focus();
            e.preventDefault();
            return false;
        }
        return true;
    }
    window.onload = function() {
        let nome = document.getElementById('nome');
        if(nome) {
            nome.addEventListener('keypress', somenteLetras);
            nome.addEventListener('paste', function(e) {
                let paste = (e.clipboardData || window.clipboardData).getData('text');
                if (!/^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/.test(paste)) {
                    e.preventDefault();
                }
            });
        }
        let tel = document.getElementById('telefone');
        if(tel) {
            tel.addEventListener('input', formatarTelefone);
            tel.addEventListener('paste', function(e) {
                let paste = (e.clipboardData || window.clipboardData).getData('text');
                if (!/^\d+$/.test(paste.replace(/\D/g, ""))) {
                    e.preventDefault();
                }
            });
            tel.value = tel.value.replace(/\D/g, '');
            formatarTelefone({target: tel});
        }
        let f = document.getElementById('form_alterar_cliente');
        if(f) f.addEventListener('submit', validarFormulario);
    }
    </script>
</head>
<body>
    <div class="alterar-cliente-container shadow">
        <h2>Alterar Cliente</h2>
        <?php if($erro): ?><div class="msg-erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <?php if($sucesso): ?><div class="msg-sucesso"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>

        <!-- Formulário para buscar cliente por ID ou Nome -->
        <form action="" method="POST" class="mb-4">
            <div class="mb-3">
                <label for="busca_cliente" class="form-label">ID ou Nome do cliente:</label>
                <input type="text" id="busca_cliente" name="busca_cliente" class="form-control" required>
            </div>
            <button type="submit" class="btn-main">Buscar</button>
        </form>

        <?php if ($cliente): ?>
        <!-- Formulário para alterar cliente -->
        <form action="" method="POST" id="form_alterar_cliente" autocomplete="off">
            <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($cliente['id_cliente']) ?>">
            <input type="hidden" name="alterar_cliente" value="1">

            <div class="mb-3">
                <label for="nome" class="form-label">Nome:</label>
                <input type="text" id="nome" name="nome" maxlength="100" required
                    pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s]+"
                    title="Apenas letras e espaços"
                    value="<?= htmlspecialchars($cliente['nome']) ?>"
                    class="form-control">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail:</label>
                <input type="email" id="email" name="email" maxlength="100" required
                    pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$"
                    title="Digite um e-mail válido"
                    value="<?= htmlspecialchars($cliente['email']) ?>"
                    class="form-control">
            </div>
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone:</label>
                <input type="text" id="telefone" name="telefone" maxlength="15" required
                    title="Digite o telefone"
                    value="<?= htmlspecialchars($cliente['telefone']) ?>"
                    class="form-control">
            </div>
            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço:</label>
                <input type="text" id="endereco" name="endereco" maxlength="150" required
                    placeholder="Digite o endereço completo"
                    value="<?= htmlspecialchars($cliente['endereco']) ?>"
                    class="form-control">
            </div>
            <button type="submit" class="btn-main">Salvar Alterações</button>
            <button type="button" class="btn-cancelar" onclick="window.location.href='principal.php'">Cancelar</button>
        </form>
        <?php endif; ?>
    </div>
    <a href="javascript:history.back()" class="btn-voltar-top-right" title="Voltar">
        <svg viewBox="0 0 24 24"><path d="M15.5 4l-1.42 1.41L18.67 10H4v2h14.67l-4.59 4.59L15.5 20l7-8z"/></svg>
        Voltar
    </a>
</body>
</html>