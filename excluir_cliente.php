<?php

// Nome: Marcos Paulo da Silva

session_start();
require_once 'conexao.php';

// --- BLINDAGEM SESSÃO E CONEXÃO ---
if (!isset($pdo) || !$pdo) {
    die("Erro: conexão com o banco de dados não foi estabelecida.");
}
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 1) {
    header('Location: principal.php');
    exit();
}

$clientes = [];
$success = $error = "";

// --- EXCLUSÃO DE CLIENTE (BLINDAGEM) ---
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id_cliente = $_GET['id'];
    try {
        $sql = "DELETE FROM cliente WHERE id_cliente = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', intval($id_cliente), PDO::PARAM_INT);
        if ($stmt->execute()) {
            $success = "Cliente excluído com sucesso!";
        } else {
            $error = "Erro ao excluir cliente!";
        }
    } catch (PDOException $e) {
        $error = "Erro ao excluir cliente! [{$e->getCode()}]";
    }
}

// --- BUSCA CLIENTES (ROBUSTO) ---
try {
    $sql = "SELECT * FROM cliente ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erro ao buscar clientes! [{$e->getCode()}]";
    $clientes = [];
}

// --- FUNÇÃO SAFE ---
function safefield($array, $field) {
    return htmlspecialchars(isset($array[$field]) ? $array[$field] : "");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Excluir Cliente</title>
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
        .excluir-cliente-container {
            background: #fff;
            border-radius: 18px;
            border: 2.5px solid #b30000;
            box-shadow: 0 0 44px #18181825, 0 2px 10px #b3000022;
            max-width: 800px;
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
        .error {
            color: #fff;
            background: linear-gradient(90deg, #ff2222 60%, #181818 100%);
            border-radius: 9px;
            padding: 12px;
            margin-bottom: 18px;
            font-weight: 700;
            text-align: center;
            box-shadow: 0 2px 12px #18181833;
        }
        .tabela-centro-container {
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0 16px #18181818;
            background: #fff;
        }
        thead, tr:first-child {
            background: linear-gradient(90deg, #b30000 80%, #181818 100%);
            color: #fff;
        }
        th, td {
            padding: 12px 8px;
            text-align: left;
            vertical-align: middle;
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
            padding: 7px 20px;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            margin-right: 5px;
            font-size: 0.98em;
            transition: background 0.18s, box-shadow 0.18s, color 0.18s;
            box-shadow: 0 2px 10px #b3000022;
            cursor: pointer;
            letter-spacing: 0.5px;
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
            .excluir-cliente-container {
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
            .excluir-cliente-container {
                margin: 22px 0 0 0;
            }
            th, td { padding: 8px 4px; }
        }
    </style>
</head>
<body>
    <div class="header-topo">
        <span class="nome-autor">Marcos Paulo da Silva</span>
        <a href="javascript:history.back()" class="btn-voltar-top-right">
            <svg viewBox="0 0 24 24"><path d="M15.5 4l-1.42 1.41L18.67 10H4v2h14.67l-4.59 4.59L15.5 20l7-8z"/></svg>
            Voltar
        </a>
    </div>
    <div class="excluir-cliente-container shadow">
        <h2>Excluir Cliente</h2>
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($clientes)): ?>
            <div class="tabela-centro-container">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Endereço</th>
                        <th>Ações</th>
                    </tr>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?= safefield($cliente, 'id_cliente') ?></td>
                            <td><?= safefield($cliente, 'nome') ?></td>
                            <td><?= safefield($cliente, 'email') ?></td>
                            <td><?= safefield($cliente, 'telefone') ?></td>
                            <td><?= safefield($cliente, 'endereco') ?></td>
                            <td>
                                <form method="get" action="excluir_cliente.php" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este cliente?');">
                                    <input type="hidden" name="id" value="<?= safefield($cliente, 'id_cliente') ?>">
                                    <button type="submit" class="btn-acao btn-excluir">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php else: ?>
            <div class="nenhum-cliente">Nenhum cliente encontrado.</div>
        <?php endif; ?>
    </div>
</body>
</html>