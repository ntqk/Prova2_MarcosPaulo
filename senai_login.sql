<?php

// Nome: Marcos Paulo da Silva

session_start();
require_once 'conexao.php';

// BLINDAGEM: conexão e sessão ADM
if (!isset($pdo) || !$pdo) {
    die("Erro: conexão com o banco de dados não foi estabelecida.");
}
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso negado!'); window.location.href='principal.php';</script>";
    exit();
}

// PROCESSAMENTO DA ALTERAÇÃO
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['id_cliente'])) {
    $id_cliente = $_POST['id_cliente'];
    $nome = trim($_POST['nome'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? "");
    $endereco = trim($_POST['endereco'] ?? "");

    // Validações mínimas (pode expandir se quiser)
    if (!$nome || !$email || !$telefone || !$endereco) {
        echo "<script>alert('Todos os campos são obrigatórios!'); window.location.href='alterar_cliente.php';</script>";
        exit();
    }
    if (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/u', $nome)) {
        echo "<script>alert('O nome deve conter apenas letras e espaços!'); window.location.href='alterar_cliente.php';</script>";
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('E-mail inválido!'); window.location.href='alterar_cliente.php';</script>";
        exit();
    }
    if (!preg_match('/^\d{11}$/', $telefone)) {
        echo "<script>alert('Telefone deve conter exatamente 11 dígitos, ex: 47999123456!'); window.location.href='alterar_cliente.php';</script>";
        exit();
    }

    try {
        $sql = "UPDATE cliente SET nome = :nome, email = :email, telefone = :telefone, endereco = :endereco WHERE id_cliente = :id_cliente";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('Cliente alterado com sucesso!'); window.location.href='alterar_cliente.php';</script>";
        } else {
            echo "<script>alert('Erro ao alterar cliente!'); window.location.href='alterar_cliente.php';</script>";
        }
    } catch (PDOException $e) {
        if ($e->getCode() === "23000") {
            echo "<script>alert('E-mail já cadastrado para outro cliente!'); window.location.href='alterar_cliente.php';</script>";
        } else {
            echo "<script>alert('Erro ao alterar cliente! [{$e->getCode()}]'); window.location.href='alterar_cliente.php';</script>";
        }
    }
} else {
    echo "<script>alert('Requisição inválida!'); window.location.href='alterar_cliente.php';</script>";
}
?>