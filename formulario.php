<?php
require_once 'proteger.php';
require_once 'config.php';

$id = (int) ($_GET['id'] ?? 0);
$prefixo = '';
$modelo = '';
$ano_fabricacao = '';
$capacidade_maxima = '';
$status_atual = 'ativo';
$status_operacional = [
    'ativo' => 'Ativo',
    'manutencao' => 'Em manutenção',
    'parado' => 'Parado'
];
$situacoes = $status_operacional;
$erros = [];

if ($id > 0 && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conexao->prepare('SELECT * FROM trens WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $trem = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$trem) {
        $_SESSION['mensagem'] = "Trem não encontrado.";
        header("Location: trens.php");
        exit;
    }

    $prefixo = $trem['prefixo'];
    $modelo = $trem['modelo_composicao'];
    $ano_fabricacao = $trem['ano_fabricacao'];
    $capacidade_maxima = $trem['capacidade_maxima'];
    $status_atual = $trem['status_operacional']; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $prefixo = trim($_POST['prefixo'] ?? '');
    $modelo = trim($_POST['modelo_composicao'] ?? $_POST['modelo'] ?? '');
    $ano_fabricacao = trim($_POST['ano_fabricacao'] ?? '');
    $capacidade_maxima = trim($_POST['capacidade_maxima'] ?? $_POST['capacidade_toneladas'] ?? '');
    $status_atual = $_POST['status_operacional'] ?? $_POST['situacao'] ?? 'ativo';
    $situacao = $status_atual;

    if ($prefixo === '') {
        $erros[] = 'Informe o prefixo do trem.';
    }

    if ($modelo === '') {
        $erros[] = 'Informe o modelo do trem.';
    }

    if (!is_numeric($ano_fabricacao) || (int) $ano_fabricacao < 1900 || (int) $ano_fabricacao > 2100) {
        $erros[] = 'Informe um ano de fabricação entre 1900 e 2100.';
    }

    if (!is_numeric($capacidade_maxima) || (float) $capacidade_maxima <= 0) {
        $erros[] = 'Informe uma capacidade maior que zero.';
    }

    if (!isset($status_operacional[$situacao])) {
        $erros[] = 'Selecione uma situação válida.';
    }

    if (count($erros) === 0) {
        $ano = (int) $ano_fabricacao;
        $capacidade = (float) $capacidade_maxima;

        if ($id > 0) {
            $stmt = $conexao->prepare('UPDATE trens SET prefixo = ?, modelo_composicao = ?, ano_fabricacao = ?, capacidade_maxima = ?, status_operacional = ? WHERE id = ?');
            $stmt->bind_param('ssidsi', $prefixo, $modelo, $ano, $capacidade, $situacao, $id);
            if ($stmt->execute()) {
                $_SESSION['mensagem'] = 'Trem atualizado com sucesso!';
            } else {
                $_SESSION['mensagem'] = 'Não foi possível realizar a atualização.';
            }
        } else {
            $stmt = $conexao->prepare('INSERT INTO trens (prefixo, modelo_composicao, ano_fabricacao, capacidade_maxima, status_operacional) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('ssids', $prefixo, $modelo, $ano, $capacidade, $situacao);
            if ($stmt->execute()) {
                $_SESSION['mensagem'] = 'Trem cadastrado com sucesso!';
            } else {
                $_SESSION['mensagem'] = 'Não foi possível realizar o cadastro.';
            }
        }
        $stmt->close();
        header('Location: trens.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $id > 0 ? 'Editar trem' : 'Novo trem' ?> - SuperTrain</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="estilo/formulario.css" />
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
    <div class="container formulario-wrapper">
      <h2 class="texto-indicativo">
        <?= $id > 0 ? 'Edição e Ajuste de Informações da Composição' : 'Cadastre uma Nova Composição Ferroviária' ?>
      </h2>

      <div class="card formulario-card">
        <h1 class="card-title"><?= $id > 0 ? 'Editar trem' : 'Novo trem' ?></h1>

        <?php if (count($erros) > 0): ?>
          <div class="aviso aviso-erro">
            <ul>
              <?php foreach ($erros as $item): ?>
                <li><?= htmlspecialchars($item) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
          <input type="hidden" name="id" value="<?= $id ?>">

          <div class="linha row">
            <div class="campo col-md-6 form-group mb-3">
              <label for="prefixo" class="form-label">Prefixo</label>
              <input type="text" class="form-control" id="prefixo" name="prefixo" maxlength="20" placeholder="Ex: Trem 130" value="<?= htmlspecialchars($prefixo) ?>" required>
            </div>

            <div class="campo col-md-6 form-group mb-3">
              <label for="ano_fabricacao" class="form-label">Ano de fabricação</label>
              <input type="number" class="form-control" id="ano_fabricacao" name="ano_fabricacao" min="1900" max="2100" placeholder="Ex: 2024" value="<?= htmlspecialchars((string) $ano_fabricacao) ?>" required>
            </div>
          </div>

          <div class="campo form-group mb-3">
            <label for="modelo_composicao" class="form-label">Modelo</label>
            <input type="text" class="form-control" id="modelo_composicao" name="modelo_composicao" maxlength="100" placeholder="Ex: Alstom Metropolis ST-X" value="<?= htmlspecialchars($modelo) ?>" required>
          </div>

          <div class="linha row">
            <div class="campo col-md-6 form-group mb-3">
              <label for="capacidade_maxima" class="form-label">Capacidade (t)</label>
              <input type="number" class="form-control" id="capacidade_maxima" name="capacidade_maxima" step="0.01" min="0.01" placeholder="Ex: 1200" value="<?= htmlspecialchars((string) $capacidade_maxima) ?>" required>
            </div>

            <div class="campo col-md-6 form-group mb-4">
              <label for="status_operacional" class="form-label">Situação</label>
              <select id="status_operacional" name="status_operacional" class="form-select">
                <?php foreach ($status_operacional as $chave => $rotulo): ?>
                  <option value="<?= $chave ?>" <?= $chave === $status_atual ? 'selected' : '' ?>>
                    <?= $rotulo ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="acoes d-flex gap-3 align-items-center mt-3">
            <button type="submit" class="botao botao-primario flex-grow-1">
              <i class="bi <?= $id > 0 ? 'bi-check2-circle' : 'bi-plus-circle' ?>"></i> <?= $id > 0 ? 'Atualizar' : 'Cadastrar' ?>
            </button>
            <a href="trens.php" class="botao botao-secundario">
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