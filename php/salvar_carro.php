<?php
session_start();

require_once 'conexao.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $placa = isset($_POST['placa']) ? trim($_POST['placa']) : '';
    $marca = isset($_POST['marca']) ? trim($_POST['marca']) : null;
    $modelo = isset($_POST['modelo']) ? trim($_POST['modelo']) : null;
    $cor = isset($_POST['cor']) ? trim($_POST['cor']) : null;
    $id_cliente_fk = isset($_POST['id_cliente_fk']) ? intval($_POST['id_cliente_fk']) : 0;

    if ($placa === '' || $id_cliente_fk <= 0) {
        header("Location: registro-carros.php?erro=Placa e proprietário são obrigatórios.");
        exit;
    }

    $sql = "INSERT INTO Veiculo (id_cliente_fk, placa, marca, modelo, cor) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param('issss', $id_cliente_fk, $placa, $marca, $modelo, $cor);
        if ($stmt->execute()) {
            header("Location: registro-carros.php?msg=Veículo cadastrado com sucesso!");
            exit;
        } else {
            $err = $stmt->error;
            $stmt->close();
            header("Location: registro-carros.php?erro=Erro ao salvar veículo: " . urlencode($err));
            exit;
        }
    } else {
        header("Location: registro-carros.php?erro=Erro na query: " . urlencode($mysqli->error));
        exit;
    }
} else {
    header("Location: registro-carros.php?erro=Método inválido.");
    exit;
}
?>