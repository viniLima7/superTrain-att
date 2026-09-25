<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Relatórios - SuperTrain</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="estilo/relatorios.css" />
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
              <a class="nav-link active" aria-current="page" href="relatorios.html">
                <i class="bi bi-file-earmark-bar-graph"></i> Relatórios
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="perfil.html">
                <i class="bi bi-person-circle"></i> Perfil
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <div class="container relatorios-container">

      <!-- HEADER / HERO -->
      <section class="card card-bg-blur relatorios-header-card">
        <h1 class="page-title">Central de Relatórios & Telemetria</h1>
        <p class="page-subtitle">
          <i class="bi bi-bar-chart-line text-primary"></i> Geração de relatórios operacionais, auditoria de dados e exportação de telemetria
        </p>
      </section>

      <!-- PAINEL DE FILTROS & GERADOR DE RELATÓRIOS -->
      <section class="card card-bg-blur filter-card">
        <h2 class="filter-title">
          <i class="bi bi-funnel"></i> Parâmetros do Relatório
        </h2>

        <form id="formFiltroRelatorios">
          <div class="row g-3">
            <div class="col-md-4">
              <label for="selectTipoRelatorio" class="form-label">Tipo de Relatório</label>
              <select id="selectTipoRelatorio" class="form-select">
                <option value="pontualidade" selected>Desempenho & Pontualidade</option>
                <option value="lotacao">Lotação & Fluxo de Passageiros</option>
                <option value="ocorrencias">Registro de Manutenções & Incidentes</option>
                <option value="telemetria">Telemetria & Sensores da Malha</option>
                <option value="energia">Eficiência & Consumo Energético</option>
              </select>
            </div>

            <div class="col-md-3">
              <label for="selectLinhaFiltro" class="form-label">Linha / Trajeto</label>
              <select id="selectLinhaFiltro" class="form-select">
                <option value="todas" selected>Todas as Linhas</option>
                <option value="130">Linha 130 (Norte/Sul)</option>
                <option value="140">Linha 140 (Sul/Norte)</option>
                <option value="142">Linha 142 (Oeste/Leste)</option>
                <option value="174">Linha 174 (Leste/Norte)</option>
                <option value="001">Linha 001 (Centro)</option>
              </select>
            </div>

            <div class="col-md-3">
              <label for="inputDataInicio" class="form-label">Data Inicial</label>
              <input type="date" id="inputDataInicio" class="form-control" value="2026-08-01" />
            </div>

            <div class="col-md-2">
              <label for="inputDataFim" class="form-label">Data Final</label>
              <input type="date" id="inputDataFim" class="form-control" value="2026-08-18" />
            </div>
          </div>

          <div class="export-toolbar">
            <button type="button" class="btn btn-primary" id="btnFiltrar">
              <i class="bi bi-search"></i> Filtrar Dados
            </button>
            <button type="button" class="btn-export btn-export-pdf" onclick="mostrarNotificacao('Exportando PDF consolidado...')">
              <i class="bi bi-filetype-pdf text-danger"></i> Exportar PDF
            </button>
            <button type="button" class="btn-export btn-export-excel" onclick="mostrarNotificacao('Exportando planilha CSV/Excel...')">
              <i class="bi bi-file-earmark-spreadsheet text-success"></i> Exportar CSV
            </button>
            <button type="button" class="btn-export" onclick="window.print()">
              <i class="bi bi-printer"></i> Imprimir
            </button>
          </div>
        </form>
      </section>

      <!-- CARDS DE KPIS / INDICADORES -->
      <section class="mb-4">
        <div class="row g-3">
          <div class="col-md-6 col-xl-3">
            <div class="kpi-card">
              <div class="kpi-header">
                <div class="kpi-icon-frame">
                  <i class="bi bi-clock-history"></i>
                </div>
                <span class="kpi-trend positive">
                  <i class="bi bi-arrow-up-short"></i> +1.2%
                </span>
              </div>
              <div class="kpi-value text-success">98.4%</div>
              <p class="kpi-label">Índice de Pontualidade Global</p>
            </div>
          </div>

          <div class="col-md-6 col-xl-3">
            <div class="kpi-card">
              <div class="kpi-header">
                <div class="kpi-icon-frame">
                  <i class="bi bi-people"></i>
                </div>
                <span class="kpi-trend neutral">
                  <i class="bi bi-dash"></i> Estável
                </span>
              </div>
              <div class="kpi-value">342.180</div>
              <p class="kpi-label">Passageiros Transportados</p>
            </div>
          </div>

          <div class="col-md-6 col-xl-3">
            <div class="kpi-card">
              <div class="kpi-header">
                <div class="kpi-icon-frame">
                  <i class="bi bi-lightning-charge"></i>
                </div>
                <span class="kpi-trend positive">
                  <i class="bi bi-arrow-down-short"></i> -4.8%
                </span>
              </div>
              <div class="kpi-value text-info">4.1 kWh/km</div>
              <p class="kpi-label">Consumo Energético Médio</p>
            </div>
          </div>

          <div class="col-md-6 col-xl-3">
            <div class="kpi-card">
              <div class="kpi-header">
                <div class="kpi-icon-frame">
                  <i class="bi bi-check2-circle"></i>
                </div>
                <span class="kpi-trend positive">
                  <i class="bi bi-check"></i> 100% Ok
                </span>
              </div>
              <div class="kpi-value">99.2%</div>
              <p class="kpi-label">Confiabilidade Operacional</p>
            </div>
          </div>
        </div>
      </section>

      <!-- DISTRIBUIÇÃO VISUAL & GRÁFICOS DE FLUXO -->
      <section class="card card-bg-blur visual-section-card">
        <div class="card-header-custom mb-3">
          <h2 class="card-title">Distribuição de Fluxo por Linha Ferroviária</h2>
          <p class="card-subtitle">
            <i class="bi bi-graph-up"></i> Volume de operação e ocupação relativa
          </p>
        </div>

        <div class="row g-4">
          <div class="col-md-6">
            <div class="fluxo-bar-item">
              <div class="fluxo-bar-label">
                <span>Linha 130 (Norte / Sul)</span>
                <span>88% Fluxo Alto</span>
              </div>
              <div class="progress" role="progressbar" aria-valuenow="88" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-danger" style="width: 88%"></div>
              </div>
            </div>

            <div class="fluxo-bar-item mt-3">
              <div class="fluxo-bar-label">
                <span>Linha 140 (Sul / Norte)</span>
                <span>65% Fluxo Moderado</span>
              </div>
              <div class="progress" role="progressbar" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-warning" style="width: 65%"></div>
              </div>
            </div>

            <div class="fluxo-bar-item mt-3">
              <div class="fluxo-bar-label">
                <span>Linha 142 (Oeste / Leste)</span>
                <span>45% Fluxo Normal</span>
              </div>
              <div class="progress" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-success" style="width: 45%"></div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="fluxo-bar-item">
              <div class="fluxo-bar-label">
                <span>Linha 174 (Leste / Norte)</span>
                <span>52% Fluxo Moderado</span>
              </div>
              <div class="progress" role="progressbar" aria-valuenow="52" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-warning" style="width: 52%"></div>
              </div>
            </div>

            <div class="fluxo-bar-item mt-3">
              <div class="fluxo-bar-label">
                <span>Linha 001 (Centro - Expresso)</span>
                <span>94% Fluxo Intenso</span>
              </div>
              <div class="progress" role="progressbar" aria-valuenow="94" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-danger" style="width: 94%"></div>
              </div>
            </div>

            <div class="fluxo-bar-item mt-3">
              <div class="fluxo-bar-label">
                <span>Pátio de Manobras e Cargas</span>
                <span>30% Capacidade Livre</span>
              </div>
              <div class="progress" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-success" style="width: 30%"></div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- TABELA DE REGISTROS DE RELATÓRIO -->
      <section class="card card-bg-blur table-card">
        <div class="table-header-custom">
          <div>
            <h2 class="table-title">Histórico de Relatórios Operacionais</h2>
            <p class="card-subtitle">Registros auditados dos últimos turnos e manutenções</p>
          </div>
          <span class="widget-badge">
            <i class="bi bi-database-check"></i> 5 registros encontrados
          </span>
        </div>

        <div class="table-responsive">
          <table class="table text-center align-middle">
            <thead>
              <tr>
                <th scope="col">Protocolo</th>
                <th scope="col">Data / Hora</th>
                <th scope="col">Linha / Composição</th>
                <th scope="col">Categoria</th>
                <th scope="col">Operador CCO</th>
                <th scope="col">Status</th>
                <th scope="col">Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="report-id">#REL-2026-89</td>
                <td>18/08/2026 • 08:30</td>
                <td>Linha 130 - Trem 01</td>
                <td>Pontualidade & Fluxo</td>
                <td>Carlos E. Silva</td>
                <td>
                  <span class="status-ativo">
                    <span class="status-dot"></span> Regular (99%)
                  </span>
                </td>
                <td>
                  <button class="btn btn-action-table" data-bs-toggle="modal" data-bs-target="#modalDetalheRelatorio"
                    onclick="abrirModalRelatorio('REL-2026-89', '18/08/2026 08:30', 'Linha 130 - Norte/Sul', 'Carlos E. Silva', 'Operação em velocidade de cruzeiro de 37 km/h. Pontualidade mantida dentro da margem de 2 minutos. Telemetria e sensores de via normais.')">
                    <i class="bi bi-eye"></i> Detalhes
                  </button>
                </td>
              </tr>

              <tr>
                <td class="report-id">#REL-2026-88</td>
                <td>18/08/2026 • 07:15</td>
                <td>Linha 142 - Trem 04</td>
                <td>Manutenção Preventiva</td>
                <td>Gabriel C.</td>
                <td>
                  <span class="status-atencao">
                    <span class="status-dot"></span> Concluído
                  </span>
                </td>
                <td>
                  <button class="btn btn-action-table" data-bs-toggle="modal" data-bs-target="#modalDetalheRelatorio"
                    onclick="abrirModalRelatorio('REL-2026-88', '18/08/2026 07:15', 'Linha 142 - Oeste/Leste', 'Gabriel C.', 'Inspeção térmica dos freios magnéticos realizada no Terminal Centro. Sensor calibrado com sucesso e composição liberada.')">
                    <i class="bi bi-eye"></i> Detalhes
                  </button>
                </td>
              </tr>

              <tr>
                <td class="report-id">#REL-2026-87</td>
                <td>17/08/2026 • 18:40</td>
                <td>Linha 001 - Expresso</td>
                <td>Lotação & Horário de Pico</td>
                <td>Carlos E. Silva</td>
                <td>
                  <span class="status-parado">
                    <span class="status-dot"></span> Lotação 94%
                  </span>
                </td>
                <td>
                  <button class="btn btn-action-table" data-bs-toggle="modal" data-bs-target="#modalDetalheRelatorio"
                    onclick="abrirModalRelatorio('REL-2026-87', '17/08/2026 18:40', 'Linha 001 - Centro Expresso', 'Carlos E. Silva', 'Pico de passageiros às 18h30. Tempo de parada nas plataformas ampliado em 25 segundos para embarque seguro.')">
                    <i class="bi bi-eye"></i> Detalhes
                  </button>
                </td>
              </tr>

              <tr>
                <td class="report-id">#REL-2026-86</td>
                <td>17/08/2026 • 14:10</td>
                <td>Linha 174 - Trem 03</td>
                <td>Telemetria & Sensores</td>
                <td>Vinícius L.</td>
                <td>
                  <span class="status-ativo">
                    <span class="status-dot"></span> Normal
                  </span>
                </td>
                <td>
                  <button class="btn btn-action-table" data-bs-toggle="modal" data-bs-target="#modalDetalheRelatorio"
                    onclick="abrirModalRelatorio('REL-2026-86', '17/08/2026 14:10', 'Linha 174 - Leste/Norte', 'Vinícius L.', 'Leitura contínua de proximidade e sensor RFID operando com latência inferior a 12ms. Rota cumprida sem ocorrências.')">
                    <i class="bi bi-eye"></i> Detalhes
                  </button>
                </td>
              </tr>

              <tr>
                <td class="report-id">#REL-2026-85</td>
                <td>17/08/2026 • 09:20</td>
                <td>Linha 140 - Carga 02</td>
                <td>Eficiência Energética</td>
                <td>Carlos E. Silva</td>
                <td>
                  <span class="status-ativo">
                    <span class="status-dot"></span> Econômico
                  </span>
                </td>
                <td>
                  <button class="btn btn-action-table" data-bs-toggle="modal" data-bs-target="#modalDetalheRelatorio"
                    onclick="abrirModalRelatorio('REL-2026-85', '17/08/2026 09:20', 'Linha 140 - Sul/Norte', 'Carlos E. Silva', 'Frenagem regenerativa recuperou 18% da energia cinética no trecho montanhoso. Consumo médio de 3.9 kWh/km.')">
                    <i class="bi bi-eye"></i> Detalhes
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

    </div>
  </main>

  <!-- MODAL DE DETALHES DO RELATÓRIO -->
  <div class="modal fade" id="modalDetalheRelatorio" tabindex="-1" aria-labelledby="modalDetalheRelatorioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content card-bg-blur">
        <div class="modal-header">
          <h1 class="modal-title" id="modalDetalheRelatorioLabel">
            <i class="bi bi-file-earmark-text text-primary me-2"></i> Relatório Operacional <span id="modalProtocolo" class="text-primary"></span>
          </h1>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label">Data e Hora do Registro</label>
              <div id="modalDataHora" class="form-control" style="background: rgba(0,0,0,0.3) !important;"></div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Linha / Sentido</label>
              <div id="modalLinha" class="form-control" style="background: rgba(0,0,0,0.3) !important;"></div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Operador Responsável</label>
              <div id="modalOperador" class="form-control" style="background: rgba(0,0,0,0.3) !important;"></div>
            </div>
          </div>

          <label class="form-label">Parecer Técnico & Telemetria Registrada</label>
          <div class="report-preview-box mb-3">
            <p id="modalParecer" class="mb-0 text-light" style="font-size: 0.95rem; line-height: 1.6;"></p>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <span class="status-ativo">
              <span class="status-dot"></span> Autenticado pelo CCO SuperTrain
            </span>
            <span class="text-muted" style="font-size: 0.8rem; font-family: var(--font-mono);">Assinatura Digital SHA-256</span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Fechar</button>
          <button type="button" class="btn btn-primary" onclick="mostrarNotificacao('Download do PDF do relatório iniciado!')">
            <i class="bi bi-download"></i> Baixar Relatório
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- TOAST DE FEEDBACK -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
    <div id="toastRelatorio" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body" id="toastRelatorioMensagem">
          <i class="bi bi-check-circle-fill me-2"></i> Ação executada com sucesso!
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function abrirModalRelatorio(protocolo, dataHora, linha, operador, parecer) {
      document.getElementById('modalProtocolo').innerText = '#' + protocolo;
      document.getElementById('modalDataHora').innerText = dataHora;
      document.getElementById('modalLinha').innerText = linha;
      document.getElementById('modalOperador').innerText = operador;
      document.getElementById('modalParecer').innerText = parecer;
    }

    function mostrarNotificacao(msg) {
      const toastEl = document.getElementById('toastRelatorio');
      document.getElementById('toastRelatorioMensagem').innerHTML = '<i class="bi bi-info-circle-fill me-2"></i> ' + msg;
      const toast = new bootstrap.Toast(toastEl);
      toast.show();
    }

    document.getElementById('btnFiltrar').addEventListener('click', function () {
      mostrarNotificacao('Filtros aplicados! Registros operacionais atualizados.');
    });
  </script>
</body>

</html>
