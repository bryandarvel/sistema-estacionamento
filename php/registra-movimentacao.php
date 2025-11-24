<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexao.php';

function carregarVagas($mysqli) {
    return $mysqli->query("
        SELECT id_vaga_pk, numero, setor
        FROM Vaga
        WHERE status = 'livre'
        ORDER BY setor, numero+0 ASC
    ");
}


$veiculos = $mysqli->query("
    SELECT v.id_veiculo_pk, v.placa, c.nome
    FROM Veiculo v
    INNER JOIN Cliente c ON v.id_cliente_fk = c.id_cliente_pk
    ORDER BY c.nome ASC
");

$vagas = carregarVagas($mysqli);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_veiculo_fk = intval($_POST['id_veiculo_fk']);
    $id_vaga_fk = intval($_POST['id_vaga_fk']);

    if ($id_veiculo_fk <= 0 || $id_vaga_fk <= 0) {
        $erro = "Selecione o veículo e a vaga.";
    } else {


        $check = $mysqli->prepare("SELECT status FROM Vaga WHERE id_vaga_pk = ?");
        $check->bind_param("i", $id_vaga_fk);
        $check->execute();
        $check->bind_result($status_vaga);
        $check->fetch();
        $check->close();

        if ($status_vaga !== 'livre') {
            $erro = "Erro: esta vaga já está ocupada no banco de dados.";
        } else {

            $stmt = $mysqli->prepare("
                INSERT INTO Movimentacao (id_veiculo_fk, id_vaga_fk, data_entrada)
                VALUES (?, ?, NOW())
            ");
            $stmt->bind_param("ii", $id_veiculo_fk, $id_vaga_fk);

            if ($stmt->execute()) {
                $update = $mysqli->prepare("
                    UPDATE Vaga SET status = 'ocupada'
                    WHERE id_vaga_pk = ?
                ");
                $update->bind_param("i", $id_vaga_fk);
                $update->execute();
                $vagas = carregarVagas($mysqli);

                $sucesso = "Movimentação registrada com sucesso!";
            } else {
                $erro = "Erro ao registrar movimentação: " . $stmt->error;
            }
        }
    }
}


$ativas = $mysqli->query("
    SELECT 
        m.id_movimentacao_pk,
        v.placa,
        c.nome,
        g.numero,
        g.setor,
        m.data_entrada
    FROM Movimentacao m
    INNER JOIN Veiculo v ON m.id_veiculo_fk = v.id_veiculo_pk
    INNER JOIN Cliente c ON v.id_cliente_fk = c.id_cliente_pk
    INNER JOIN Vaga g ON m.id_vaga_fk = g.id_vaga_pk
    WHERE m.data_saida IS NULL
    ORDER BY m.data_entrada DESC
");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LHD Parking - Registro de Movimentação</title>
  <link rel="stylesheet" href="../css/style-registro-movimentacao.css">
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
    <div class="container-principal">
      
      <section class="container-formulario">
        <div class="caixa-formulario">
          <h2>Registro de Movimentação</h2>

          <?php if (isset($erro)): ?>
            <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
          <?php elseif (isset($sucesso)): ?>
            <p class="mensagem-sucesso"><?= htmlspecialchars($sucesso) ?></p>
          <?php endif; ?>

          <form method="post" action="">
            <div class="grupo-campo">
              <label for="id_veiculo_fk">Veículo:</label>
              <select name="id_veiculo_fk" id="id_veiculo_fk" required>
                <option value="">Selecione o veículo</option>
                <?php while ($v = $veiculos->fetch_assoc()): ?>
                  <option value="<?= $v['id_veiculo_pk'] ?>">
                    <?= $v['placa'] ?> - <?= $v['nome'] ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="grupo-campo">
              <label for="id_vaga_fk">Vaga:</label>
              <select name="id_vaga_fk" id="id_vaga_fk" required>
                <option value="">Selecione a vaga</option>
                <?php while ($vg = $vagas->fetch_assoc()): ?>
                  <option value="<?= $vg['id_vaga_pk'] ?>">
                    Vaga <?= $vg['numero'] ?> - Setor <?= $vg['setor'] ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <button type="submit" class="botao-registrar">Registrar Entrada</button>
          </form>

          <a href="menu.php" class="link-voltar">← Voltar para o Menu</a>

        </div>
      </section>
      <section class="container-lista">
        <div class="caixa-lista">
          <h2>Movimentações Ativas</h2>

          <div class="tabela-container">
            <?php if ($ativas && $ativas->num_rows > 0): ?>
              <table class="tabela-movimentacoes">
                <thead>
                  <tr>
                    <th>Placa</th>
                    <th>Cliente</th>
                    <th>Vaga</th>
                    <th>Setor</th>
                    <th>Entrada</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $ativas->fetch_assoc()): ?>
                    <tr>
                      <td><?= $row['placa'] ?></td>
                      <td><?= $row['nome'] ?></td>
                      <td><?= $row['numero'] ?></td>
                      <td><?= $row['setor'] ?></td>
                      <td><?= date('d/m/Y H:i', strtotime($row['data_entrada'])) ?></td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            <?php else: ?>
              <p class="mensagem-vazia">Nenhuma movimentação ativa no momento.</p>
            <?php endif; ?>
          </div>

        </div>
      </section>

    </div>
  </main>
</body>
</html>
