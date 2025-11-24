<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexao.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt_veiculos = $mysqli->prepare("DELETE FROM Veiculo WHERE id_cliente_fk = ?");
    $stmt_veiculos->bind_param('i', $id);
    $stmt_veiculos->execute();
    
    $stmt_cliente = $mysqli->prepare("DELETE FROM Cliente WHERE id_cliente_pk = ?");
    $stmt_cliente->bind_param('i', $id);
    
    if ($stmt_cliente->execute()) {
        $_SESSION['mensagem'] = "Cliente e veículos associados excluídos com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao excluir cliente: " . $stmt_cliente->error;
    }
}

header("Location: gerenciar-cliente.php");
exit;