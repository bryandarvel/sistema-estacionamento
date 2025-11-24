<?php
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : null;
    $cpf = isset($_POST['cpf']) ? trim($_POST['cpf']) : null;

    if ($nome === '') {
        die('Nome é obrigatório.');
    }

    $sql = "INSERT INTO Cliente (nome, telefone, cpf) VALUES (?, ?, ?)";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param('sss', $nome, $telefone, $cpf);
        if ($stmt->execute()) {
            header("Location: ../php/menu.php");
            exit;
        } else {
            $err = $stmt->error;
            $stmt->close();
            die("Erro ao salvar cliente: " . htmlspecialchars($err));
        }
    } else {
        die("Erro na query: " . $mysqli->error);
    }
} else {
    die("Método inválido.");
}