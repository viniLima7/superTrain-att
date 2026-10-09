<?php
require 'proteger.php';
require 'config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
  $id = (int) $_POST['excluir_id'];

  $stmt = $conexao->prepare('DELETE FROM trens WHERE id_trem = ?');
  $stmt->bind_param('i', $id);

  if ($stmt->execute()) {
    $_SESSION['mensagem'] = 'Trem excluído com sucesso.';

    header('Location: trens.php');

    $stmt->close();

    exit;
  } else {
    $_SESSION['mensagem'] = 'Erro ao excluir o trem.';

    header('Location: trens.php');

    $stmt->close();

    exit;
  }
}

$mensagem = $_SESSION['mensagem'] ?? '';

unset($_SESSION['mensagem']);

$resultado = $conexao->query('SELECT * FROM trens ORDER BY prefixo');

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Trens - SuperTrain</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="estilo/trens.css" />
</head>

<body>
  <header>
    <!-- NAV -->
    <nav class="navbar navbar-expand-lg bg-blur">
      <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
          <img src="imagens/logo.png" alt="SuperTrain Logo" width="36" height="28" />
          SuperTrain
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSuperTrain"
          aria-controls="navbarSuperTrain" aria-expanded="false" aria-label="Toggle navigation">
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
              <a class="nav-link active" aria-current="page" href="trens.php">
                <i class="bi bi-train-front"></i> Trens
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="rotas.php">
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
            <a href="formulario.php?id=0" class="btn btn-primary">
              <i class="bi bi-plus-lg"></i>
              <span>Adicionar Trem</span>
            </a>
          </div>
      <div class="card card-bg-blur trens-card">
        <div class="card-header-custom">
          <h1 class="card-title">Frota de Trens</h1>
          <p class="card-subtitle">
            <i class="bi bi-cursor"></i> Clique em uma composição para expandir telemetria e mapa em tempo real
          </p>
        </div>

        <?php
        if ($mensagem !== ''):
        ?>

          <p class="aviso"><?= htmlspecialchars($mensagem) ?></p>

        <?php
        endif;
        ?>

        <?php
        if ($resultado->num_rows === 0):
        ?>

          <div class="empty-fleet-state">
            <i class="bi bi-train-front empty-icon"></i>
            <p class="empty-text">Nenhum trem cadastrado no momento.</p>
            <a href="formulario.php" class="btn btn-primary btn-adicionar-trem">
              <i class="bi bi-plus-lg"></i>
              <span>Cadastrar Primeiro Trem</span>
            </a>
          </div>

        <?php
        else:
        ?>
          <div class="table-responsive">
            <table class="table text-center mb-0">
              <thead>
                <tr>
                  <th scope="col">Linha</th>
                  <th scope="col">Modelo</th>
                  <th scope="col">Capacidade</th>
                  <th scope="col">Status</th>
                </tr>
              </thead>
              <tbody>

                <?php
                while ($linha = $resultado->fetch_assoc()) :
                ?>

                  <!-- CRIAÇÃO DOS TRENS -->
                  <tr class="train-row" data-bs-toggle="collapse" data-bs-target="#detalhes-trem-<?= htmlspecialchars($linha['id']) ?>" aria-expanded="false">
                    <td scope="row"><?= htmlspecialchars($linha['prefixo']) ?></td>
                    <td><?= htmlspecialchars($linha['modelo_composicao']) ?></td>
                    <td><?= number_format((float) $linha['capacidade_maxima'], 2, ',', '.') ?></td>
                    <td>
                      <span class="status-<?= [
                        'ativo' => 'ativo',
                        'manutencao' => 'atencao',
                        'parado' => 'parado'
                      ][$linha['status_operacional']] ?? 'desconhecido';?>">

                        <span class="status-dot"></span> <?= [
                          'ativo' => 'Ativo',
                          'manutencao' => 'Manutenção',
                          'parado' => 'Parado'
                        ][$linha['status_operacional']] ?? 'desconhecido';?>
                      </span>
                    </td>
                  </tr>
                  <tr class="collapse" id="detalhes-trem-<?= htmlspecialchars($linha['id']) ?>">
                    <td colspan="4" class="p-0 border-0">
                      <div class="detail-box">
                        <div class="row g-3 align-items-center">
                          <div class="col-md-7">
                            <div class="detail-label">
                              <i class="bi bi-geo-alt-fill text-primary"></i> Localização via GPS
                            </div>
                            <div class="map-container">
                              <iframe
                                src="https://maps.google.com/maps?q=Joinville&t=&z=14&ie=UTF8&iwloc=&output=embed"
                                class="w-100 h-100 border-0" allowfullscreen="" loading="lazy">
                              </iframe>
                            </div>
                          </div>
                          <div class="col-md-5 d-flex flex-column justify-content-between">
                            <div class="speed-metric-card">
                              <div class="detail-label justify-content-center">
                                <i class="bi bi-speedometer2"></i> Velocidade Atual
                              </div>
                              <div class="speed-number">1</div>
                              <div class="speed-unit">KM / H</div>
                            </div>

                            <div class="train-actions">
                              <a class="btn btn-sm btn-outline-secondary" title="Editar informações do trem" href="formulario.php?id=<?= (int) $linha['id'] ?>">
                                <i class="bi bi-pencil"></i> Editar
                              </a>

                              <form method="post" onsubmit="return confirm('Confirma a exclusão do trem?');">

                                <input type="hidden" name="excluir_id" value="<?= (int) $linha['id'] ?>">

                                <button class="btn btn-sm btn-outline-danger" title="Excluir trem">
                                  <i class="bi bi-trash"></i> Excluir
                                </button>

                              </form>


                            </div>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php
                endwhile;
                ?>
              </tbody>
            </table>
          </div>
      </div>
    </div>
  <?php
        endif;
  ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>