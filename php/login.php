<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'conexao.php';

    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    $sql = "SELECT id_login_pk, nome, senha FROM Login WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $nome, $senha_db);

    if ($stmt->fetch()) {
        if (password_verify($senha, $senha_db)) {
            $_SESSION['login_id'] = $id;
            $_SESSION['login_nome'] = $nome;

            header("Location: menu.php");
            exit;
        } else {
            header("Location: login.php?erro=E-mail ou senha inválidos.");
            exit;
        }
    } else {
        header("Location: login.php?erro=E-mail ou senha inválidos.");
        exit;
    }

    $stmt->close();
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LHD Parking - Login</title>
  <link rel="stylesheet" href="../css/style-login.css">
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
    <section class="container-login">
      <div class="caixa-login">
        <h2>Login</h2>

        <?php
        if (isset($_GET['msg'])) {
            echo "<p class='mensagem-sucesso'>".htmlspecialchars($_GET['msg'])."</p>";
        }
        if (isset($_GET['erro'])) {
            echo "<p class='mensagem-erro'>".htmlspecialchars($_GET['erro'])."</p>";
        }
        ?>

        <form action="login.php" method="post">
          <div class="grupo-campo">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
          </div>

          <div class="grupo-campo">
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
          </div>

          <button type="submit" class="botao-entrar">Entrar</button>
        </form>

        <p class="texto-link">Não tem conta?
          <a href="criar_conta.php" class="link">Criar conta</a>
        </p>
        <a href="../html/index.html" class="link-voltar">← Voltar para o Início</a>
      </div>
    </section>
  </main>
</body>
</html>