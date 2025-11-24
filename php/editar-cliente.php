<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: gerenciar-cliente.php");
    exit;
}

$stmt = $mysqli->prepare("SELECT id_cliente_pk, nome, telefone, cpf FROM Cliente WHERE id_cliente_pk = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

if (!$cliente) {
    header("Location: gerenciar-cliente.php");
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $telefone = trim($_POST['telefone']);
    $cpf = trim($_POST['cpf']);

    if (empty($nome) || empty($telefone) || empty($cpf)) {
        $erro = 'Todos os campos são obrigatórios.';
    } else {
        $update = $mysqli->prepare("UPDATE Cliente SET nome = ?, telefone = ?, cpf = ? WHERE id_cliente_pk = ?");
        $update->bind_param('sssi', $nome, $telefone, $cpf, $id);

        if ($update->execute()) {
            $sucesso = 'Cliente atualizado com sucesso!';
            $cliente['nome'] = $nome;
            $cliente['telefone'] = $telefone;
            $cliente['cpf'] = $cpf;
        } else {
            $erro = 'Erro ao atualizar cliente: ' . $update->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Cliente - LHD Parking</title>
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
        <h2>Editar Cliente</h2>

        <?php if ($erro): ?>
          <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <?php if ($sucesso): ?>
          <p class="mensagem-sucesso"><?= htmlspecialchars($sucesso) ?></p>
        <?php endif; ?>

        <form method="post">
          <div class="grupo-campo">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($cliente['nome']) ?>" required>
          </div>

          <div class="grupo-campo">
            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($cliente['telefone']) ?>" required>
          </div>

          <div class="grupo-campo">
            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" value="<?= htmlspecialchars($cliente['cpf']) ?>" required>
          </div>

          <button type="submit" class="botao-salvar">Salvar Alterações</button>
        </form>

        <a href="gerenciar-cliente.php" class="link-voltar">← Voltar para Gerenciar Clientes</a>
      </div>
    </section>
  </main>
</body>
</html>