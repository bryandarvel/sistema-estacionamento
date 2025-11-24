<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: gerenciar-veiculo.php");
    exit;
}

$stmt = $mysqli->prepare("SELECT id_veiculo_pk, placa, marca, modelo, cor, id_cliente_fk FROM Veiculo WHERE id_veiculo_pk = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$veiculo = $result->fetch_assoc();

if (!$veiculo) {
    header("Location: gerenciar-veiculo.php");
    exit;
}

$clientes = $mysqli->query("SELECT id_cliente_pk, nome FROM Cliente ORDER BY nome");

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $placa = trim($_POST['placa']);
    $marca = trim($_POST['marca']);
    $modelo = trim($_POST['modelo']);
    $cor = trim($_POST['cor']);
    $id_cliente_fk = intval($_POST['id_cliente_fk']);

    if (empty($placa) || empty($marca) || empty($modelo) || empty($cor) || $id_cliente_fk <= 0) {
        $erro = 'Todos os campos são obrigatórios.';
    } else {
        $update = $mysqli->prepare("UPDATE Veiculo SET placa = ?, marca = ?, modelo = ?, cor = ?, id_cliente_fk = ? WHERE id_veiculo_pk = ?");
        $update->bind_param('ssssii', $placa, $marca, $modelo, $cor, $id_cliente_fk, $id);

        if ($update->execute()) {
            $sucesso = 'Veículo atualizado com sucesso!';
            $veiculo['placa'] = $placa;
            $veiculo['marca'] = $marca;
            $veiculo['modelo'] = $modelo;
            $veiculo['cor'] = $cor;
            $veiculo['id_cliente_fk'] = $id_cliente_fk;
        } else {
            $erro = 'Erro ao atualizar veículo: ' . $update->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Veículo - LHD Parking</title>
  <link rel="stylesheet" href="../css/style-editar.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="fundo-sobreposicao"></div>
  
  <header>
    <div class="conteudo-cabecalho">
      <h1>LHD Parking</h1>
      <p>Soluções Inteligentes em Estacionamento</p>
    </div>
  </header>

  <main>
    <section class="container-editar">
      <div class="caixa-editar">
        <h2>Editar Veículo</h2>

        <?php if ($erro): ?>
          <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <?php if ($sucesso): ?>
          <p class="mensagem-sucesso"><?= htmlspecialchars($sucesso) ?></p>
        <?php endif; ?>

        <form method="post">
          <div class="grupo-campo">
            <label for="placa">Placa:</label>
            <input type="text" id="placa" name="placa" value="<?= htmlspecialchars($veiculo['placa']) ?>" required>
          </div>

          <div class="grupo-campo">
            <label for="marca">Marca:</label>
            <input type="text" id="marca" name="marca" value="<?= htmlspecialchars($veiculo['marca']) ?>" required>
          </div>

          <div class="grupo-campo">
            <label for="modelo">Modelo:</label>
            <input type="text" id="modelo" name="modelo" value="<?= htmlspecialchars($veiculo['modelo']) ?>" required>
          </div>

          <div class="grupo-campo">
            <label for="cor">Cor:</label>
            <input type="text" id="cor" name="cor" value="<?= htmlspecialchars($veiculo['cor']) ?>" required>
          </div>

          <div class="grupo-campo">
            <label for="id_cliente_fk">Cliente:</label>
            <select name="id_cliente_fk" id="id_cliente_fk" required>
              <option value="">Selecione um cliente</option>
              <?php while ($c = $clientes->fetch_assoc()): ?>
                <option value="<?= $c['id_cliente_pk'] ?>" <?= $c['id_cliente_pk'] == $veiculo['id_cliente_fk'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($c['nome']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <button type="submit" class="botao-salvar">Salvar Alterações</button>
        </form>

        <a href="gerenciar-veiculo.php" class="link-voltar">← Voltar para Gerenciar Veículos</a>
      </div>
    </section>
  </main>
</body>
</html>