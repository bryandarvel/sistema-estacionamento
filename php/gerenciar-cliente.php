<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

$mensagem = '';
$erro = '';

if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    unset($_SESSION['mensagem']);
}

if (isset($_SESSION['erro'])) {
    $erro = $_SESSION['erro'];
    unset($_SESSION['erro']);
}

require_once 'conexao.php';


$result = $mysqli->query("SELECT id_cliente_pk, nome, telefone, cpf FROM Cliente ORDER BY id_cliente_pk DESC");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciar Clientes - LHD Parking</title>
  <link rel="stylesheet" href="../css/style-gerenciar-clientes.css">
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
        <h2>Gerenciar Clientes</h2>
        

        <?php 
        if ($mensagem): ?>
    <p class="mensagem-sucesso"><?= htmlspecialchars($mensagem) ?></p>
<?php endif; ?>

<?php if ($erro): ?>
    <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
<?php endif;      
        if ($result && $result->num_rows > 0): ?>
          <div class="tabela-container">
            <table class="tabela-gerenciar">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nome</th>
                  <th>Telefone</th>
                  <th>CPF</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['id_cliente_pk']) ?></td>
                    <td><?= htmlspecialchars($row['nome']) ?></td>
                    <td><?= htmlspecialchars($row['telefone']) ?></td>
                    <td><?= htmlspecialchars($row['cpf']) ?></td>
                    <td class="acoes">
                      <a href="../php/editar-cliente.php?id=<?= $row['id_cliente_pk'] ?>" class="botao-editar">Editar</a>
                      <a href="../php/excluir-cliente.php?id=<?= $row['id_cliente_pk'] ?>" class="botao-excluir" onclick="return confirm('Tem certeza que deseja excluir este cliente? Todos os veículos associados serão removidos.')">Excluir</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p class="mensagem-vazia">Nenhum cliente cadastrado.</p>
        <?php endif; ?>

        <a href="../html/menu-gerenciar.html" class="link-voltar">Voltar para Gerenciamento</a>
      </div>
    </section>
  </main>
</body>
</html>