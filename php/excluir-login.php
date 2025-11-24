<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexao.php';

$id = $_GET['id'] ?? null;

if ($id) {
    if ($id == $_SESSION['login_id']) {
        $_SESSION['erro'] = "Você não pode excluir sua própria conta!";
    } else {
        $stmt = $mysqli->prepare("DELETE FROM Login WHERE id_login_pk = ?");
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            $_SESSION['mensagem'] = "Login excluído com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao excluir login: " . $stmt->error;
        }
    }
}

header("Location: gerenciar-login.php");
exit;