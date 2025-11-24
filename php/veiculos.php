<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LHD Parking - Veículos Registrados</title>
  <link rel="stylesheet" href="../css/style-veiculos.css">
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
    <section class="container-lista">
      <div class="caixa-lista">
        <h2>Veículos Registrados</h2>
        
        <div class="tabela-container">
          <?php
          require_once 'conexao.php';

         $result = $mysqli->query("SELECT id_veiculo_pk, id_cliente_fk, placa, marca, modelo, cor FROM Veiculo ORDER BY id_veiculo_pk DESC");

if ($result && $result->num_rows > 0) {
  echo "<table class='tabela-veiculos'>";
  echo "<thead><tr><th>ID</th><th>Placa</th><th>Marca</th><th>Modelo</th><th>Cor</th></tr></thead>";
              echo "<tbody>";
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>".htmlspecialchars($row['id_veiculo_pk'])."</td>";
                  echo "<td>".htmlspecialchars($row['placa'])."</td>";
                  echo "<td>".htmlspecialchars($row['marca'])."</td>";
                  echo "<td>".htmlspecialchars($row['modelo'])."</td>";
                  echo "<td>".htmlspecialchars($row['cor'])."</td>";
                  echo "</tr>";
              }
              echo "</tbody></table>";
          } else {
              echo "<p class='mensagem-vazia'>Nenhum veículo cadastrado.</p>";
          }
          $mysqli->close();
          ?>
        </div>

        <a href="../html/relatorio.html" class="link-voltar">← Voltar</a>
      </div>
    </section>
  </main>
</body>
</html>