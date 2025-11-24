<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LHD Parking - Registro de Clientes</title>
  <link rel="stylesheet" href="../css/style-registro-clientes.css">
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
    <section class="container-formulario">
      <div class="caixa-formulario">
        <h2>Registrar Cliente</h2>

        <?php
        if (isset($_GET['msg'])) {
            echo "<p class='mensagem-sucesso'>".htmlspecialchars($_GET['msg'])."</p>";
        }
        if (isset($_GET['erro'])) {
            echo "<p class='mensagem-erro'>".htmlspecialchars($_GET['erro'])."</p>";
        }
        ?>

        <form action="salvar_clientes.php" method="post">
          <div class="grupo-campo">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
          </div>

          <div class="grupo-campo">
            <label for="telefone">Telefone:</label>
            <input type="tel" id="telefone" name="telefone" required placeholder="(xx) xxxxx-xxxx">
          </div>

          <div class="grupo-campo">
            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" required placeholder="000.000.000-00">
          </div>

          <button type="submit" class="botao-enviar">Enviar</button>
        </form>
        <a href="menu.php" class="link-voltar">← Voltar para o Menu</a>
      </div>
    </section>
  </main>
</body>
</html>