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
        <a class="navbar-brand" href="dashboard.html">
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
              <a class="nav-link" href="dashboard.html">
                <i class="bi bi-grid-fill"></i> Dashboard
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="trens.html">
                <i class="bi bi-train-front"></i> Trens
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="rotas.html">
                <i class="bi bi-signpost-split"></i> Rotas
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="sensores.html">
                <i class="bi bi-broadcast-pin"></i> Sensores
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="relatorios.html">
                <i class="bi bi-file-earmark-bar-graph"></i> Relatórios
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="perfil.html">
                <i class="bi bi-person-circle"></i> Perfil
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
            <img src="imagens/foto_perfil.webp" alt="Foto do Operador" class="perfil-avatar-img" />
            <span class="perfil-avatar-badge" title="Operador Conectado"></span>
          </div>

          <div class="perfil-info">
            <h1 class="perfil-nome">Carlos Eduardo Silva</h1>
            <div class="perfil-cargo">
              <i class="bi bi-shield-check"></i> Operador CCO Sênior / Administrador
            </div>
            <div class="perfil-meta-tags">
              <span class="meta-tag">
                <i class="bi bi-person-badge"></i> Matrícula: <strong>ST-0482</strong>
              </span>
              <span class="meta-tag">
                <i class="bi bi-geo-alt"></i> Setor: <strong>CCO Central - Malha Sul</strong>
              </span>
              <span class="meta-tag">
                <i class="bi bi-clock"></i> Turno: <strong>06:00 - 14:00 (Diurno)</strong>
              </span>
              <span class="status-ativo">
                <span class="status-dot"></span> Em Turno Ativo
              </span>
            </div>
          </div>
        </div>
      </section>

      <!-- GRID PRINCIPAL -->
      <div class="row g-4">

        <!-- COLUNA ESQUERDA: ESTATÍSTICAS & ATIVIDADE -->
        <div class="col-lg-4">
          <!-- CARD DE INDICADORES DO OPERADOR -->
          <div class="card card-bg-blur perfil-card">
            <h2 class="card-section-title">
              <i class="bi bi-speedometer2"></i> Indicadores do Turno
            </h2>
            <p class="card-section-subtitle">Métricas acumuladas do mês corrente</p>

            <div class="stat-item">
              <span class="stat-item-label">
                <i class="bi bi-clock-history text-primary"></i> Horas em Escala
              </span>
              <span class="stat-item-value">168h</span>
            </div>

            <div class="stat-item">
              <span class="stat-item-label">
                <i class="bi bi-train-front text-info"></i> Trens Despachados
              </span>
              <span class="stat-item-value">1.420</span>
            </div>

            <div class="stat-item">
              <span class="stat-item-label">
                <i class="bi bi-check-circle text-success"></i> Pontualidade das Linhas
              </span>
              <span class="stat-item-value text-success">98.4%</span>
            </div>

            <div class="stat-item">
              <span class="stat-item-label">
                <i class="bi bi-exclamation-triangle text-warning"></i> Alertas Atendidos
              </span>
              <span class="stat-item-value text-warning">47</span>
            </div>
          </div>

          <!-- CARD DE ATIVIDADES RECENTES -->
          <div class="card card-bg-blur perfil-card mt-4">
            <h2 class="card-section-title">
              <i class="bi bi-activity"></i> Log de Ações
            </h2>
            <p class="card-section-subtitle">Últimas operações executadas</p>

            <div class="activity-item">
              <div>
                <p class="activity-text">Atualizou status do <strong>Trem 130</strong> para Ativo</p>
                <span class="activity-time">Hoje às 08:42</span>
              </div>
            </div>

            <div class="activity-item">
              <div>
                <p class="activity-text">Registrou novo sensor térmico no <strong>Terminal Centro</strong></p>
                <span class="activity-time">Hoje às 07:15</span>
              </div>
            </div>

            <div class="activity-item">
              <div>
                <p class="activity-text">Aprovou desvio de rota na <strong>Linha 142 (Oeste/Leste)</strong></p>
                <span class="activity-time">Ontem às 13:50</span>
              </div>
            </div>

            <div class="activity-item">
              <div>
                <p class="activity-text">Exportou relatório mensal de pontualidade da malha</p>
                <span class="activity-time">Ontem às 11:20</span>
              </div>
            </div>
          </div>
        </div>

        <!-- COLUNA DIREITA: DADOS CADASTRAIS, SEGURANÇA E PREFERÊNCIAS -->
        <div class="col-lg-8">

          <!-- FORMULÁRIO DE DADOS CADASTRAIS & FUNCIONAIS -->
          <div class="card card-bg-blur perfil-card">
            <h2 class="card-section-title">
              <i class="bi bi-person-vcard"></i> Informações Cadastrais & Funcionais
            </h2>
            <p class="card-section-subtitle">Gerencie suas informações profissionais e de contato</p>

            <form id="formPerfilOperador">
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="inputNomeCompleto" class="form-label">Nome Completo</label>
                    <input type="text" id="inputNomeCompleto" class="form-control" value="Carlos Eduardo Silva" required />
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="inputEmailCorp" class="form-label">E-mail Corporativo</label>
                    <input type="email" id="inputEmailCorp" class="form-control" value="carlos.silva@supertrain.com" required />
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="inputRamal" class="form-label">Telefone / Ramal CCO</label>
                    <input type="text" id="inputRamal" class="form-control" value="(47) 3451-9842 • Ramal 204" />
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="inputCargoFuncao" class="form-label">Cargo / Função</label>
                    <input type="text" id="inputCargoFuncao" class="form-control" value="Operador CCO Sênior" />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group mb-3">
                    <label for="inputMatricula" class="form-label">Matrícula Operacional</label>
                    <input type="text" id="inputMatricula" class="form-control" value="ST-0482" disabled />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group mb-3">
                    <label for="selectSetorLinha" class="form-label">Setor / Linha Principal</label>
                    <select id="selectSetorLinha" class="form-select">
                      <option value="130" selected>Linha 130 (Norte/Sul)</option>
                      <option value="140">Linha 140 (Sul/Norte)</option>
                      <option value="142">Linha 142 (Oeste/Leste)</option>
                      <option value="174">Linha 174 (Leste/Norte)</option>
                      <option value="001">Linha 001 (Centro)</option>
                      <option value="geral">Controle Geral de Malha</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group mb-3">
                    <label for="selectTurnoTrabalho" class="form-label">Turno de Operação</label>
                    <select id="selectTurnoTrabalho" class="form-select">
                      <option value="diurno1" selected>Diurno (06:00 - 14:00)</option>
                      <option value="vespertino">Vespertino (14:00 - 22:00)</option>
                      <option value="noturno">Noturno (22:00 - 06:00)</option>
                      <option value="escala">Escala 12x36</option>
                    </select>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <!-- CARD DE SEGURANÇA E ACESSO -->
          <div class="card card-bg-blur perfil-card mt-4">
            <h2 class="card-section-title">
              <i class="bi bi-shield-lock"></i> Segurança & Credenciais
            </h2>
            <p class="card-section-subtitle">Mantenha seu acesso ao CCO protegido e atualizado</p>

            <form id="formSeguranca">
              <div class="row g-3">
                <div class="col-md-4">
                  <div class="form-group mb-3">
                    <label for="inputSenhaAtual" class="form-label">Senha Atual</label>
                    <input type="password" id="inputSenhaAtual" class="form-control" placeholder="••••••••••••" />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group mb-3">
                    <label for="inputNovaSenha" class="form-label">Nova Senha</label>
                    <input type="password" id="inputNovaSenha" class="form-control" placeholder="Nova senha segura" />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group mb-3">
                    <label for="inputConfirmarSenha" class="form-label">Confirmar Senha</label>
                    <input type="password" id="inputConfirmarSenha" class="form-control" placeholder="Repita a senha" />
                  </div>
                </div>
              </div>

              <div class="preference-row mt-2">
                <div class="preference-info">
                  <h4>Autenticação em Duas Etapas (2FA)</h4>
                  <p>Adiciona uma camada extra de verificação ao entrar no sistema</p>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="check2FA" checked />
                </div>
              </div>
            </form>
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
                <input class="form-check-input" type="checkbox" id="checkAlertaSonoro" checked />
              </div>
            </div>

            <div class="preference-row">
              <div class="preference-info">
                <h4>Notificação de Desvio de Telemetria</h4>
                <p>Receber avisos imediatos quando a velocidade exceder o limite do trecho</p>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="checkTelemetria" checked />
              </div>
            </div>

            <div class="preference-row">
              <div class="preference-info">
                <h4>Relatório Automático ao Fechar Turno</h4>
                <p>Gerar e enviar por e-mail o compilado do turno operacional ao realizar logout</p>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="checkRelatorioAuto" checked />
              </div>
            </div>
          </div>

          <!-- BOTÕES DE AÇÃO -->
          <div class="perfil-actions-bar">
            <a href="login.html" class="btn-danger-custom">
              <i class="bi bi-box-arrow-right"></i> Sair da Conta
            </a>
            <button type="button" class="btn-secondary-custom" onclick="window.history.back()">
              <i class="bi bi-x-circle"></i> Cancelar
            </button>
            <button type="button" class="btn btn-primary" id="btnSalvarPerfil">
              <i class="bi bi-check2-circle"></i> Salvar Alterações
            </button>
          </div>

        </div>
      </div>

    </div>
  </main>

  <!-- TOAST DE FEEDBACK -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
    <div id="toastFeedback" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <i class="bi bi-check-circle-fill me-2"></i> Alterações de perfil salvas com sucesso!
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('btnSalvarPerfil').addEventListener('click', function () {
      const toastEl = document.getElementById('toastFeedback');
      const toast = new bootstrap.Toast(toastEl);
      toast.show();
    });
  </script>
</body>

</html>
