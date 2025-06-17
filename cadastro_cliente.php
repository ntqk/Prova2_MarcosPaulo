<?php
// Nome: Marcos Paulo da Silva

session_start();
require 'conexao.php';

$success = $error = "";
$cliente = ["nome"=>"", "email"=>"", "telefone"=>"", "endereco"=>""];
$id_cliente = $_GET['id'] ?? null;

if (!in_array($_SESSION['perfil'], [1,2,4])) {
    header('Location: principal.php');
    exit();
}

if ($id_cliente && is_numeric($id_cliente) && $_SERVER["REQUEST_METHOD"] !== "POST") {
    $sql = "SELECT * FROM cliente WHERE id_cliente = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_cliente, PDO::PARAM_INT);
    $stmt->execute();
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cliente) {
        $error = "Cliente não encontrado!";
        $cliente = ["nome"=>"", "email"=>"", "telefone"=>"", "endereco"=>""];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_cliente'])) {
    $id_cliente = $_POST['id_cliente'];
    $nome = trim($_POST['nome'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $telefone = preg_replace('/\D/', '', trim($_POST['telefone'] ?? ""));
    $endereco = trim($_POST['endereco'] ?? "");

    if (!$nome || !$email || !$telefone || !$endereco) {
        $error = "Todos os campos são obrigatórios!";
    } elseif (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/u', $nome)) {
        $error = "O nome deve conter apenas letras e espaços!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "E-mail inválido!";
    } elseif (!preg_match('/^\d{10,15}$/', $telefone)) {
        $error = "Telefone deve conter apenas números (10-15 dígitos)!";
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
            $success = "Dados alterados com sucesso!";
            $cliente = ["nome"=>$nome, "email"=>$email, "telefone"=>$telefone, "endereco"=>$endereco];
        } else {
            $error = "Erro ao alterar!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Alterar Cliente</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: #fafbfb;
            min-height: 100vh;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .container-alterar {
            max-width: 405px;
            margin: 48px auto 0 auto;
            background: #fff;
            border-radius: 18px;
            border: 2.5px solid #b30000;
            box-shadow: 0 0 40px #18181824, 0 2px 10px #b3000022;
            padding: 36px 30px 30px 30px;
            animation: fadein 1.1s cubic-bezier(.23,1.51,.55,.93);
        }
        @keyframes fadein {
            from { opacity: 0; transform: translateY(38px);}
            to { opacity: 1; transform: none;}
        }
        .container-alterar h2 {
            color: #b30000;
            font-weight: 700;
            font-size: 1.42em;
            margin-bottom: 28px;
            letter-spacing: 0.01em;
            text-align: center;
        }
        .msg {
            color: #fff;
            background: linear-gradient(90deg, #b30000 65%, #212 100%);
            border-radius: 9px;
            padding: 12px;
            margin-bottom: 16px;
            font-weight: 700;
            text-align: center;
            box-shadow: 0 2px 12px #18181833;
        }
        .msg.error {
            background: linear-gradient(90deg, #ff2222 60%, #181818 100%);
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            color: #b30000;
            font-weight: 600;
            margin-bottom: 4px;
            display: block;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px 8px;
            border: 1.5px solid #b30000b0;
            border-radius: 10px;
            font-size: 1.08em;
            outline: none;
            transition: border .17s;
            background: #f8f8fa;
        }
        input[type="text"]:focus, input[type="email"]:focus {
            border-color: #181818;
            background: #fff;
        }
        button[type="submit"], button[type="reset"] {
            width: 49%;
            background: #b30000;
            color: #fff;
            font-weight: 700;
            border: none;
            border-radius: 11px;
            padding: 12px 0;
            font-size: 1.08em;
            letter-spacing: 1px;
            margin-top: 5px;
            margin-bottom: 8px;
            box-shadow: 0 4px 13px #18181818;
            transition: background .18s, color .18s, border .18s;
        }
        button[type="reset"] {
            background: #fff;
            color: #b30000;
            border: 1.2px solid #b30000;
        }
        button[type="submit"]:hover, button[type="submit"]:focus {
            background: #fff;
            color: #b30000;
            border: 1.2px solid #b30000;
        }
        button[type="reset"]:hover, button[type="reset"]:focus {
            background: #b30000;
            color: #fff;
        }
        .btn-voltar-top-right {
            position: fixed;
            top: 22px;
            right: 32px;
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
            transition: background 0.27s, box-shadow 0.27s, transform 0.22s;
            text-decoration: none;
            outline: none;
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
            .container-alterar { padding: 12px 2vw; }
            .btn-voltar-top-right { top: 8px; right: 10px; padding: 6px 12px; font-size: 1em;}
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
        let value = input.value.replace(/\D/g, "");
        let formatted = "";
        if(value.length < 3) {
            formatted = value;
        } else if(value.length < 7) {
            formatted = "(" + value.substring(0,2) + ") " + value.substring(2);
        } else if(value.length <= 10) {
            formatted = "(" + value.substring(0,2) + ") " + value.substring(2,6) + "-" + value.substring(6,10);
        } else {
            formatted = "(" + value.substring(0,2) + ") " + value.substring(2,7) + "-" + value.substring(7,11);
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
        nome.addEventListener('keypress', somenteLetras);
        nome.addEventListener('paste', function(e) {
            let paste = (e.clipboardData || window.clipboardData).getData('text');
            if (!/^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/.test(paste)) {
                e.preventDefault();
            }
        });

        let tel = document.getElementById('telefone');
        tel.addEventListener('input', formatarTelefone);
        tel.addEventListener('keypress', somenteNumeros);
        tel.addEventListener('paste', function(e) {
            let paste = (e.clipboardData || window.clipboardData).getData('text');
            if (!/^\d+$/.test(paste.replace(/\D/g, ""))) {
                e.preventDefault();
            }
        });

        document.getElementById('form_cliente').addEventListener('submit', validarFormulario);
    }
    </script>
</head>
<body>
    <div class="container-alterar shadow">
        <h2>Alterar Cliente</h2>
        <?php if($success): ?><div class="msg"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <?php if($error): ?><div class="msg error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" action="" autocomplete="off" id="form_cliente">
            <input type="hidden" name="id_cliente" value="<?=htmlspecialchars($id_cliente)?>">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" maxlength="100" required
                    pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s]+"
                    title="Apenas letras e espaços"
                    value="<?=htmlspecialchars($cliente['nome'] ?? "")?>">
            </div>
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" maxlength="100" required
                    pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$"
                    title="Digite um e-mail válido"
                    value="<?=htmlspecialchars($cliente['email'] ?? "")?>">
            </div>
            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="text" name="telefone" id="telefone" maxlength="15" required
                    pattern="\d{10,15}"
                    title="Apenas números (10-15 dígitos)"
                    placeholder="(47) 98765-4321"
                    value="<?php
                        $tel = preg_replace('/\D/', '', $cliente['telefone'] ?? "");
                        if(strlen($tel) === 11) {
                            echo '('.substr($tel,0,2).') '.substr($tel,2,5).'-'.substr($tel,7,4);
                        } elseif(strlen($tel) === 10) {
                            echo '('.substr($tel,0,2).') '.substr($tel,2,4).'-'.substr($tel,6,4);
                        } else {
                            echo htmlspecialchars($cliente['telefone'] ?? "");
                        }
                    ?>">
            </div>
            <div class="form-group">
                <label for="endereco">Endereço:</label>
                <input type="text" name="endereco" id="endereco" maxlength="150" required
                    placeholder="Digite o endereço completo"
                    value="<?=htmlspecialchars($cliente['endereco'] ?? "")?>">
            </div>
            <div style="display: flex; gap: 2%;">
                <button type="submit">Alterar</button>
                <button type="reset" onclick="setTimeout(()=>{document.getElementById('nome').focus()},100)">Limpar</button>
            </div>
        </form>
    </div>
    <a href="javascript:history.back()" class="btn-voltar-top-right">
        <svg viewBox="0 0 24 24"><path d="M15.5 4l-1.42 1.41L18.67 10H4v2h14.67l-4.59 4.59L15.5 20l7-8z"/></svg>
        Voltar
    </a>
</body>
</html>