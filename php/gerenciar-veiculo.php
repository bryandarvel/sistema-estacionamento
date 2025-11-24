<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexao.php';

$sql = "
SELECT 
  v.id_veiculo_pk,
  v.placa,
  v.marca,
  v.modelo,
  v.cor,
  c.nome AS nome_cliente
FROM Veiculo v
INNER JOIN Cliente c ON v.id_cliente_fk = c.id_cliente_pk
ORDER BY v.id_veiculo_pk DESC
";
$result = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciar Veículos - LHD Parking</title>
  <link rel="stylesheet" href="../css/style-gerenciar-veiculos.css">
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
    <section class="container-gerenciar">
      <div class="caixa-gerenciar">
        <h2>Gerenciar Veículos</h2>

        <?php if ($result && $result->num_rows > 0): ?>
          <div class="tabela-container">
            <table class="tabela-gerenciar">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Placa</th>
                  <th>Marca</th>
                  <th>Modelo</th>
                  <th>Cor</th>
                  <th>Cliente</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['id_veiculo_pk']) ?></td>
                    <td><?= htmlspecialchars($row['placa']) ?></td>
                    <td><?= htmlspecialchars($row['marca']) ?></td>
                    <td><?= htmlspecialchars($row['modelo']) ?></td>
                    <td><?= htmlspecialchars($row['cor']) ?></td>
                    <td><?= htmlspecialchars($row['nome_cliente']) ?></td>
                    <td class="acoes">
                      <a href="../php/editar-veiculo.php?id=<?= $row['id_veiculo_pk'] ?>" class="botao-editar">Editar</a>
                      <a href="../php/excluir-veiculo.php?id=<?= $row['id_veiculo_pk'] ?>" class="botao-excluir" onclick="return confirm('Tem certeza que deseja excluir este veículo?')">Excluir</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p class="mensagem-vazia">Nenhum veículo cadastrado.</p>
        <?php endif; ?>

        <a href="../html/menu-gerenciar.html" class="link-voltar">Voltar para Gerenciamento</a>
      </div>
    </section>
  </main>
</body>
</html>