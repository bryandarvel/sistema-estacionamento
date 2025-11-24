<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexao.php';

$where = "1=1";

$forma = isset($_GET['forma']) ? $_GET['forma'] : "";
$data_ini = isset($_GET['data_ini']) ? $_GET['data_ini'] : "";
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : "";

if ($forma !== "") {
    $where .= " AND forma_pagamento = '".$mysqli->real_escape_string($forma)."'";
}

if ($data_ini !== "" && $data_fim !== "") {
    $where .= " AND DATE(data_pagamento) BETWEEN '$data_ini' AND '$data_fim'";
}

$total = $mysqli->query("
    SELECT SUM(valor) AS total
    FROM Pagamento
    WHERE $where
")->fetch_assoc();

$hoje = ($data_ini === "" && $data_fim === "")
    ? $mysqli->query("SELECT SUM(valor) AS total_hoje 
                        FROM Pagamento 
                        WHERE DATE(data_pagamento) = CURDATE()")->fetch_assoc()
    : ['total_hoje' => null];

$formas = $mysqli->query("
    SELECT forma_pagamento, SUM(valor) AS total
    FROM Pagamento
    WHERE $where
    GROUP BY forma_pagamento
");

$clientes_carros = $mysqli->query("
    SELECT c.nome, COUNT(v.id_veiculo_pk) AS total_veiculos
    FROM Cliente c
    INNER JOIN Veiculo v ON v.id_cliente_fk = c.id_cliente_pk
    GROUP BY c.id_cliente_pk
    HAVING COUNT(v.id_veiculo_pk) > 2
");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - LHD Parking</title>
  <link rel="stylesheet" href="../css/style-dashboard.css">
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
    <div class="container-dashboard">
      <section class="container-filtros">
        <div class="caixa-dashboard">
          <h2>Filtrar Dados</h2>

          <form method="GET" class="formulario-filtros">
            <div class="grupo-campo">
              <label for="forma">Forma de pagamento:</label>
              <select name="forma" id="forma">
                <option value="">Todas</option>
                <option value="Dinheiro" <?= $forma=="Dinheiro"?"selected":"" ?>>Dinheiro</option>
                <option value="Pix" <?= $forma=="Pix"?"selected":"" ?>>Pix</option>
                <option value="Cartão de Crédito" <?= $forma=="Cartão de Crédito"?"selected":"" ?>>Crédito</option>
                <option value="Cartão de Débito" <?= $forma=="Cartão de Débito"?"selected":"" ?>>Débito</option>
              </select>
            </div>

            <div class="grupo-datas">
              <div class="grupo-campo">
                <label for="data_ini">Data início:</label>
                <input type="date" name="data_ini" id="data_ini" value="<?= htmlspecialchars($data_ini) ?>">
              </div>

              <div class="grupo-campo">
                <label for="data_fim">Data fim:</label>
                <input type="date" name="data_fim" id="data_fim" value="<?= htmlspecialchars($data_fim) ?>">
              </div>
            </div>

            <button type="submit" class="botao-filtrar">Aplicar Filtro</button>
          </form>
        </div>
      </section>

      <section class="container-estatisticas">
        <div class="caixa-dashboard">
          <h2>Faturamento</h2>
          <div class="estatisticas">
            <div class="estatistica-item">
              <span class="estatistica-label">Total Geral</span>
              <span class="estatistica-valor">R$ <?= number_format($total['total'] ?? 0, 2, ',', '.') ?></span>
            </div>
            <div class="estatistica-item">
              <span class="estatistica-label">Hoje</span>
              <span class="estatistica-valor">R$ <?= number_format($hoje['total_hoje'] ?? 0, 2, ',', '.') ?></span>
            </div>
          </div>

          <h3>Por forma de pagamento</h3>
          <div class="lista-formas">
            <?php while($f = $formas->fetch_assoc()): ?>
              <div class="forma-item">
                <span class="forma-nome"><?= htmlspecialchars($f['forma_pagamento']) ?></span>
                <span class="forma-valor">R$ <?= number_format($f['total'], 2, ',', '.') ?></span>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
      </section>

      <section class="container-clientes">
        <div class="caixa-dashboard">
          <h2>Clientes com +2 veículos</h2>
          <?php if ($clientes_carros->num_rows > 0): ?>
            <div class="lista-clientes">
              <?php while($cli = $clientes_carros->fetch_assoc()): ?>
                <div class="cliente-item">
                  <span class="cliente-nome"><?= htmlspecialchars($cli['nome']) ?></span>
                  <span class="cliente-veiculos"><?= $cli['total_veiculos'] ?> veículos</span>
                </div>
              <?php endwhile; ?>
            </div>
          <?php else: ?>
            <p class="mensagem-vazia">Nenhum cliente com mais de 2 veículos.</p>
          <?php endif; ?>
        </div>
      </section>
    </div>

    <a href="menu.php" class="link-voltar">Voltar para o Menu</a>
  </main>
</body>
</html>