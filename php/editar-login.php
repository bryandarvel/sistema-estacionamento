<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: gerenciar-login.php");
    exit;
}

$stmt = $mysqli->prepare("SELECT id_login_pk, nome, sobrenome, email FROM Login WHERE id_login_pk = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$login = $result->fetch_assoc();

if (!$login) {
    header("Location: gerenciar-login.php");
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (empty($nome) || empty($sobrenome) || empty($email)) {
        $erro = 'Nome, sobrenome e email são obrigatórios.';
    } else {
        if (!empty($senha)) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $update = $mysqli->prepare("UPDATE Login SET nome = ?, sobrenome = ?, email = ?, senha = ? WHERE id_login_pk = ?");
            $update->bind_param('ssssi', $nome, $sobrenome, $email, $senha_hash, $id);
        } else {
            $update = $mysqli->prepare("UPDATE Login SET nome = ?, sobrenome = ?, email = ? WHERE id_login_pk = ?");
            $update->bind_param('sssi', $nome, $sobrenome, $email, $id);
        }

        if ($update->execute()) {
            $sucesso = 'Login atualizado com sucesso!';
            $login['nome'] = $nome;
            $login['sobrenome'] = $sobrenome;
            $login['email'] = $email;
        } else {
            $erro = 'Erro ao atualizar login: ' . $update->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Login - LHD Parking</title>
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
        <h2>Editar Login</h2>

        <?php if ($erro): ?>
          <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <?php if ($sucesso): ?>
          <p class="mensagem-sucesso"><?= htmlspecialchars($sucesso) ?></p>
        <?php endif; ?>

        <form method="post">
          <div class="grupo-campo">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($login['nome']) ?>" required>
          </div>

          <div class="grupo-campo">
            <label for="sobrenome">Sobrenome:</label>
            <input type="text" id="sobrenome" name="sobrenome" value="<?= htmlspecialchars($login['sobrenome']) ?>" required>
          </div>

          <div class="grupo-campo">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($login['email']) ?>" required>
          </div>

          <div class="grupo-campo">
            <label for="senha">Nova Senha (deixe em branco para manter a atual):</label>
            <input type="password" id="senha" name="senha">
          </div>

          <button type="submit" class="botao-salvar">Salvar Alterações</button>
        </form>

        <a href="gerenciar-login.php" class="link-voltar">← Voltar para Gerenciar Logins</a>
      </div>
    </section>
  </main>
</body>
</html>