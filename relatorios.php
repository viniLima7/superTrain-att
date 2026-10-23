<?php
// 1. Inicia a sessão se ela ainda não estiver ativa
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2. Se o usuário não estiver logado, redireciona para a tela de login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// 3. Inclui a conexão com o banco de dados
require 'config.php';

// 4. Pega os filtros do formulário (com valores padrão se estiverem vazios)
$tipo   = $_GET['tipo'] ?? 'todos';
$linha  = $_GET['linha'] ?? 'todas';
$inicio = $_GET['data_inicio'] ?? '';
$fim    = $_GET['data_fim'] ?? '';

// ----------------------------------------------------
// CONSULTA 1: Busca as linhas ativas para o filtro <select>
// ----------------------------------------------------
$linhas = $conexao->query("
    SELECT id, codigo_linha, nome_linha
    FROM linhas
    WHERE ativo = TRUE
    ORDER BY codigo_linha
");

// ----------------------------------------------------
// CONSULTA 2: Busca os indicadores (KPIs)
// ----------------------------------------------------
$whereKpi = [];
$paramsKpi = [];
$tiposKpi = '';

if ($linha != 'todas') {
    $whereKpi[] = 'linha_id = ?';
    $paramsKpi[] = (int)$linha;
    $tiposKpi .= 'i';
}
if ($inicio) {
    $whereKpi[] = 'data_referencia >= ?';
    $paramsKpi[] = $inicio;
    $tiposKpi .= 's';
}
if ($fim) {
    $whereKpi[] = 'data_referencia <= ?';
    $paramsKpi[] = $fim;
    $tiposKpi .= 's';
}

$sqlKpi = "
    SELECT pontualidade_global_percentual, passageiros_transportados,
           consumo_energetico_kwh_km, confiabilidade_operacional_percentual
    FROM kpis_malha_historico
";

if ($whereKpi) {
    $sqlKpi .= ' WHERE ' . implode(' AND ', $whereKpi);
}
$sqlKpi .= ' ORDER BY data_referencia DESC LIMIT 1';

$stmt = $conexao->prepare($sqlKpi);
if ($paramsKpi) {
    $stmt->bind_param($tiposKpi, ...$paramsKpi);
}
$stmt->execute();

$kpi = $stmt->get_result()->fetch_assoc() ?: [];

// Váriaveis com valores padrão (caso não encontre nada no banco)
$pontualidade  = $kpi['pontualidade_global_percentual'] ?? 0;
$passageiros   = $kpi['passageiros_transportados'] ?? 0;
$consumo       = $kpi['consumo_energetico_kwh_km'] ?? 0;
$confiabilidade = $kpi['confiabilidade_operacional_percentual'] ?? 0;

// ----------------------------------------------------
// CONSULTA 3: Busca a lista de Relatórios
// ----------------------------------------------------
$where = [];
$params = [];
$tipos = '';

if ($tipo != 'todos') {
    $where[] = 'r.tipo_relatorio = ?';
    $params[] = $tipo;
    $tipos .= 's';
}
if ($linha != 'todas') {
    $where[] = 'r.linha_id = ?';
    $params[] = (int)$linha;
    $tipos .= 'i';
}
if ($inicio) {
    $where[] = 'DATE(r.data_hora_registro) >= ?';
    $params[] = $inicio;
    $tipos .= 's';
}
if ($fim) {
    $where[] = 'DATE(r.data_hora_registro) <= ?';
    $params[] = $fim;
    $tipos .= 's';
}

$sqlRelatorios = "
    SELECT r.id, r.protocolo, r.tipo_relatorio, r.data_hora_registro,
           r.status_sumario, r.parecer_tecnico,
           r.assinatura_digital_sha256,
           l.codigo_linha, l.nome_linha,
           t.prefixo, t.modelo_composicao,
           o.nome_completo AS operador_nome
    FROM relatorios_operacionais r
    INNER JOIN linhas l ON l.id = r.linha_id
    LEFT JOIN trens t ON t.id = r.trem_id
    INNER JOIN operadores o ON o.id = r.operador_id
";

if ($where) {
    $sqlRelatorios .= ' WHERE ' . implode(' AND ', $where);
}
$sqlRelatorios .= ' ORDER BY r.data_hora_registro DESC';

$stmt = $conexao->prepare($sqlRelatorios);
if ($params) {
    $stmt->bind_param($tipos, ...$params);
}
$stmt->execute();

$relatorios = $stmt->get_result();
$totalRelatorios = $relatorios->num_rows;

// ----------------------------------------------------
// CONSULTA 4: Busca o Fluxo de Lotação por Linha
// ----------------------------------------------------
$whereFluxo = [];
$paramsFluxo = [];
$tiposFluxo = '';

if ($linha != 'todas') {
    $whereFluxo[] = 'l.id = ?';
    $paramsFluxo[] = (int)$linha;
    $tiposFluxo .= 'i';
}
if ($inicio) {
    $whereFluxo[] = 'DATE(tel.data_hora) >= ?';
    $paramsFluxo[] = $inicio;
    $tiposFluxo .= 's';
}
if ($fim) {
    $whereFluxo[] = 'DATE(tel.data_hora) <= ?';
    $paramsFluxo[] = $fim;
    $tiposFluxo .= 's';
}

$sqlFluxo = "
    SELECT l.id, l.codigo_linha, l.nome_linha,
           COALESCE(AVG(tel.ocupacao_percentual), 0) AS ocupacao_media
    FROM linhas l
    LEFT JOIN trens t ON t.linha_id = l.id AND t.ativo = TRUE
    LEFT JOIN telemetria_trens tel ON tel.trem_id = t.id
";

if ($whereFluxo) {
    $sqlFluxo .= ' WHERE ' . implode(' AND ', $whereFluxo);
}

$sqlFluxo .= "
    GROUP BY l.id, l.codigo_linha, l.nome_linha
    ORDER BY ocupacao_media DESC
";

$stmt = $conexao->prepare($sqlFluxo);
if ($paramsFluxo) {
    $stmt->bind_param($tiposFluxo, ...$paramsFluxo);
}
$stmt->execute();

// CORREÇÃO IMPORTANTE: Guardando o resultado do fluxo para rodar no HTML
$fluxos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - SuperTrain</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="estilo/relatorios.css">
</head>

<body>

<!-- NAVEGAÇÃO PRINCIPAL -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">SuperTrain</a>

        <div class="navbar-nav">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
            <a class="nav-link" href="trens.php">Trens</a>
            <a class="nav-link" href="rotas.php">Rotas</a>
            <a class="nav-link" href="sensores.php">Sensores</a>
            <a class="nav-link active" href="relatorios.php">Relatórios</a>
            <a class="nav-link" href="perfil.php">Perfil</a>
            <a class="nav-link" href="sair.php">Sair</a>
        </div>
    </div>
</nav>

<div class="container py-4">

    <h1 class="mb-4">Relatórios Operacionais</h1>

    <!-- FORMULÁRIO DE FILTROS -->
    <form method="GET" class="row g-3 mb-4">

        <div class="col-md-3">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-select">
                <option value="todos">Todos</option>
                <option value="pontualidade" <?= $tipo == 'pontualidade' ? 'selected' : '' ?>>Pontualidade</option>
                <option value="lotacao" <?= $tipo == 'lotacao' ? 'selected' : '' ?>>Lotação</option>
                <option value="ocorrencias" <?= $tipo == 'ocorrencias' ? 'selected' : '' ?>>Ocorrências</option>
                <option value="telemetria" <?= $tipo == 'telemetria' ? 'selected' : '' ?>>Telemetria</option>
                <option value="energia" <?= $tipo == 'energia' ? 'selected' : '' ?>>Energia</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Linha</label>
            <select name="linha" class="form-select">
                <option value="todas">Todas</option>

                <?php while ($l = $linhas->fetch_assoc()): ?>
                    <option value="<?= $l['id'] ?>" <?= $linha == $l['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($l['codigo_linha'] . ' - ' . $l['nome_linha']) ?>
                    </option>
                <?php endwhile; ?>

            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Data inicial</label>
            <input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($inicio) ?>">
        </div>

        <div class="col-md-2">
            <label class="form-label">Data final</label>
            <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($fim) ?>">
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Filtrar
            </button>
        </div>

    </form>

    <!-- CARDS DE KPIS -->
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card p-3">
                <h6>Pontualidade</h6>
                <h2><?= number_format($pontualidade, 1, ',', '.') ?>%</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <h6>Passageiros</h6>
                <h2><?= number_format($passageiros, 0, ',', '.') ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <h6>Consumo</h6>
                <h2><?= number_format($consumo, 2, ',', '.') ?></h2>
                <small>kWh/km</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <h6>Confiabilidade</h6>
                <h2><?= number_format($confiabilidade, 1, ',', '.') ?>%</h2>
            </div>
        </div>

    </div>

    <!-- SEÇÃO DE FLUXO POR LINHA -->
    <div class="card p-4 mb-4">

        <h4 class="mb-3">Fluxo por linha</h4>

        <?php while ($f = $fluxos->fetch_assoc()): ?>
            <?php $ocupacao = min(100, max(0, (float)$f['ocupacao_media'])); ?>

            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <span>
                        <?= htmlspecialchars($f['codigo_linha']) ?> -
                        <?= htmlspecialchars($f['nome_linha']) ?>
                    </span>
                    <strong><?= number_format($ocupacao, 1, ',', '.') ?>%</strong>
                </div>

                <div class="progress">
                    <div class="progress-bar" style="width: <?= $ocupacao ?>%"></div>
                </div>
            </div>
        <?php endwhile; ?>

    </div>

    <!-- TABELA DE RELATÓRIOS -->
    <div class="card p-4">

        <div class="d-flex justify-content-between mb-3">
            <h4>Relatórios</h4>
            <span class="badge bg-primary"><?= $totalRelatorios ?> registros</span>
        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>Protocolo</th>
                        <th>Tipo</th>
                        <th>Linha</th>
                        <th>Trem</th>
                        <th>Operador</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($totalRelatorios == 0): ?>

                    <tr>
                        <td colspan="8" class="text-center">
                            Nenhum relatório encontrado.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php while ($r = $relatorios->fetch_assoc()): ?>

                        <tr>
                            <td><?= htmlspecialchars($r['protocolo']) ?></td>
                            <td><?= htmlspecialchars($r['tipo_relatorio']) ?></td>
                            <td><?= htmlspecialchars($r['codigo_linha']) ?></td>
                            <td><?= htmlspecialchars($r['prefixo'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($r['operador_nome']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($r['data_hora_registro'])) ?></td>
                            <td><?= htmlspecialchars($r['status_sumario']) ?></td>

                            <td>
                                <!-- Botão limpo sem complicar no HTML -->
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick='abrirModal(<?= htmlspecialchars(json_encode($r), ENT_QUOTES, "UTF-8") ?>)'>
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php endif; ?>

                </tbody>
            </table>

        </div>
    </div>

</div>

<!-- JANELA MODAL DO BOOTSTRAP -->
<div class="modal fade" id="modalRelatorio" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detalhes do relatório</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="conteudoModal"></div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Função simples para exibir as informações no Modal sem dar erro de Javascript
function abrirModal(dados) {
    // Função auxiliar simples para tratar texto vazio
    function texto(valor) {
        return valor ? valor : '-';
    }

    let html = `
        <p><strong>Protocolo:</strong> ${texto(dados.protocolo)}</p>
        <p><strong>Tipo:</strong> ${texto(dados.tipo_relatorio)}</p>
        <p><strong>Linha:</strong> ${texto(dados.codigo_linha)} - ${texto(dados.nome_linha)}</p>
        <p><strong>Trem:</strong> ${texto(dados.prefixo)}</p>
        <p><strong>Modelo:</strong> ${texto(dados.modelo_composicao)}</p>
        <p><strong>Operador:</strong> ${texto(dados.operador_nome)}</p>
        <p><strong>Status:</strong> ${texto(dados.status_sumario)}</p>
        <p><strong>Parecer técnico:</strong> ${texto(dados.parecer_tecnico)}</p>
        <p><strong>Assinatura:</strong> ${texto(dados.assinatura_digital_sha256)}</p>
    `;

    document.getElementById('conteudoModal').innerHTML = html;

    let modal = new bootstrap.Modal(document.getElementById('modalRelatorio'));
    modal.show();
}
</script>

</body>
</html>