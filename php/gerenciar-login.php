<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexao.php';

$result = $mysqli->query("SELECT id_login_pk, nome, sobrenome, email, data_criacao FROM Login ORDER BY id_login_pk DESC");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciar Logins - LHD Parking</title>
  <link rel="stylesheet" href="../css/style-gerenciar-logins.css">
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
        <h2>Gerenciar Logins</h2>

        <?php if ($result && $result->num_rows > 0): ?>
          <div class="tabela-container">
            <table class="tabela-gerenciar">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nome</th>
                  <th>Sobrenome</th>
                  <th>Email</th>
                  <th>Data de Criação</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['id_login_pk']) ?></td>
                    <td><?= htmlspecialchars($row['nome']) ?></td>
                    <td><?= htmlspecialchars($row['sobrenome']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['data_criacao']) ?></td>
                    <td class="acoes">
                      <a href="../php/editar-login.php?id=<?= $row['id_login_pk'] ?>" class="botao-editar">Editar</a>
                      <a href="../php/excluir-login.php?id=<?= $row['id_login_pk'] ?>" class="botao-excluir" onclick="return confirm('Tem certeza que deseja excluir este login?')">Excluir</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p class="mensagem-vazia">Nenhum login encontrado.</p>
        <?php endif; ?>

        <a href="../html/menu-gerenciar.html" class="link-voltar">Voltar para Gerenciamento</a>
      </div>
    </section>
  </main>
</body>
</html>