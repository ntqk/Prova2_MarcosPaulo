<?php
// Nome: Marcos Paulo da Silva

session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtendo o nome do perfil do usuário logado
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'] ?? '';

// Definição das permissões por perfil - **APENAS CRUD DE CLIENTE**
$permissoes = [
    1 => [
        "Cadastrar" => ["cadastro_cliente.php"],
        "Buscar"    => ["buscar_cliente.php"],
        "Alterar"   => ["alterar_cliente.php"],
        "Excluir"   => ["excluir_cliente.php"]
    ],
    2 => [
        "Cadastrar" => ["cadastro_cliente.php"],
        "Buscar"    => ["buscar_cliente.php"],
        "Alterar"   => ["alterar_cliente.php"]
    ],
    4 => [
        "Cadastrar" => ["cadastro_cliente.php"],
        "Buscar"    => ["buscar_cliente.php"],
        "Alterar"   => ["alterar_cliente.php"]
    ]
];
// Obtendo as opções disponíveis para o perfil logado
$opcoes_menu = $permissoes[$id_perfil] ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .painel-container {
            margin: 48px auto 0 auto;
            max-width: 800px;
            background: #fff;
            border-radius: 18px;
            border: 2.5px solid #b30000;
            box-shadow: 0 0 34px #b3000017;
            padding: 34px 42px 38px 42px;
            animation: fadein 1s cubic-bezier(.25,1.15,.55,.93);
        }
        @keyframes fadein {
            from { opacity: 0; transform: translateY(38px);}
            to { opacity: 1; transform: none;}
        }
        .painel-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.8px solid #b30000;
            margin-bottom: 28px;
            padding-bottom: 16px;
        }
        .painel-header .saudacao {
            font-size: 1.35em;
            font-weight: 700;
            color: #b30000;
        }
        .painel-header .perfil-badge {
            background: #b30000;
            color: #fff;
            padding: 5px 18px;
            border-radius: 12px;
            margin-left: 20px;
            font-weight: 600;
            letter-spacing: 1px;
            font-size: 1em;
            box-shadow: 0 2px 7px #b3000030;
        }
        .painel-header .logout form {
            margin: 0;
        }
        .painel-header .logout button {
            background: #b30000;
            color: #fff;
            border-radius: 10px;
            padding: 7px 22px;
            font-weight: 600;
            font-size: 1.08em;
            border: none;
            margin-left: 24px;
            box-shadow: 0 2px 7px #b3000030;
            transition: background .19s, color .19s;
        }
        .painel-header .logout button:hover {
            background: #fff;
            color: #b30000;
            border: 1.3px solid #b30000;
        }
        .painel-menu {
            margin: 40px 0 0 0;
            padding: 0;
        }
        .painel-menu .menu {
            justify-content: center;
            gap: 18px;
        }
        .painel-menu .menu > li > a {
            font-size: 1.16em;
            font-weight: 700;
        }
        .painel-menu .dropdown-menu {
            min-width: 180px;
        }
        .painel-info {
            margin: 46px 0 0 0;
            font-size: 1.19em;
            color: #b30000c3;
            font-weight: 600;
            text-align: center;
            letter-spacing: 0.02em;
        }
        @media (max-width: 800px) {
            .painel-container { padding: 15px 2vw; }
            .painel-header { flex-direction: column; gap: 8px;}
            .painel-header .perfil-badge { margin: 12px 0 0 0;}
        }
    </style>
</head>
<body>
    <div class="painel-container shadow">
        <div class="painel-header">
            <div class="saudacao">
                Olá, <?= htmlspecialchars($_SESSION["usuario"]);?>!
                <span class="perfil-badge"><?= htmlspecialchars($nome_perfil);?></span>
            </div>
            <div class="logout">
                <form action="logout.php" method="POST">
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>

        <nav class="painel-menu">
            <ul class="menu">
                <?php foreach ($opcoes_menu as $categoria => $arquivos):?>
                    <li class="dropdown">
                        <a href="#"><?= htmlspecialchars($categoria); ?></a>
                        <ul class="dropdown-menu">
                            <?php foreach ($arquivos as $arquivo): ?>
                                <li>
                                    <a href="<?= htmlspecialchars($arquivo) ?>">
                                        <?= ucfirst(str_replace("_","",basename($arquivo,".php")))?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <div class="painel-info">
            Bem-vindo ao painel principal do sistema.<br>
            Utilize o menu acima para acessar as funções disponíveis de acordo com seu perfil.
        </div>
    </div>
</body>
</html>