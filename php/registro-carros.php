<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LHD Parking - Registro de Carros</title>
  <link rel="stylesheet" href="../css/style-registro-carros.css">
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
        <h2>Registro de Carro</h2>

        <?php
        if (isset($_GET['msg'])) {
            echo "<p class='mensagem-sucesso'>".htmlspecialchars($_GET['msg'])."</p>";
        }
        if (isset($_GET['erro'])) {
            echo "<p class='mensagem-erro'>".htmlspecialchars($_GET['erro'])."</p>";
        }

        require_once 'conexao.php';
        $clientes = $mysqli->query("SELECT id_cliente_pk, nome, cpf FROM Cliente ORDER BY nome ASC");
        ?>

        <form action="salvar_carro.php" method="post">
          <div class="grupo-campo">
            <label for="id_cliente_fk">Cliente:</label>
            <select id="id_cliente_fk" name="id_cliente_fk" required>
              <option value="">Selecione um cliente</option>
              <?php
              if ($clientes && $clientes->num_rows > 0) {
                  while ($cli = $clientes->fetch_assoc()) {
                      echo '<option value="'.htmlspecialchars($cli['id_cliente_pk']).'">'.
                            htmlspecialchars($cli['nome']).' - CPF: '.htmlspecialchars($cli['cpf']).'</option>';
                  }
              } else {
                  echo '<option value="">Nenhum cliente cadastrado</option>';
              }
              ?>
            </select>
          </div>

          <p class="aviso">*Aviso: Caso não houver vagas, não é possível prosseguir com o pagamento.</p>

          <div class="grupo-campo">
            <label for="placa">Placa:</label>
            <input type="text" id="placa" name="placa" required>
          </div>

          <div class="grupo-campo">
            <label for="marca">Marca:</label>
            <input type="text" id="marca" name="marca" required>
          </div>

          <div class="grupo-campo">
            <label for="modelo">Modelo:</label>
            <input type="text" id="modelo" name="modelo" required>
          </div>

          <div class="grupo-campo">
            <label for="cor">Cor:</label>
            <input type="text" id="cor" name="cor" required>
          </div>

          <button type="submit" class="botao-enviar">Salvar</button>
        </form>

        <a href="menu.php" class="link-voltar">← Voltar para o Menu</a>
      </div>
    </section>
  </main>
</body>
</html>