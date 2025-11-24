<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LHD Parking - Menu</title>
  <link rel="stylesheet" href="../css/style-menu.css">
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
    <section class="container-menu">
      <div class="caixa-menu">
        <h2>Menu Principal</h2>
        <p class="mensagem-boas-vindas">Bem-vindo, <?php echo htmlspecialchars($_SESSION['login_nome']); ?>!</p>

        <div class="botoes">
          <a href="../php/pagamento.php" class="botao">Pagamento</a>
          <a href="../html/relatorio.html" class="botao">Relatório</a>
          <a href="../php/registro-carros.php" class="botao">Registrar Carros</a>
          <a href="../php/registro-clientes.php" class="botao">Registrar Clientes</a>
          <a href="../html/menu-gerenciar.html" class="botao">Gerenciar Dados</a>
          <a href="../php/registra-movimentacao.php" class="botao">Registrar uma Movimentação</a>

        </div>

        <a href="../php/logout.php" class="link-voltar">← Sair</a>
      </div>
    </section>
  </main>
</body>
</html>