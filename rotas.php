<?php
require_once 'proteger.php';
require_once 'config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// Excluir rota
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
  $id = (int) $_POST['excluir_id'];

  if ($id > 0) {
    $stmt = $conexao->prepare(
      'DELETE FROM rotas WHERE id = ?'
    );

    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
      $_SESSION['mensagem'] = $stmt->affected_rows > 0
        ? 'Rota excluída com sucesso!'
        : 'A rota não foi encontrada.';
    } else {
      $_SESSION['mensagem'] = 'Não foi possível excluir a rota.';
    }

    $stmt->close();
  }

  header('Location: rotas.php');
  exit;
}

// Mensagem de retorno
$mensagem = $_SESSION['mensagem'] ?? '';
unset($_SESSION['mensagem']);

// Consultar as rotas cadastradas
$resultado = $conexao->query(
  'SELECT id, direcao, horario_partida, tipo_composicao
     FROM rotas
     ORDER BY id DESC'
);

if (!$resultado) {
  die('Erro ao consultar as rotas. Verifique a estrutura da tabela rotas.');
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Rotas - SuperTrain</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <link rel="stylesheet" href="estilo/rotas.css?v=1.0">
</head>

<body>

  <header>
    <nav class="navbar navbar-expand-lg bg-blur">
      <div class="container-fluid">

        <a class="navbar-brand" href="dashboard.php">
          <img src="imagens/logo.png"
            alt="SuperTrain Logo"
            width="36"
            height="28">
          SuperTrain
        </a>

        <button class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarSuperTrain"
          aria-controls="navbarSuperTrain"
          aria-expanded="false"
          aria-label="Abrir menu">

          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSuperTrain">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

            <li class="nav-item">
              <a class="nav-link" href="dashboard.php">
                <i class="bi bi-grid-fill"></i> Dashboard
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="trens.php">
                <i class="bi bi-train-front"></i> Trens
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="rotas.php">
                <i class="bi bi-signpost-split"></i> Rotas
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="sensores.php">
                <i class="bi bi-broadcast-pin"></i> Sensores
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="relatorios.php">
                <i class="bi bi-file-earmark-bar-graph"></i> Relatórios
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="perfil.php">
                <i class="bi bi-person-circle"></i> Perfil
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link text-danger" href="sair.php">
                <i class="bi bi-box-arrow-right"></i> Sair
              </a>
            </li>

          </ul>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <div class="container trens-container">

      <div class="header-actions mb-2">
        <a href="formulario_rotas.php" class="btn btn-primary">
          <i class="bi bi-plus-lg"></i>
          <span>Adicionar Rota</span>
        </a>
      </div>

      <div class="card card-bg-blur trens-card">

        <div class="card-header-custom">
          <h1 class="card-title">Rotas cadastradas</h1>
        </div>

        <?php if ($mensagem !== ''): ?>
          <p class="aviso">
            <?= htmlspecialchars($mensagem) ?>
          </p>
        <?php endif; ?>

        <?php if ($resultado->num_rows === 0): ?>

          <div class="empty-fleet-state">
            <i class="bi bi-signpost-split empty-icon"></i>

            <p class="empty-text">
              Nenhuma rota cadastrada no momento.
            </p>

            <a href="formulario_rotas.php"
              class="btn btn-primary btn-adicionar-trem">

              <i class="bi bi-plus-lg"></i>
              <span>Cadastrar primeira rota</span>
            </a>
          </div>

        <?php else: ?>

          <div class="table-responsive">
            <table class="table text-center mb-0">

              <thead>
                <tr>
                  <th scope="col">Linha</th>
                  <th scope="col">Horário de Partida</th>
                  <th scope="col">Composição</th>
                  <th scope="col">Ações</th>
                </tr>
              </thead>

              <tbody>
                <?php while ($rota = $resultado->fetch_assoc()): ?>

                  <tr class="train-row">

                    <td>
                      <?= htmlspecialchars($rota['direcao']) ?>
                    </td>

                    <td>
                      <?= htmlspecialchars(
                        substr((string) $rota['horario_partida'], 0, 5)
                      ) ?>
                    </td>

                    <td>
                      <?php
                      $nomes_composicao = [
                        'passageiro' => 'Passageiro',
                        'carga' => 'Carga Comercial',
                        'expresso' => 'Expresso Direto'
                      ];

                      $tipo = $rota['tipo_composicao'] ?? '';

                      echo htmlspecialchars(
                        $nomes_composicao[$tipo] ?? $tipo
                      );
                      ?>
                    </td>

                    <td>
                      <div class="train-actions d-flex justify-content-center gap-2">

                        <a
                          class="btn btn-sm btn-outline-secondary"
                          title="Editar rota"
                          href="formulario_rotas.php?id=<?= (int) $rota['id'] ?>">

                          <i class="bi bi-pencil"></i>
                          Editar
                        </a>

                        <form
                          method="post"
                          action="rotas.php"
                          onsubmit="return confirm('Confirma a exclusão desta rota?');">

                          <input
                            type="hidden"
                            name="excluir_id"
                            value="<?= (int) $rota['id'] ?>">

                          <button
                            type="submit"
                            class="btn btn-sm btn-outline-danger"
                            title="Excluir rota">

                            <i class="bi bi-trash"></i>
                            Excluir
                          </button>

                        </form>

                      </div>
                    </td>

                  </tr>

                <?php endwhile; ?>
              </tbody>

            </table>
          </div>

        <?php endif; ?>

      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>