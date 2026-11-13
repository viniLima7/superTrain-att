<?php
require 'config.php';
require 'proteger.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

$cargo = $_SESSION['usuario_cargo'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id']) && $cargo != 'cliente') {
  $id = (int) $_POST['excluir_id'];

  $stmt = $conexao->prepare('DELETE FROM trens WHERE id = ?');
  $stmt->bind_param('i', $id);

  if ($stmt->execute()) {
    $_SESSION['mensagem'] = 'Trem excluído com sucesso.';
  } else {
    $_SESSION['mensagem'] = 'Erro ao excluir o trem.';
  }

  $stmt->close();
  header('Location: dashboard.php');
  exit;
}

$mensagem = $_SESSION['mensagem'] ?? '';
unset($_SESSION['mensagem']);

$resultado = $conexao->query("SELECT * FROM trens WHERE status_operacional IN ('manutencao', 'parado') ORDER BY prefixo");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - SuperTrain</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="estilo/dashboard.css" />
  <link rel="stylesheet" href="estilo/trens.css" />
</head>

<body>
  <header>

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
              <a class="nav-link active" aria-current="page" href="dashboard.php">
                <i class="bi bi-grid-fill"></i> Dashboard
              </a>
            </li>
            <?php if($cargo != 'cliente'): ?>
            <li class="nav-item">
              <a class="nav-link" href="usuarios.php">
                <i class="bi bi-people-fill"></i> usuários
              </a>
            </li>
            <?php endif; ?>

            <li class="nav-item">
              <a class="nav-link" href="trens.php">
                <i class="bi bi-train-front"></i> Trens
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="rotas.php">
                <i class="bi bi-signpost-split"></i> Rotas
              </a>
            </li>

            <?php if($cargo != 'cliente'): ?>
            <li class="nav-item">
              <a class="nav-link" href="sensores.php">
                <i class="bi bi-broadcast-pin"></i> Sensores
              </a>
            </li>
            <?php endif; ?>

            <?php if($cargo != 'cliente'): ?>
              <li class="nav-item">
                <a class="nav-link" href="relatorios.php">
                  <i class="bi bi-file-earmark-bar-graph"></i> Relatórios
                </a>
              </li>
              <?php endif; ?>
            <li class="nav-item">
              <a class="nav-link" href="perfil.php">
                <i class="bi bi-person-circle"></i> Perfil
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-danger" onclick="return confirm('Tem certeza que deseja sair?');" href="sair.php">
                <i class="bi bi-box-arrow-right"></i> Sair
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <div class="container dashboard-container">

      <!-- SEÇÃO 1: TABELA DE STATUS DOS TRENS -->
      <section class="dashboard-section">
                   <div class="card card-bg-blur trens-card">
        <div class="card-header-custom">
          <h1 class="card-title">Frota de Trens</h1>
          <p class="card-subtitle">
            <i class="bi bi-cursor"></i> Clique em uma composição para ver na página de trens
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
            <p class="empty-text">Nenhum trem em manutenção ou parado no momento.</p>
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
                  <th scope="col">Capacidade (T)</th>
                  <th scope="col">Status</th>
                </tr>
              </thead>
              <tbody>

                <?php
                while ($linha = $resultado->fetch_assoc()) :
                ?>

                  <!-- CRIAÇÃO DOS TRENS -->
                  <tr class="train-row" onclick="window.location.href='trens.php'" style="cursor: pointer;" title="Clique para ver na página de trens">
                    <td scope="row"><?= htmlspecialchars($linha['prefixo']) ?></td>
                    <td><?= htmlspecialchars($linha['modelo_composicao']) ?></td>
                    <td><?= number_format((float) $linha['capacidade_maxima'], 2, ',', '.') ?></td>
                    <td>
                      <span class="status-<?= [
                                            'ativo' => 'ativo',
                                            'manutencao' => 'atencao',
                                            'parado' => 'parado'
                                          ][$linha['status_operacional']] ?? 'desconhecido'; ?>">

                        <span class="status-dot"></span> <?= [
                                                            'ativo' => 'Ativo',
                                                            'manutencao' => 'Manutenção',
                                                            'parado' => 'Parado'
                                                          ][$linha['status_operacional']] ?? 'desconhecido'; ?>
                      </span>
                    </td>
                  </tr>
                <?php
                endwhile;
                ?>
              </tbody>
            </table>
          </div>
        <?php
        endif;
        ?>
      </div>
      </section>

      <!-- SEÇÃO 2: CARDS DE RESUMO (LOTAÇÃO & PERFIL) -->
      <section class="dashboard-section">
        <div class="row g-3">
          <div class="col-md-6">
            <a href="rotas.php" class="text-decoration-none d-block h-100">
              <div class="card card-bg-blur widget-card">
                <div class="widget-icon-frame">
                  <img src="imagens/imagem_rotas.jfif" alt="Ícone de Rotas" />
                </div>
                <h3 class="widget-title">Rotas dos trens</h3>
                <span class="widget-badge">
                  <i class="bi bi-signpost-split"></i> Clique para ver as rotas
                </span>
              </div>
            </a>
          </div>

          <div class="col-md-6">
            <a href="perfil.php" class="text-decoration-none d-block h-100">
              <div class="card card-bg-blur widget-card">
                <div class="widget-icon-frame">
                  <img src="imagens/foto_perfil.webp" alt="Foto de Perfil" />
                </div>
                <h3 class="widget-title">Perfil</h3>
                <span class="widget-badge">
                  <i class="bi bi-shield-check"></i> Clique para ver seu perfil
                </span>
              </div>
            </a>
          </div>
        </div>
      </section>

      <!-- SEÇÃO 3: TABELA DE TEMPO DE ESPERA DOS METRÔS -->
      <section class="dashboard-section">
        <div class="card card-bg-blur dashboard-card">
          <div class="card-header-custom">
            <h2 class="card-title">Tempo de Espera</h2>
            <p class="card-subtitle">
              <i class="bi bi-clock-history"></i> Estimativas de chegada por plataforma
            </p>
          </div>

          <div class="table-responsive">
            <table class="table text-center">
              <thead>
                <tr>
                  <th scope="col">Linha</th>
                  <th scope="col">Sentido</th>
                  <th scope="col">Nível de Fluxo</th>
                  <th scope="col">Capacidade</th>
                  <th scope="col">Previsão</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th scope="row">130</th>
                  <td>Norte / Sul</td>
                  <td><span class="status-ativo">Normal</span></td>
                  <td class="progress-cell">
                    <div class="progress" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">
                      <div class="progress-bar bg-success" style="width: 30%"></div>
                    </div>
                  </td>
                  <td><span class="time-badge text-success">2 min</span></td>
                </tr>

                <tr>
                  <th scope="row">142</th>
                  <td>Oeste / Leste</td>
                  <td><span class="status-atencao">Atenção</span></td>
                  <td class="progress-cell">
                    <div class="progress" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                      <div class="progress-bar bg-warning" style="width: 60%"></div>
                    </div>
                  </td>
                  <td><span class="time-badge text-warning">5 min</span></td>
                </tr>

                <tr>
                  <th scope="row">174</th>
                  <td>Leste / Norte</td>
                  <td><span class="status-atencao">Atenção</span></td>
                  <td class="progress-cell">
                    <div class="progress" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                      <div class="progress-bar bg-warning" style="width: 50%"></div>
                    </div>
                  </td>
                  <td><span class="time-badge text-warning">7 min</span></td>
                </tr>

                <tr>
                  <th scope="row">130</th>
                  <td>Sul / Norte</td>
                  <td><span class="status-parado">Intenso</span></td>
                  <td class="progress-cell">
                    <div class="progress" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100">
                      <div class="progress-bar bg-danger" style="width: 80%"></div>
                    </div>
                  </td>
                  <td><span class="time-badge text-danger">10 min</span></td>
                </tr>

                <tr>
                  <th scope="row">001</th>
                  <td>Centro</td>
                  <td><span class="status-parado">Intenso</span></td>
                  <td class="progress-cell">
                    <div class="progress" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                      <div class="progress-bar bg-danger" style="width: 100%"></div>
                    </div>
                  </td>
                  <td><span class="time-badge text-danger">14 min</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
