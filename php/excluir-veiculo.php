<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexao.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $mysqli->prepare("DELETE FROM Veiculo WHERE id_veiculo_pk = ?");
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Veículo excluído com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao excluir veículo: " . $stmt->error;
    }
}

header("Location: gerenciar-veiculo.php");
exit;