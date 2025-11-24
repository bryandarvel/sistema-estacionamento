<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LHD Parking - Criar Conta</title>
  <link rel="stylesheet" href="../css/style-criar-conta.css">
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
    <section class="container-cadastro">
      <div class="caixa-cadastro">
        <h2>Criar Conta</h2>

        <?php
        if (isset($_GET['msg'])) {
            echo "<p class='mensagem-sucesso'>".htmlspecialchars($_GET['msg'])."</p>";
        }
        if (isset($_GET['erro'])) {
            echo "<p class='mensagem-erro'>".htmlspecialchars($_GET['erro'])."</p>";
        }
        ?>

        <form action="criar_conta.php" method="post">
          <div class="grupo-campo">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
          </div>

          <div class="grupo-campo">
            <label for="sobrenome">Sobrenome:</label>
            <input type="text" id="sobrenome" name="sobrenome" required>
          </div>

          <div class="grupo-campo">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
          </div>

          <div class="grupo-campo">
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
          </div>

          <div class="grupo-campo">
            <label for="confirmar">Confirmar senha:</label>
            <input type="password" id="confirmar" name="confirmar" required>
          </div>

          <button type="submit" class="botao-criar-conta">Criar Conta</button>
        </form>

        <a href="../php/login.php" class="link-voltar">← Voltar para o Login</a>
      </div>
    </section>
  </main>
</body>
</html>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'conexao.php';
    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $confirmar = trim($_POST['confirmar']);

    if ($senha !== $confirmar) {
        header("Location: criar_conta.php?erro=As senhas não conferem.");
        exit;
    }

    $check = $mysqli->prepare("SELECT id_login_pk FROM Login WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    header("Location: criar_conta.php?erro=Email já cadastrado. Faça login.");
    exit;
}
$check->close();

$sql = "INSERT INTO Login (nome, sobrenome, email, senha) VALUES (?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ssss", $nome, $sobrenome, $email, $senha_hash);

if ($stmt->execute()) {
    header("Location: login.php?msg=Conta criada com sucesso!");
    exit;
} else {
    header("Location: criar_conta.php?erro=Erro ao criar conta.");
    exit;
}

    $sql = "INSERT INTO Login (nome, sobrenome, email, senha) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssss", $nome, $sobrenome, $email, $senha_hash);

    if ($stmt->execute()) {
        header("Location: login.php?msg=Conta criada com sucesso!");
        exit;
    } else {
        header("Location: criar_conta.php?erro=Erro ao criar conta. Email já existente?");
        exit;
    }
}
?>