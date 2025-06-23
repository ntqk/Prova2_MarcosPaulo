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

// Mensagem de sucesso via GET após PRG
if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1) {
    $sucesso = "Cliente alterado com sucesso!";
}

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
    $telefone = preg_replace('/\D/', '', trim($_POST['telefone'] ?? "")); // Remove máscara
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
        $sql = "UPDATE cliente SET nome = :nome, email = :email, telefone = :telefone, endereco = :endereco WHERE id_cliente = :id_cliente";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':telefone' => $telefone,
            ':endereco' => $endereco,
            ':id_cliente' => $id_cliente
        ])) {
            // PRG pattern: redireciona para a página principal com mensagem de sucesso
            header("Location: principal.php?sucesso=1");
            exit();
        } else {
            $erro = "Erro ao alterar cliente!";
        }
    }
    // Se erro, mantém cliente preenchido
    $cliente = ["id_cliente"=>$id_cliente, "nome"=>$nome, "email"=>$email, "telefone"=>$telefone, "endereco"=>$endereco];
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
        .header-topo {
            width: 100vw;
            min-height: 68px;
            /* Cor cinza clara e elegante */
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
        }
        .alterar-cliente-container {
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
        @media (max-width: 500px) {
            .alterar-cliente-container { padding: 15px 5vw; }
            .header-topo { font-size: 1em; min-height: 56px;}
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
    function validarEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    function validarFormulario(e) {
        var email = document.getElementById('email').value.trim();
        var telefone = document.getElementById('telefone').value.replace(/\D/g, "");
        if (!validarEmail(email)) {
            alert('E-mail inválido!');
            document.getElementById('email').focus();
            e.preventDefault();
            return false;
        }
        if (telefone.length !== 11) {
            alert('Telefone deve conter exatamente 11 dígitos (Ex: 47999123456)');
            document.getElementById('telefone').focus();
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
        let f = document.getElementById('form_alterar_cliente');
        if(f) f.addEventListener('submit', validarFormulario);
    }
    </script>
</head>
<body>
    <div class="header-topo">
        <span class="nome-autor">Marcos Paulo da Silva</span>
    </div>
    <div class="alterar-cliente-container shadow">
        <h2>Alterar Cliente</h2>
        <?php if($erro): ?><div class="msg-erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <?php if($sucesso): ?><div class="msg-sucesso"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>

        <form action="" method="POST" class="mb-4">
            <div class="mb-3">
                <label for="busca_cliente" class="form-label">ID ou Nome do cliente:</label>
                <input type="text" id="busca_cliente" name="busca_cliente" class="form-control" required>
            </div>
            <button type="submit" class="btn-main">Buscar</button>
        </form>

        <?php if ($cliente): ?>
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
                        $tel = preg_replace('/\D/', '', $cliente['telefone'] ?? "");
                        if(strlen($tel) === 11) {
                            echo '('.substr($tel,0,2).') '.substr($tel,2,5).'-'.substr($tel,7,4);
                        } elseif(strlen($tel) === 10) {
                            echo '('.substr($tel,0,2).') '.substr($tel,2,4).'-'.substr($tel,6,4);
                        } else {
                            echo htmlspecialchars($cliente['telefone'] ?? "");
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
                    value="<?= htmlspecialchars($cliente['endereco']) ?>"
                    class="form-control">
            </div>
            <button type="submit" class="btn-main">Salvar Alterações</button>
            <button type="button" class="btn-cancelar" onclick="window.location.href='principal.php'">Cancelar</button>
        </form>
        <?php endif; ?>
    </div>
    <a href="principal.php" class="btn-voltar-top-right" title="Voltar">
        <svg viewBox="0 0 24 24"><path d="M15.5 4l-1.42 1.41L18.67 10H4v2h14.67l-4.59 4.59L15.5 20l7-8z"/></svg>
        Voltar
    </a>
</body>
</html>