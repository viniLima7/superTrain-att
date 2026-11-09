<?php
require 'proteger.php';
require 'config.php';

$usuarioId = (int) $_SESSION['usuario_id'];

// Rótulos para os campos enum da tabela operadores
$turnos = [
  'diurno1'    => ['rotulo' => 'Diurno (06:00 - 14:00)',     'resumo' => '06:00 - 14:00 (Diurno)'],
  'vespertino' => ['rotulo' => 'Vespertino (14:00 - 22:00)', 'resumo' => '14:00 - 22:00 (Vespertino)'],
  'noturno'    => ['rotulo' => 'Noturno (22:00 - 06:00)',    'resumo' => '22:00 - 06:00 (Noturno)'],
  'escala'     => ['rotulo' => 'Escala 12x36',               'resumo' => 'Escala 12x36'],
];

$statusTurno = [
  'ativo'   => ['rotulo' => 'Em Turno Ativo', 'cor' => null],
  'pausa'   => ['rotulo' => 'Em Pausa',       'cor' => '#ffc107'],
  'inativo' => ['rotulo' => 'Fora de Turno',  'cor' => '#6c757d'],
];

/**
 * Busca os dados do usuário logado junto com seu registro de operador (se existir).
 */
function buscarPerfil(mysqli $conexao, int $usuarioId): ?array
{
  $stmt = $conexao->prepare(
    'SELECT u.id AS usuario_id, u.email, u.nome_usuario, u.senha_hash, u.cargo AS cargo_usuario,
            o.id AS operador_id, o.matricula, o.nome_completo, o.cargo_funcao, o.setor_descricao,
            o.telefone_ramal, o.foto_perfil_url, o.linha_principal_id, o.turno_trabalho, o.status_turno,
            o.horas_em_escala_acumuladas, o.trens_despachados_acumulados,
            o.pontualidade_linhas_percentual, o.alertas_atendidos_acumulados,
            o.autenticacao_2fa, o.alerta_sonoro_emergencia, o.notificacao_telemetria,
            o.relatorio_auto_fechamento
       FROM usuarios u
       LEFT JOIN operadores o ON o.usuario_id = u.id
      WHERE u.id = ?'
  );
  $stmt->bind_param('i', $usuarioId);
  $stmt->execute();
  $perfil = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  return $perfil ?: null;
}

$perfil = buscarPerfil($conexao, $usuarioId);

if ($perfil === null) {
  // Usuário da sessão não existe mais no banco
  header('Location: sair.php');
  exit;
}

// SALVAR ALTERAÇÕES
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_perfil'])) {
  $erros = [];

  $nomeCompleto = trim($_POST['nome_completo'] ?? '');
  $email        = trim($_POST['email'] ?? '');
  $telefone     = trim($_POST['telefone_ramal'] ?? '');
  $cargoFuncao  = trim($_POST['cargo_funcao'] ?? '');
  $linhaId      = ($_POST['linha_principal_id'] ?? '') === 'geral' || ($_POST['linha_principal_id'] ?? '') === ''
    ? null
    : (int) $_POST['linha_principal_id'];
  $turno        = array_key_exists($_POST['turno_trabalho'] ?? '', $turnos) ? $_POST['turno_trabalho'] : 'diurno1';

  $twoFa          = isset($_POST['autenticacao_2fa']) ? 1 : 0;
  $alertaSonoro   = isset($_POST['alerta_sonoro_emergencia']) ? 1 : 0;
  $notifTelemetria = isset($_POST['notificacao_telemetria']) ? 1 : 0;
  $relatorioAuto  = isset($_POST['relatorio_auto_fechamento']) ? 1 : 0;

  $senhaAtual     = $_POST['senha_atual'] ?? '';
  $novaSenha      = $_POST['nova_senha'] ?? '';
  $confirmarSenha = $_POST['confirmar_senha'] ?? '';

  if ($nomeCompleto === '') {
    $erros[] = 'Informe o nome completo.';
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'Informe um e-mail válido.';
  } else {
    $stmt = $conexao->prepare('SELECT id FROM usuarios WHERE email = ? AND id <> ?');
    $stmt->bind_param('si', $email, $usuarioId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
      $erros[] = 'Este e-mail já está em uso por outro usuário.';
    }
    $stmt->close();
  }

  // Troca de senha (somente se algum campo de senha foi preenchido)
  $alterarSenha = $senhaAtual !== '' || $novaSenha !== '' || $confirmarSenha !== '';
  if ($alterarSenha) {
    if (!password_verify($senhaAtual, $perfil['senha_hash'])) {
      $erros[] = 'A senha atual está incorreta.';
    } elseif (strlen($novaSenha) < 6) {
      $erros[] = 'A nova senha deve ter pelo menos 6 caracteres.';
    } elseif ($novaSenha !== $confirmarSenha) {
      $erros[] = 'A confirmação da nova senha não confere.';
    }
  }

  if (empty($erros)) {
    try {
      $conexao->begin_transaction();

      // Dados da conta (tabela usuarios)
      if ($alterarSenha) {
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $conexao->prepare('UPDATE usuarios SET email = ?, senha_hash = ? WHERE id = ?');
        $stmt->bind_param('ssi', $email, $hash, $usuarioId);
      } else {
        $stmt = $conexao->prepare('UPDATE usuarios SET email = ? WHERE id = ?');
        $stmt->bind_param('si', $email, $usuarioId);
      }
      $stmt->execute();
      $stmt->close();

      // Dados funcionais (tabela operadores)
      if ($perfil['operador_id'] !== null) {
        $stmt = $conexao->prepare(
          'UPDATE operadores
              SET nome_completo = ?, cargo_funcao = ?, telefone_ramal = ?, linha_principal_id = ?,
                  turno_trabalho = ?, autenticacao_2fa = ?, alerta_sonoro_emergencia = ?,
                  notificacao_telemetria = ?, relatorio_auto_fechamento = ?
            WHERE usuario_id = ?'
        );
        $stmt->bind_param(
          'sssisiiiii',
          $nomeCompleto,
          $cargoFuncao,
          $telefone,
          $linhaId,
          $turno,
          $twoFa,
          $alertaSonoro,
          $notifTelemetria,
          $relatorioAuto,
          $usuarioId
        );
      } else {
        // Usuário ainda sem registro de operador: cria um com matrícula gerada
        $matricula = sprintf('ST-%04d', $usuarioId);
        $stmt = $conexao->prepare(
          'INSERT INTO operadores
             (usuario_id, matricula, nome_completo, cargo_funcao, telefone_ramal, linha_principal_id,
              turno_trabalho, autenticacao_2fa, alerta_sonoro_emergencia, notificacao_telemetria,
              relatorio_auto_fechamento)
           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
          'issssisiiii',
          $usuarioId,
          $matricula,
          $nomeCompleto,
          $cargoFuncao,
          $telefone,
          $linhaId,
          $turno,
          $twoFa,
          $alertaSonoro,
          $notifTelemetria,
          $relatorioAuto
        );
      }
      $stmt->execute();
      $stmt->close();

      $conexao->commit();

      $_SESSION['usuario_email'] = $email;
      $_SESSION['mensagem'] = 'Alterações de perfil salvas com sucesso!';
      $_SESSION['mensagem_tipo'] = 'sucesso';
    } catch (mysqli_sql_exception $e) {
      $conexao->rollback();
      $_SESSION['mensagem'] = 'Erro ao salvar as alterações do perfil.';
      $_SESSION['mensagem_tipo'] = 'erro';
    }
  } else {
    $_SESSION['mensagem'] = implode(' ', $erros);
    $_SESSION['mensagem_tipo'] = 'erro';
  }

  header('Location: perfil.php');
  exit;
}

$mensagem = $_SESSION['mensagem'] ?? '';
$mensagemTipo = $_SESSION['mensagem_tipo'] ?? 'sucesso';

unset($_SESSION['mensagem'], $_SESSION['mensagem_tipo']);

// Linhas para o select de setor / linha principal
$linhas = $conexao->query('SELECT id, codigo_linha, nome_linha FROM linhas WHERE ativo = 1 ORDER BY codigo_linha');

// Valores de exibição (com padrões quando o usuário ainda não é operador)
$nomeExibicao   = $perfil['nome_completo'] ?? ($perfil['nome_usuario'] ?? $perfil['email']);
$cargoExibicao  = $perfil['cargo_funcao'] ?? ucfirst((string) $perfil['cargo_usuario']);
$matricula      = $perfil['matricula'] ?? '—';
$setor          = $perfil['setor_descricao'] ?? '—';
$foto           = $perfil['foto_perfil_url'] ?: 'imagens/foto_perfil.webp';
$turnoAtual     = $perfil['turno_trabalho'] ?? 'diurno1';
$statusAtual    = $statusTurno[$perfil['status_turno'] ?? 'inativo'] ?? $statusTurno['inativo'];
$linhaAtual     = $perfil['linha_principal_id'];

$preferencia = function (string $campo) use ($perfil): string {
  return !empty($perfil[$campo]) ? 'checked' : '';
};

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Perfil do Operador - SuperTrain</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="estilo/perfil.css" />
</head>

<body>
  <header>
    <!-- NAV FLUTUANTE -->
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
              <a class="nav-link" href="usuarios.php">
                <i class="bi bi-people-fill"></i> usuários
              </a>
            </li>

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
              <a class="nav-link active" aria-current="page" href="perfil.php">
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
    <div class="container perfil-container">

      <!-- HERO / BANNER DE IDENTIFICAÇÃO DO OPERADOR -->
      <section class="card card-bg-blur perfil-hero-card">
        <div class="perfil-hero-content">
          <div class="perfil-avatar-wrapper">
            <img src="<?= htmlspecialchars($foto) ?>" alt="Foto do Operador" class="perfil-avatar-img" />
            <span class="perfil-avatar-badge" title="Operador Conectado"></span>
          </div>

          <div class="perfil-info">
            <h1 class="perfil-nome"><?= htmlspecialchars($nomeExibicao) ?></h1>
            <div class="perfil-cargo">
              <i class="bi bi-shield-check"></i> <?= htmlspecialchars($cargoExibicao) ?>
            </div>
            <div class="perfil-meta-tags">
              <span class="meta-tag">
                <i class="bi bi-person-badge"></i> Matrícula: <strong><?= htmlspecialchars($matricula) ?></strong>
              </span>
              <span class="meta-tag">
                <i class="bi bi-geo-alt"></i> Setor: <strong><?= htmlspecialchars($setor) ?></strong>
              </span>
              <span class="meta-tag">
                <i class="bi bi-clock"></i> Turno: <strong><?= htmlspecialchars($turnos[$turnoAtual]['resumo'] ?? '—') ?></strong>
              </span>
              <span class="status-ativo">
                <span class="status-dot" <?= $statusAtual['cor'] ? 'style="background-color: ' . $statusAtual['cor'] . ';"' : '' ?>></span>
                <?= htmlspecialchars($statusAtual['rotulo']) ?>
              </span>
            </div>
          </div>
        </div>
      </section>

      <!-- GRID PRINCIPAL -->
      <div class="row g-4">

        <!-- COLUNA ESQUERDA: ESTATÍSTICAS -->
        <div class="col-lg-4">
          <!-- CARD DE INDICADORES DO OPERADOR -->
          <div class="card card-bg-blur perfil-card perfil-stats-card">
            <div class="perfil-stats-header">
              <h2 class="card-section-title">
                <i class="bi bi-speedometer2"></i> Indicadores do Turno
              </h2>
              <p class="card-section-subtitle">Métricas acumuladas do mês corrente</p>
            </div>

            <div class="perfil-stats-grid">
              <div class="stat-item">
                <span class="stat-item-label">
                  <i class="bi bi-clock-history text-primary"></i> Horas em Escala
                </span>
                <span class="stat-item-value"><?= (int) ($perfil['horas_em_escala_acumuladas'] ?? 0) ?>h</span>
              </div>

              <div class="stat-item">
                <span class="stat-item-label">
                  <i class="bi bi-train-front text-info"></i> Trens Despachados
                </span>
                <span class="stat-item-value"><?= number_format((int) ($perfil['trens_despachados_acumulados'] ?? 0), 0, ',', '.') ?></span>
              </div>

              <div class="stat-item">
                <span class="stat-item-label">
                  <i class="bi bi-check-circle text-success"></i> Pontualidade das Linhas
                </span>
                <span class="stat-item-value text-success">
                  <?= $perfil['pontualidade_linhas_percentual'] !== null
                    ? number_format((float) $perfil['pontualidade_linhas_percentual'], 1, '.', '') . '%'
                    : '—' ?>
                </span>
              </div>

              <div class="stat-item">
                <span class="stat-item-label">
                  <i class="bi bi-exclamation-triangle text-warning"></i> Alertas Atendidos
                </span>
                <span class="stat-item-value text-warning"><?= (int) ($perfil['alertas_atendidos_acumulados'] ?? 0) ?></span>
              </div>
            </div>
          </div>
        </div>

        <!-- COLUNA DIREITA: DADOS CADASTRAIS, SEGURANÇA E PREFERÊNCIAS -->
        <div class="col-lg-8">
          <form id="formPerfilOperador" method="post">
            <input type="hidden" name="salvar_perfil" value="1" />

            <!-- FORMULÁRIO DE DADOS CADASTRAIS & FUNCIONAIS -->
            <div class="card card-bg-blur perfil-card">
              <h2 class="card-section-title">
                <i class="bi bi-person-vcard"></i> Informações Cadastrais & Funcionais
              </h2>
              <p class="card-section-subtitle">Gerencie suas informações profissionais e de contato</p>

              <div class="row g-3">
                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="inputNomeCompleto" class="form-label">Nome Completo</label>
                    <input type="text" id="inputNomeCompleto" name="nome_completo" class="form-control"
                      value="<?= htmlspecialchars($perfil['nome_completo'] ?? '') ?>" maxlength="150" required />
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="inputEmailCorp" class="form-label">E-mail Corporativo</label>
                    <input type="email" id="inputEmailCorp" name="email" class="form-control"
                      value="<?= htmlspecialchars($perfil['email']) ?>" maxlength="150" required />
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="inputRamal" class="form-label">Telefone / Ramal CCO</label>
                    <input type="text" id="inputRamal" name="telefone_ramal" class="form-control"
                      value="<?= htmlspecialchars($perfil['telefone_ramal'] ?? '') ?>" maxlength="60" />
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="inputCargoFuncao" class="form-label">Cargo / Função</label>
                    <input type="text" id="inputCargoFuncao" name="cargo_funcao" class="form-control"
                      value="<?= htmlspecialchars($perfil['cargo_funcao'] ?? '') ?>" maxlength="100" />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group mb-3">
                    <label for="inputMatricula" class="form-label">Matrícula Operacional</label>
                    <input type="text" id="inputMatricula" class="form-control" value="<?= htmlspecialchars($matricula) ?>" disabled />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group mb-3">
                    <label for="selectSetorLinha" class="form-label">Setor / Linha Principal</label>
                    <select id="selectSetorLinha" name="linha_principal_id" class="form-select">
                      <?php while ($linha = $linhas->fetch_assoc()): ?>
                        <option value="<?= (int) $linha['id'] ?>" <?= (int) $linhaAtual === (int) $linha['id'] ? 'selected' : '' ?>>
                          <?= htmlspecialchars($linha['nome_linha']) ?>
                        </option>
                      <?php endwhile; ?>
                      <option value="geral" <?= $linhaAtual === null ? 'selected' : '' ?>>Controle Geral de Malha</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group mb-3">
                    <label for="selectTurnoTrabalho" class="form-label">Turno de Operação</label>
                    <select id="selectTurnoTrabalho" name="turno_trabalho" class="form-select">
                      <?php foreach ($turnos as $valor => $turno): ?>
                        <option value="<?= $valor ?>" <?= $turnoAtual === $valor ? 'selected' : '' ?>>
                          <?= htmlspecialchars($turno['rotulo']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- CARD DE SEGURANÇA E ACESSO -->
            <div class="card card-bg-blur perfil-card mt-4">
              <h2 class="card-section-title">
                <i class="bi bi-shield-lock"></i> Segurança & Credenciais
              </h2>
              <p class="card-section-subtitle">Mantenha seu acesso ao CCO protegido e atualizado</p>

              <div class="row g-3">
                <div class="col-md-4">
                  <div class="form-group mb-3">
                    <label for="inputSenhaAtual" class="form-label">Senha Atual</label>
                    <input type="password" id="inputSenhaAtual" name="senha_atual" class="form-control"
                      placeholder="••••••••••••" autocomplete="current-password" />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group mb-3">
                    <label for="inputNovaSenha" class="form-label">Nova Senha</label>
                    <input type="password" id="inputNovaSenha" name="nova_senha" class="form-control"
                      placeholder="Nova senha segura" autocomplete="new-password" />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group mb-3">
                    <label for="inputConfirmarSenha" class="form-label">Confirmar Senha</label>
                    <input type="password" id="inputConfirmarSenha" name="confirmar_senha" class="form-control"
                      placeholder="Repita a senha" autocomplete="new-password" />
                  </div>
                </div>
              </div>

              <div class="preference-row mt-2">
                <div class="preference-info">
                  <h4>Autenticação em Duas Etapas (2FA)</h4>
                  <p>Adiciona uma camada extra de verificação ao entrar no sistema</p>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="check2FA" name="autenticacao_2fa" value="1" <?= $preferencia('autenticacao_2fa') ?> />
                </div>
              </div>
            </div>

            <!-- CARD DE PREFERÊNCIAS DO SISTEMA -->
            <div class="card card-bg-blur perfil-card mt-4">
              <h2 class="card-section-title">
                <i class="bi bi-sliders"></i> Preferências do Sistema & Alertas CCO
              </h2>
              <p class="card-section-subtitle">Configure notificações operacionais e alertas sonoros em tempo real</p>

              <div class="preference-row">
                <div class="preference-info">
                  <h4>Alertas Sonoros de Emergência da Malha</h4>
                  <p>Reproduzir aviso sonoro ao detectar bloqueio de via ou parada não programada</p>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="checkAlertaSonoro" name="alerta_sonoro_emergencia" value="1" <?= $preferencia('alerta_sonoro_emergencia') ?> />
                </div>
              </div>

              <div class="preference-row">
                <div class="preference-info">
                  <h4>Notificação de Desvio de Telemetria</h4>
                  <p>Receber avisos imediatos quando a velocidade exceder o limite do trecho</p>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="checkTelemetria" name="notificacao_telemetria" value="1" <?= $preferencia('notificacao_telemetria') ?> />
                </div>
              </div>

              <div class="preference-row">
                <div class="preference-info">
                  <h4>Relatório Automático ao Fechar Turno</h4>
                  <p>Gerar e enviar por e-mail o compilado do turno operacional ao realizar logout</p>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="checkRelatorioAuto" name="relatorio_auto_fechamento" value="1" <?= $preferencia('relatorio_auto_fechamento') ?> />
                </div>
              </div>
            </div>

            <!-- BOTÕES DE AÇÃO -->
            <div class="perfil-actions-bar">
              <a href="sair.php" class="btn-danger-custom">
                <i class="bi bi-box-arrow-right"></i> Sair da Conta
              </a>
              <button type="button" class="btn-secondary-custom" onclick="window.history.back()">
                <i class="bi bi-x-circle"></i> Cancelar
              </button>
              <button type="submit" class="btn btn-primary" id="btnSalvarPerfil">
                <i class="bi bi-check2-circle"></i> Salvar Alterações
              </button>
            </div>
          </form>

        </div>
      </div>

    </div>
  </main>

  <?php if ($mensagem !== ''): ?>
    <!-- TOAST DE FEEDBACK -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
      <div id="toastFeedback" class="toast align-items-center <?= $mensagemTipo === 'erro' ? 'text-bg-danger' : 'text-bg-primary' ?> border-0"
        role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">
            <i class="bi <?= $mensagemTipo === 'erro' ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' ?> me-2"></i>
            <?= htmlspecialchars($mensagem) ?>
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const toastEl = document.getElementById('toastFeedback');
    if (toastEl) {
      new bootstrap.Toast(toastEl).show();
    }
  </script>
</body>

</html>