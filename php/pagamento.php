<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexao.php';

$veiculos = $mysqli->query("SELECT v.id_veiculo_pk, v.placa, c.nome 
                            FROM Veiculo v 
                            INNER JOIN Cliente c ON v.id_cliente_fk = c.id_cliente_pk
                            ORDER BY v.placa ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_veiculo_fk = intval($_POST['id_veiculo_fk']);
    $valor = floatval($_POST['valor']);
    $forma_pagamento = trim($_POST['forma_pagamento']);

    if ($id_veiculo_fk <= 0 || $valor <= 0 || $forma_pagamento === '') {
        $erro = "Todos os campos são obrigatórios.";
    } else {
        $movQuery = $mysqli->prepare("SELECT id_movimentacao_pk, id_vaga_fk FROM Movimentacao WHERE id_veiculo_fk = ? AND data_saida IS NULL LIMIT 1");
        $movQuery->bind_param('i', $id_veiculo_fk);
        $movQuery->execute();
        $movQuery->bind_result($id_movimentacao_fk, $id_vaga_fk);

        if ($movQuery->fetch()) {
            $movQuery->close();

            $sql = "INSERT INTO Pagamento (id_movimentacao_fk, valor, data_pagamento, forma_pagamento)
                    VALUES (?, ?, NOW(), ?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ids', $id_movimentacao_fk, $valor, $forma_pagamento);
            $stmt->execute();

            $mysqli->query("UPDATE Movimentacao SET data_saida = NOW() WHERE id_movimentacao_pk = $id_movimentacao_fk");
            $mysqli->query("UPDATE Vaga SET status = 'livre' WHERE id_vaga_pk = $id_vaga_fk");

            $sucesso = "Pagamento registrado com sucesso!";
        } else {
            $erro = "Nenhuma movimentação ativa encontrada para este veículo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LHD Parking - Pagamento</title>
  <link rel="stylesheet" href="../css/style-pagamento.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <script>
  function atualizarFormaPagamento() {
    const forma = document.getElementById('forma_pagamento').value;
    const areaPix = document.getElementById('area-pix');
    const areaCartao = document.getElementById('area-cartao');
    const areaOutros = document.getElementById('area-outros');
    const valor = document.getElementById('valor').value || 0;
    const qr = document.getElementById('pix-qr');

    areaPix.style.display = 'none';
    areaCartao.style.display = 'none';
    areaOutros.style.display = 'none';

    if (forma === 'Pix') {
      areaPix.style.display = 'block';
      qr.src = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=LHD%20Parking%20-%20Pagamento%20R$" + valor + "%20via%20Pix%20(chavepix@lhdparking.com)";
    } else if (forma === 'Cartão de Crédito' || forma === 'Cartão de Débito') {
      areaCartao.style.display = 'block';
    } else if (forma === 'Outros') {
      areaOutros.style.display = 'block';
    }
  }
  </script>
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
    <section class="container-pagamento">
      <div class="caixa-pagamento">
        <h2>Pagamento</h2>

        <?php if (isset($erro)): ?>
          <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
        <?php elseif (isset($sucesso)): ?>
          <p class="mensagem-sucesso"><?= htmlspecialchars($sucesso) ?></p>
        <?php endif; ?>

        <form method="post" action="">
          <div class="grupo-campo">
            <label for="id_veiculo_fk">Placa / Cliente:</label>
            <select name="id_veiculo_fk" id="id_veiculo_fk" required>
              <option value="">Selecione um veículo</option>
              <?php while ($v = $veiculos->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($v['id_veiculo_pk']) ?>">
                  <?= htmlspecialchars($v['placa']) ?> - <?= htmlspecialchars($v['nome']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="grupo-campo">
            <label for="valor">Valor (R$):</label>
            <input type="number" step="0.01" name="valor" id="valor" required oninput="atualizarFormaPagamento()">
          </div>

          <div class="grupo-campo">
            <label for="forma_pagamento">Forma de Pagamento:</label>
            <select name="forma_pagamento" id="forma_pagamento" required onchange="atualizarFormaPagamento()">
              <option value="">Selecione</option>
              <option value="Dinheiro">Dinheiro</option>
              <option value="Cartão de Crédito">Cartão de Crédito</option>
              <option value="Cartão de Débito">Cartão de Débito</option>
              <option value="Pix">Pix</option>
              <option value="Outros">Outros</option>
            </select>
          </div>

          <div id="area-pix" class="area-pagamento">
            <div class="qr-code-container">
              <h3>Pagamento via Pix</h3>
              <p class="instrucao-pix">Escaneie o QR Code abaixo para pagamento:</p>
              <img id="pix-qr" src="" alt="QR Code Pix" class="qr-code">
              <p class="chave-pix">Chave Pix: <strong>chavepix@lhdparking.com</strong></p>
            </div>
          </div>

          <div id="area-cartao" class="area-pagamento">
            <h3>Dados do Cartão</h3>
            <div class="grupo-campo">
              <label for="nome_cartao">Nome no Cartão:</label>
              <input type="text" id="nome_cartao" placeholder="Nome completo">
            </div>

            <div class="grupo-campo">
              <label for="numero_cartao">Número do Cartão:</label>
              <input type="text" id="numero_cartao" maxlength="16" placeholder="0000 0000 0000 0000">
            </div>

            <div class="grupo-campos">
              <div class="grupo-campo">
                <label for="validade_cartao">Validade:</label>
                <input type="text" id="validade_cartao" placeholder="MM/AA" maxlength="5">
              </div>
              <div class="grupo-campo">
                <label for="cvv_cartao">CVV:</label>
                <input type="text" id="cvv_cartao" maxlength="3" placeholder="123">
              </div>
            </div>
          </div>

          <div id="area-outros" class="area-pagamento">
            <h3>Outras Formas de Pagamento</h3>
            <div class="grupo-campo">
              <label for="descricao_outros">Descrição:</label>
              <input type="text" id="descricao_outros" placeholder="Ex: transferência, cortesia...">
            </div>
          </div>

          <button type="submit" class="botao-confirmar">Confirmar Pagamento</button>
        </form>

        <a href="menu.php" class="link-voltar">← Voltar para o Menu</a>
      </div>
    </section>
  </main>
</body>
</html>