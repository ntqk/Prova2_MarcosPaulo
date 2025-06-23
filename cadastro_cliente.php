<?php

// Nome: Marcos Paulo da Silva

session_start();
require_once 'conexao.php';

if (!in_array($_SESSION['perfil'], [1,2,3,4])){
    header('Location: principal.php');
    exit();
}

$erro = $sucesso = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $telefone = preg_replace('/\D/', '', trim($_POST['telefone'] ?? ""));
    $endereco = trim($_POST['endereco'] ?? "");

    if (!$nome || !$email || !$telefone || !$endereco) {
        $erro = "Todos os campos são obrigatórios!";
    } elseif (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/u', $nome)) {
        $erro = "O nome deve conter apenas letras e espaços!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido!";
    } elseif (!preg_match('/^\d{11}$/', $telefone)) {
        $erro = "Telefone deve conter exatamente 11 dígitos, ex: 47999123456!";
    } else {
        $sql = "INSERT INTO cliente (nome, email, telefone, endereco) VALUES (:nome, :email, :telefone, :endereco)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':telefone' => $telefone,
            ':endereco' => $endereco
        ])) {
            $sucesso = "Cliente cadastrado com sucesso!";
        } else {
            $erro = "Erro ao cadastrar cliente!";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #fff;
            min-height: 100vh;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .header-topo {
            width: 100vw;
            min-height: 68px;
            background: linear-gradient(90deg, #e3e4e8 65%, #c8cace 100%);
            color: #181818;
            font-size: 1.25em;
            font-weight: bold;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 14px #b3000022, 0 2px 6px #18181822;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
        }
        .header-topo .nome-autor {
            font-size: 1.13em;
            font-weight: 700;
            letter-spacing: .5px;
            border-bottom: 2.5px solid #fff;
            padding: 2px 14px;
            border-radius: 9px;
            text-shadow: 0 2px 10px #2222;
            background: rgba(255,255,255,0.07);
            margin-right: auto;
            margin-left: auto;
        }
        .header-topo .btn-voltar-top-right {
            position: absolute;
            right: 28px;
            top: 18px;
            z-index: 200;
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
        .header-topo .btn-voltar-top-right svg {
            width: 20px;
            height: 20px;
            fill: #fff;
            transition: transform 0.3s;
        }
        .header-topo .btn-voltar-top-right:hover {
            background: #b30000;
            color: #fff;
            box-shadow: 0 12px 44px #b3000022;
            transform: translateY(-2px) scale(1.06);
        }
        .header-topo .btn-voltar-top-right:hover svg {
            fill: #fff;
            transform: translateX(-6px) scale(1.1);
        }
        .cadastro-cliente-container {
            background: #fff;
            border-radius: 18px;
            border: 2.5px solid #b30000;
            box-shadow: 0 0 44px #18181825, 0 2px 10px #b3000022;
            max-width: 410px;
            margin: 110px auto 0 auto;
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
            text-align: center;
        }
        .form-label {
            color: #b30000;
            font-weight: 600;
        }
        .form-control:focus {
            border-color: #181818;
            box-shadow: 0 0 0 0.17rem #18181833;
        }
        .form-control {
            border: 1.4px solid #b30000;
            border-radius: 8px;
            font-size: 1.08em;
            padding: 8.5px 13px;
            background: #fff;
            color: #222;
            margin-bottom: 0 !important;
        }
        .campo-telefone {
            display: flex;
            flex-direction: column;
            gap: 0;
        }
        .telefone-hint {
            font-size: 0.97em;
            color: #b30000;
            margin-top: 4px;
            margin-bottom: 0px;
            letter-spacing: 0.01em;
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
            width: 48%;
            margin-bottom: 6px;
            margin-right: 10px;
            box-shadow: 0 4px 13px #18181818;
            transition: background .22s, color .22s, transform .18s;
        }
        .btn-main:hover, .btn-main:focus {
            background: #fff;
            color: #b30000;
            border: 1.3px solid #b30000;
            transform: translateY(-2px) scale(1.03);
        }
        .btn-limpar {
            background: #fff;
            color: #b30000;
            font-weight: 700;
            border-radius: 12px;
            padding: 12px 0;
            font-size: 1.13em;
            border: 1.5px solid #b30000;
            width: 48%;
            margin-bottom: 6px;
            box-shadow: 0 2px 6px #18181810;
            transition: background .22s, color .22s, transform .18s;
        }
        .btn-limpar:hover, .btn-limpar:focus {
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
        @media (max-width: 500px) {
            .cadastro-cliente-container { padding: 15px 5vw; }
            .header-topo { font-size: 1em; min-height: 56px;}
            .header-topo .btn-voltar-top-right { top: 7px; right: 7px;}
        }
    </style>
    <script>
    function somenteLetras(e) {
        let char = String.fromCharCode(e.which);
        if (!/^[A-Za-zÀ-ÖØ-öø-ÿ\s]$/.test(char) && e.keyCode !== 8 && e.keyCode !== 9) {
            e.preventDefault();
        }
    }
    function somenteNumeros(e) {
        let char = String.fromCharCode(e.which);
        if (!/^\d$/.test(char) && e.keyCode !== 8 && e.keyCode !== 9) {
            e.preventDefault();
        }
    }
    function formatarTelefone(e) {
        let input = e.target;
        let value = input.value.replace(/\D/g, "").substring(0,11);
        let formatted = "";
        if (value.length > 0) {
            formatted = "(" + value.substring(0,2) + ") ";
            if (value.length >= 7) {
                formatted += value.substring(2,7) + "-" + value.substring(7,11);
            } else if (value.length > 2) {
                formatted += value.substring(2);
            }
        }
        input.value = formatted;
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
            tel.addEventListener('keypress', somenteNumeros);
            tel.addEventListener('paste', function(e) {
                let paste = (e.clipboardData || window.clipboardData).getData('text');
                if (!/^\d+$/.test(paste.replace(/\D/g, ""))) {
                    e.preventDefault();
                }
            });
            tel.value = tel.value.replace(/\D/g, '');
            formatarTelefone({target: tel});
        }
    }
    </script>
</head>
<body>
    <div class="header-topo">
        <span class="nome-autor">Marcos Paulo da Silva</span>
        <a href="javascript:history.back()" class="btn-voltar-top-right" title="Voltar">
            <svg viewBox="0 0 24 24"><path d="M15.5 4l-1.42 1.41L18.67 10H4v2h14.67l-4.59 4.59L15.5 20l7-8z"/></svg>
            Voltar
        </a>
    </div>
    <div class="cadastro-cliente-container shadow">
        <h2>Cadastrar Cliente</h2>
        <?php if($erro): ?><div class="msg-erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <?php if($sucesso): ?><div class="msg-sucesso"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>
        <form method="POST" action="" autocomplete="off">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome:</label>
                <input type="text" id="nome" name="nome" maxlength="100" required
                    pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s]+"
                    title="Apenas letras e espaços"
                    value="<?= isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '' ?>"
                    class="form-control">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail:</label>
                <input type="email" id="email" name="email" maxlength="100" required
                    pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$"
                    title="Digite um e-mail válido"
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                    class="form-control">
            </div>
            <div class="mb-3 campo-telefone">
                <label for="telefone" class="form-label">Telefone:</label>
                <input
                    type="text"
                    id="telefone"
                    name="telefone"
                    maxlength="15"
                    required
                    pattern="\(\d{2}\)\s\d{5}-\d{4}"
                    title="Informe no formato (47) 99912-3456"
                    placeholder="(47) 99912-3456"
                    value="<?php
                        $tel = preg_replace('/\D/', '', $_POST['telefone'] ?? "");
                        if(strlen($tel) === 11) {
                            echo '('.substr($tel,0,2).') '.substr($tel,2,5).'-'.substr($tel,7,4);
                        } elseif(strlen($tel) === 10) {
                            echo '('.substr($tel,0,2).') '.substr($tel,2,4).'-'.substr($tel,6,4);
                        } else {
                            echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : "";
                        }
                    ?>"
                    class="form-control"
                    style="width: 100%;max-width:225px;"
                >
                <span class="telefone-hint">Apenas celulares: (DD) 9XXXX-XXXX</span>
            </div>
            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço:</label>
                <input type="text" id="endereco" name="endereco" maxlength="150" required
                    placeholder="Digite o endereço completo"
                    value="<?= isset($_POST['endereco']) ? htmlspecialchars($_POST['endereco']) : '' ?>"
                    class="form-control">
            </div>
            <div style="display: flex; justify-content: space-between;">
                <button type="submit" class="btn-main">Cadastrar Cliente</button>
                <button type="reset" class="btn-limpar">Limpar</button>
            </div>
        </form>
    </div>
</body>
</html>