<?php
require_once 'proteger.php';
require_once 'config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

$id = (int) ($_GET['id'] ?? 0);

$direcao_linha = '';
$horario_partida = '';
$tipo_composicao = '';
$erros = [];

// Carregar os dados da rota para edição
if ($id > 0 && $_SERVER['REQUEST_METHOD'] === 'GET') {
  $stmt = $conexao->prepare(
    'SELECT id, direcao, horario_partida, tipo_composicao
         FROM rotas
         WHERE id = ?'
  );

  $stmt->bind_param('i', $id);
  $stmt->execute();

  $resultado = $stmt->get_result();
  $rota = $resultado->fetch_assoc();

  $stmt->close();

  if (!$rota) {
    $_SESSION['mensagem'] = 'Rota não encontrada.';
    header('Location: rotas.php');
    exit;
  }

  $direcao_linha = $rota['direcao'];
  $horario_partida = $rota['horario_partida'];
  $tipo_composicao = $rota['tipo_composicao'];
}

// Processar cadastro ou atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int) ($_POST['id'] ?? 0);

  $direcao_linha = trim($_POST['direcao_linha'] ?? '');
  $horario_partida = trim($_POST['horario_partida'] ?? '');
  $tipo_composicao = trim($_POST['tipo_composicao'] ?? '');

  if ($direcao_linha === '') {
    $erros[] = 'Informe a direção da linha.';
  }

  if ($horario_partida === '') {
    $erros[] = 'Informe o horário de partida.';
  }

  $tipos_validos = [
    'passageiro',
    'carga',
    'expresso'
  ];

  if (!in_array($tipo_composicao, $tipos_validos, true)) {
    $erros[] = 'Selecione um tipo de composição válido.';
  }

  if (empty($erros)) {
    if ($id > 0) {
      $stmt = $conexao->prepare(
        'UPDATE rotas
                 SET direcao = ?, horario_partida = ?, tipo_composicao = ?
                 WHERE id = ?'
      );

      $stmt->bind_param(
        'sssi',
        $direcao_linha,
        $horario_partida,
        $tipo_composicao,
        $id
      );

      $sucesso = $stmt->execute();
      $stmt->close();

      $_SESSION['mensagem'] = $sucesso
        ? 'Rota atualizada com sucesso!'
        : 'Não foi possível atualizar a rota.';
    } else {
      $stmt = $conexao->prepare(
        'INSERT INTO rotas
                 (direcao, horario_partida, tipo_composicao)
                 VALUES (?, ?, ?)'
      );

      $stmt->bind_param(
        'sss',
        $direcao_linha,
        $horario_partida,
        $tipo_composicao
      );

      $sucesso = $stmt->execute();
      $stmt->close();

      $_SESSION['mensagem'] = $sucesso
        ? 'Rota cadastrada com sucesso!'
        : 'Não foi possível cadastrar a rota.';
    }

    header('Location: rotas.php');
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?= $id > 0 ? 'Editar Rota' : 'Nova Rota' ?> - SuperTrain</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <link rel="stylesheet" href="estilo/formulario.css">
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
              <a class="nav-link active" href="rotas.php">
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
    <div class="container formulario-wrapper">

      <h2 class="texto-indicativo">
        <?= $id > 0
          ? 'Edição e Ajuste de Informações da Rota'
          : 'Cadastre uma Nova Rota Ferroviária' ?>
      </h2>

      <div class="card formulario-card">

        <h1 class="card-title">
          <?= $id > 0 ? 'Editar Rota' : 'Nova Rota' ?>
        </h1>

        <?php if (!empty($erros)): ?>
          <div class="aviso aviso-erro">
            <ul>
              <?php foreach ($erros as $erro): ?>
                <li><?= htmlspecialchars($erro) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post" action="formulario_rotas.php<?= $id > 0 ? '?id=' . $id : '' ?>"
          autocomplete="off">

          <input type="hidden" name="id" value="<?= $id ?>">

          <div class="linha row">

            <div class="campo col-md-6 form-group mb-3">
              <label for="direcao_linha" class="form-label">
                Direção da linha
              </label>

              <input
                type="text"
                class="form-control"
                id="direcao_linha"
                name="direcao_linha"
                maxlength="20"
                placeholder="Norte - Sul"
                value="<?= htmlspecialchars($direcao_linha) ?>"
                required>
            </div>

            <div class="campo col-md-6 form-group mb-3">
              <label for="horario_partida" class="form-label">
                Horário de Partida
              </label>

              <input
                type="time"
                class="form-control"
                id="horario_partida"
                name="horario_partida"
                value="<?= htmlspecialchars($horario_partida) ?>"
                required>
            </div>

          </div>

          <div class="campo form-group mb-3">
            <label for="tipo_composicao" class="form-label">
              Tipo de Composição
            </label>

            <select
              class="form-select"
              id="tipo_composicao"
              name="tipo_composicao"
              required>

              <option value="">Selecione o tipo de composição</option>

              <option value="passageiro"
                <?= $tipo_composicao === 'passageiro' ? 'selected' : '' ?>>
                Passageiro
              </option>

              <option value="carga"
                <?= $tipo_composicao === 'carga' ? 'selected' : '' ?>>
                Carga Comercial
              </option>

              <option value="expresso"
                <?= $tipo_composicao === 'expresso' ? 'selected' : '' ?>>
                Expresso Direto
              </option>

            </select>
          </div>

          <div class="acoes d-flex gap-3 align-items-center mt-3">

            <button type="submit"
              class="botao botao-primario flex-grow-1">

              <i class="bi <?= $id > 0
                              ? 'bi-check2-circle'
                              : 'bi-plus-circle' ?>"></i>

              <?= $id > 0 ? 'Atualizar' : 'Cadastrar' ?>
            </button>

            <a href="rotas.php" class="botao botao-secundario">
              Cancelar
            </a>

          </div>
        </form>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>