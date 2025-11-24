<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexao.php';

$dataFiltro = isset($_GET['data']) ? $_GET['data'] : null;

$sql_clientes = "SELECT id_cliente_pk, nome, telefone, cpf FROM Cliente ORDER BY nome ASC";
$clientes = $mysqli->query($sql_clientes);

$sql_veiculos = "SELECT v.id_veiculo_pk, v.placa, v.marca, v.modelo, v.cor, c.nome AS cliente_nome
                 FROM Veiculo v
                 INNER JOIN Cliente c ON v.id_cliente_fk = c.id_cliente_pk
                 ORDER BY v.id_veiculo_pk DESC";
$veiculos = $mysqli->query($sql_veiculos);

$sql_mov = "SELECT m.id_movimentacao_pk, v.placa, vg.numero AS vaga_numero, vg.setor,
                   m.data_entrada, m.data_saida
            FROM Movimentacao m
            INNER JOIN Veiculo v ON m.id_veiculo_fk = v.id_veiculo_pk
            LEFT JOIN Vaga vg ON m.id_vaga_fk = vg.id_vaga_pk";

if ($dataFiltro) {
    $sql_mov .= " WHERE DATE(m.data_entrada) = ?";
}
$sql_mov .= " ORDER BY m.data_entrada DESC";

$stmtMov = $mysqli->prepare($sql_mov);
if ($dataFiltro) $stmtMov->bind_param("s", $dataFiltro);
$stmtMov->execute();
$movimentacoes = $stmtMov->get_result();

$sql_pag = "SELECT p.id_pagamento_pk, v.placa, p.valor, p.forma_pagamento, p.data_pagamento
            FROM Pagamento p
            INNER JOIN Movimentacao m ON p.id_movimentacao_fk = m.id_movimentacao_pk
            INNER JOIN Veiculo v ON m.id_veiculo_fk = v.id_veiculo_pk";
if ($dataFiltro) {
    $sql_pag .= " WHERE DATE(p.data_pagamento) = ?";
}
$sql_pag .= " ORDER BY p.data_pagamento DESC";

$stmtPag = $mysqli->prepare($sql_pag);
if ($dataFiltro) $stmtPag->bind_param("s", $dataFiltro);
$stmtPag->execute();
$pagamentos = $stmtPag->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LHD Parking - Relatório Geral</title>
  <link rel="stylesheet" href="../css/style-relatorio-geral.css">
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
    <section class="container-relatorio">
      <div class="caixa-relatorio">
        <h2>Relatório Geral</h2>

        <form method="get" class="filtro-data">
          <label for="data">Filtrar por data:</label>
          <input type="date" id="data" name="data" value="<?= htmlspecialchars($dataFiltro ?? '') ?>">
          <button type="submit" class="botao-filtrar">Filtrar</button>
          <?php if ($dataFiltro): ?>
            <a href="relatorio-geral.php" class="link-limpar">Limpar</a>
          <?php endif; ?>
        </form>

        <div class="secao-relatorio">
          <h3>Movimentações</h3>
          <div class="tabela-container">
            <table class="tabela-relatorio">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Placa</th>
                  <th>Vaga</th>
                  <th>Setor</th>
                  <th>Entrada</th>
                  <th>Saída</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($movimentacoes && $movimentacoes->num_rows > 0): ?>
                  <?php while ($m = $movimentacoes->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($m['id_movimentacao_pk']) ?></td>
                      <td><?= htmlspecialchars($m['placa']) ?></td>
                      <td><?= htmlspecialchars($m['vaga_numero'] ?? '—') ?></td>
                      <td><?= htmlspecialchars($m['setor'] ?? '—') ?></td>
                      <td><?= htmlspecialchars($m['data_entrada']) ?></td>
                      <td><?= htmlspecialchars($m['data_saida'] ?? '—') ?></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="6">Nenhuma movimentação encontrada<?= $dataFiltro ? " para $dataFiltro" : "" ?>.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="secao-relatorio">
          <h3>Pagamentos realizados</h3>
          <div class="tabela-container">
            <table class="tabela-relatorio">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Placa</th>
                  <th>Valor (R$)</th>
                  <th>Forma</th>
                  <th>Data</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($pagamentos && $pagamentos->num_rows > 0): ?>
                  <?php while ($p = $pagamentos->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($p['id_pagamento_pk']) ?></td>
                      <td><?= htmlspecialchars($p['placa']) ?></td>
                      <td><?= number_format($p['valor'], 2, ',', '.') ?></td>
                      <td><?= htmlspecialchars($p['forma_pagamento'] ?? '—') ?></td>
                      <td><?= htmlspecialchars($p['data_pagamento'] ?? '—') ?></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="5">Nenhum pagamento encontrado<?= $dataFiltro ? " para $dataFiltro" : "" ?>.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="secao-relatorio">
          <h3>Clientes registrados</h3>
          <div class="tabela-container">
            <table class="tabela-relatorio">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nome</th>
                  <th>Telefone</th>
                  <th>CPF</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($clientes && $clientes->num_rows > 0): ?>
                  <?php while ($c = $clientes->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($c['id_cliente_pk']) ?></td>
                      <td><?= htmlspecialchars($c['nome']) ?></td>
                      <td><?= htmlspecialchars($c['telefone'] ?? '—') ?></td>
                      <td><?= htmlspecialchars($c['cpf'] ?? '—') ?></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="4">Nenhum cliente registrado.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="secao-relatorio">
          <h3>Carros registrados</h3>
          <div class="tabela-container">
            <table class="tabela-relatorio">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Placa</th>
                  <th>Marca</th>
                  <th>Modelo</th>
                  <th>Cor</th>
                  <th>Cliente</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($veiculos && $veiculos->num_rows > 0): ?>
                  <?php while ($v = $veiculos->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($v['id_veiculo_pk']) ?></td>
                      <td><?= htmlspecialchars($v['placa']) ?></td>
                      <td><?= htmlspecialchars($v['marca'] ?? '—') ?></td>
                      <td><?= htmlspecialchars($v['modelo'] ?? '—') ?></td>
                      <td><?= htmlspecialchars($v['cor'] ?? '—') ?></td>
                      <td><?= htmlspecialchars($v['cliente_nome'] ?? '—') ?></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="6">Nenhum carro registrado.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <a href="../html/relatorio.html" class="link-voltar">← Voltar</a>
      </div>
    </section>
  </main>
</body>
</html>