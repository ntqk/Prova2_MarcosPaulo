<?php

// Nome: Marcos Paulo da Silva

session_start();
require_once 'conexao.php';

// --- SESSÃO E CONEXÃO ---
if (
    !isset($_SESSION['perfil']) ||
    !is_numeric($_SESSION['perfil']) ||
    !in_array(intval($_SESSION['perfil']), [1,2,3,4])
) {
    header('Location: principal.php');
    exit();
}

if (!isset($pdo) || !$pdo) {
    die("Erro: conexão com o banco de dados não foi estabelecida.");
}

// --- VARIÁVEIS E SANITIZAÇÃO ---
$clientes = [];
$busca = isset($_POST['busca']) ? trim($_POST['busca']) : "";
$success = isset($_GET['msg']) ? $_GET['msg'] : "";
$erro = "";

// --- BUSCA NO BANCO (TRY/CATCH) ---
try {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && $busca !== "") {
        // Limite de busca para evitar SQL Injection ou erros de tipo
        if (ctype_digit($busca) && strlen($busca) < 11) { // id_cliente é int
            $sql = "SELECT * FROM cliente WHERE id_cliente = :busca ORDER BY nome ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':busca', intval($busca), PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM cliente WHERE nome LIKE :busca_nome ORDER BY nome ASC";
            $stmt = $pdo->prepare($sql);
            $busca_nome = "%$busca%";
            $stmt->bindValue(':busca_nome', $busca_nome, PDO::PARAM_STR);
        }
        $stmt->execute();
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $sql = "SELECT * FROM cliente ORDER BY nome ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $ex) {
    $erro = "Erro ao buscar clientes: " . htmlspecialchars($ex->getMessage());
    $clientes = [];
}

// --- FUNÇÃO DE SAFE PRINT ---
function safefield($array, $field) {
    return htmlspecialchars(isset($array[$field]) ? $array[$field] : "");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Buscar Cliente</title>
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
        .buscar-container {
            background: #fff;
            border-radius: 18px;
            border: 2.5px solid #b30000;
            box-shadow: 0 0 44px 0 #18181823, 0 2px 10px #b3000022;
            max-width: 900px;
            margin: 110px auto 30px auto;
            padding: 38px 34px 32px 34px;
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
            margin-bottom: 22px;
            letter-spacing: 0.03em;
        }
        .success {
            color: #fff;
            background: linear-gradient(90deg, #b30000 60%, #222 100%);
            border-radius: 9px;
            padding: 12px;
            margin-bottom: 18px;
            font-weight: 700;
            text-align: center;
            box-shadow: 0 2px 12px #18181833;
        }
        .msg-erro {
            color: #fff;
            background: linear-gradient(90deg, #ff2222 60%, #181818 100%);
            border-radius: 9px;
            padding: 12px;
            margin-bottom: 18px;
            font-weight: 700;
            text-align: center;
            box-shadow: 0 2px 12px #18181833;
        }
        .buscar-form label {
            color: #b30000;
            font-weight: 600;
        }
        .buscar-form input[type="text"] {
            border: 1.5px solid #b30000;
            border-radius: 9px;
            font-size: 1.04em;
            background: #fff;
            color: #b30000;
            box-shadow: 0 2px 7px #18181819;
            font-family: inherit;
            font-weight: 500;
            transition: border 0.18s, box-shadow 0.18s, background 0.18s, color 0.18s;
        }
        .buscar-form input[type="text"]:focus {
            border-color: #181818;
            box-shadow: 0 0 11px #18181850;
            outline: none;
            background: #fff;
        }
        .buscar-form button {
            background: #b30000;
            color: #fff;
            font-weight: 700;
            border-radius: 13px;
            padding: 11px 32px;
            border: none;
            font-size: 1.09em;
            margin-left: 10px;
            margin-top: 2px;
            box-shadow: 0 2px 10px #b3000024;
            transition: background .22s, color .22s, transform .18s;
        }
        .buscar-form button:hover {
            background: #fff;
            color: #b30000;
            border: 1.1px solid #b30000;
            transform: translateY(-1.5px) scale(1.03);
        }
        .tabela-centro-container {
            margin-top: 34px;
        }
        table {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0 16px #18181818;
            background: #fff;
        }
        thead {
            background: linear-gradient(90deg, #b30000 80%, #181818 100%);
            color: #fff;
        }
        thead th {
            padding: 13px 8px;
            font-size: 1.01em;
        }
        tbody td {
            padding: 10px 7px;
            color: #181818;
            border-top: 1.5px solid #b3000022;
        }
        tbody tr:nth-child(even) {
            background: #fff0f0;
        }
        tbody tr:hover {
            background: #ffeaea;
        }
        .btn-acao {
            display: inline-block;
            background: #b30000;
            color: #fff;
            padding: 7px 19px;
            border-radius: 13px;
            font-weight: 700;
            border: none;
            margin-right: 7px;
            font-size: 0.98em;
            transition: background 0.18s, box-shadow 0.18s, color 0.18s;
            box-shadow: 0 2px 10px #b3000022;
            cursor: pointer;
            letter-spacing: 0.5px;
        }
        .btn-acao:hover,
        .btn-acao:focus {
            background: #222;
            color: #fff;
            box-shadow: 0 6px 18px #18181833;
        }
        .btn-excluir {
            background: #fff;
            color: #b30000;
            border: 1.2px solid #b30000;
        }
        .btn-excluir:hover {
            background: #b30000;
            color: #fff;
            border: 1.2px solid #222;
        }
        .nenhum-cliente {
            color: #b30000b0;
            font-size: 1.14em;
            margin-top: 32px;
            text-align: center;
            font-weight: 600;
        }
        @media (max-width: 900px) {
            .buscar-container {
                padding: 12px 2vw;
            }
        }
        @media (max-width: 650px) {
            .header-topo .btn-voltar-top-right {
                top: 7px;
                right: 7px;
                padding: 8px 14px;
                font-size: 1em;
            }
            .buscar-container {
                margin: 22px 0 0 0;
            }
        }
    </style>
</head>
<body>
    <div class="header-topo">
        <span class="nome-autor">Marcos Paulo da Silva</span>
        <a href="javascript:history.back()" class="btn-voltar-top-right" title="Voltar">
            <svg viewBox="0 0 24 24"><path d="M15.5 4l-1.42 1.41L18.67 10H4v2h14.67l-4.59 4.59L15.5 20l7-8z"/></svg>
            Voltar
        </a>
    </div>
    <div class="buscar-container shadow">
        <h2>Buscar Cliente</h2>
        <?php if($success): ?>
            <div class="success"><?=htmlspecialchars($success)?></div>
        <?php endif; ?>
        <?php if($erro): ?>
            <div class="msg-erro"><?= $erro ?></div>
        <?php endif; ?>
        <form method="POST" action="" class="buscar-form row g-2 justify-content-center align-items-end">
            <div class="col-12 col-md-auto">
                <label for="busca" class="form-label">ID ou Nome:</label>
                <input type="text" id="busca" name="busca" value="<?=htmlspecialchars($busca)?>" class="form-control" placeholder="Buscar cliente">
            </div>
            <div class="col-12 col-md-auto">
                <button type="submit" class="btn">Pesquisar</button>
            </div>
        </form>
        <?php if(is_array($clientes) && count($clientes)): ?>
            <div class="tabela-centro-container">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Endereço</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?= safefield($cliente, 'id_cliente') ?></td>
                            <td><?= safefield($cliente, 'nome') ?></td>
                            <td><?= safefield($cliente, 'email') ?></td>
                            <td><?= safefield($cliente, 'telefone') ?></td>
                            <td><?= safefield($cliente, 'endereco') ?></td>
                            <td>
                                <a href="alterar_cliente.php?id=<?= urlencode(safefield($cliente, 'id_cliente')) ?>" class="btn-acao">Alterar</a>
                                <form method="get" action="excluir_cliente.php" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este cliente?');">
                                    <input type="hidden" name="id" value="<?= safefield($cliente, 'id_cliente') ?>">
                                    <button type="submit" class="btn-acao btn-excluir">Excluir</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="nenhum-cliente">Nenhum cliente encontrado.</div>
        <?php endif; ?>
    </div>
</body>
</html>