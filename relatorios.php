<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require 'config.php';

$tipo = $_GET['tipo'] ?? 'todos';
$linha = $_GET['linha'] ?? 'todas';
$inicio = $_GET['data_inicio'] ?? '';
$fim = $_GET['data_fim'] ?? '';

function consulta($db, $sql, $types = '', $params = []) {
    $stmt = $db->prepare($sql);
    if (!$stmt) die('Erro na consulta: ' . $db->error);
    if ($types) {
        $refs = [$types];
        foreach ($params as $i => $v) $refs[] = &$params[$i];
        call_user_func_array([$stmt, 'bind_param'], $refs);
    }
    $stmt->execute();
    return $stmt->get_result();
}

/* LINHAS */
$linhas = $conexao->query("
    SELECT id, codigo_linha, nome_linha
    FROM linhas
    WHERE ativo = TRUE
    ORDER BY codigo_linha
");

/* FILTRO DE DATA */
$whereData = [];
$paramsData = [];
$typesData = '';

if ($inicio) {
    $whereData[] = 'data_referencia >= ?';
    $paramsData[] = $inicio;
    $typesData .= 's';
}
if ($fim) {
    $whereData[] = 'data_referencia <= ?';
    $paramsData[] = $fim;
    $typesData .= 's';
}

$filtroData = $whereData ? ' WHERE ' . implode(' AND ', $whereData) : '';

/* KPIs */
$sql = "
    SELECT
        COALESCE(AVG(pontualidade_global_percentual),0) AS pontualidade,
        COALESCE(SUM(passageiros_transportados),0) AS passageiros,
        COALESCE(AVG(consumo_energetico_kwh_km),0) AS consumo,
        COALESCE(AVG(confiabilidade_operacional_percentual),0) AS confiabilidade
    FROM kpis_malha_historico
    $filtroData
";

$kpi = consulta($conexao, $sql, $typesData, $paramsData)->fetch_assoc();

/* GRÁFICO */
$grafico = consulta($conexao, "
    SELECT data_referencia,
           pontualidade_global_percentual,
           confiabilidade_operacional_percentual,
           lotacao_media_percentual
    FROM kpis_malha_historico
    $filtroData
    ORDER BY data_referencia
", $typesData, $paramsData);

$labels = $pontualidade = $confiabilidade = $lotacao = [];

while ($g = $grafico->fetch_assoc()) {
    $labels[] = date('d/m', strtotime($g['data_referencia']));
    $pontualidade[] = (float)$g['pontualidade_global_percentual'];
    $confiabilidade[] = (float)$g['confiabilidade_operacional_percentual'];
    $lotacao[] = (float)$g['lotacao_media_percentual'];
}

/* FLUXO POR LINHA */
$whereFluxo = [];
$paramsFluxo = [];
$typesFluxo = '';

if ($linha !== 'todas') {
    $whereFluxo[] = 'l.id = ?';
    $paramsFluxo[] = (int)$linha;
    $typesFluxo .= 'i';
}
if ($inicio) {
    $whereFluxo[] = 'tel.data_hora >= ?';
    $paramsFluxo[] = $inicio . ' 00:00:00';
    $typesFluxo .= 's';
}
if ($fim) {
    $whereFluxo[] = 'tel.data_hora <= ?';
    $paramsFluxo[] = $fim . ' 23:59:59';
    $typesFluxo .= 's';
}

$filtroFluxo = $whereFluxo ? ' WHERE ' . implode(' AND ', $whereFluxo) : '';

$fluxo = consulta($conexao, "
    SELECT l.codigo_linha, l.nome_linha,
           COALESCE(AVG(tel.ocupacao_percentual),0) AS ocupacao
    FROM linhas l
    JOIN trens t ON t.linha_id = l.id
    JOIN telemetria_trens tel ON tel.trem_id = t.id
    $filtroFluxo
    GROUP BY l.id, l.codigo_linha, l.nome_linha
    ORDER BY l.codigo_linha
", $typesFluxo, $paramsFluxo);

$fluxos = [];
$totalFluxo = 0;

while ($f = $fluxo->fetch_assoc()) {
    $f['ocupacao'] = max(0, (float)$f['ocupacao']);
    $totalFluxo += $f['ocupacao'];
    $fluxos[] = $f;
}

$acumulado = 0;
$ultimo = count($fluxos) - 1;

foreach ($fluxos as $i => &$f) {
    $p = $totalFluxo > 0 ? ($f['ocupacao'] / $totalFluxo) * 100 : 0;
    if ($i === $ultimo && $totalFluxo > 0) $p = 100 - $acumulado;
    $f['percentual'] = max(0, $p);
    $acumulado += $f['percentual'];
}
unset($f);

/* SENSORES */
$sensores = $conexao->query("
    SELECT s.codigo_identificador, s.nome_modelo, s.status_sensor,
           s.unidade_medida, s.localizacao_detalhe,
           ls.valor_lido, ls.mensagem_status, ls.data_hora
    FROM sensores s
    LEFT JOIN leituras_sensores ls ON ls.id = (
        SELECT l.id
        FROM leituras_sensores l
        WHERE l.sensor_id = s.id
        ORDER BY l.data_hora DESC
        LIMIT 1
    )
    ORDER BY s.id
");

/* RELATÓRIOS */
$whereRel = [];
$paramsRel = [];
$typesRel = '';

if ($tipo !== 'todos') {
    $whereRel[] = 'r.tipo_relatorio = ?';
    $paramsRel[] = $tipo;
    $typesRel .= 's';
}
if ($linha !== 'todas') {
    $whereRel[] = 'r.linha_id = ?';
    $paramsRel[] = (int)$linha;
    $typesRel .= 'i';
}
if ($inicio) {
    $whereRel[] = 'r.data_hora_registro >= ?';
    $paramsRel[] = $inicio . ' 00:00:00';
    $typesRel .= 's';
}
if ($fim) {
    $whereRel[] = 'r.data_hora_registro <= ?';
    $paramsRel[] = $fim . ' 23:59:59';
    $typesRel .= 's';
}

$filtroRel = $whereRel ? ' WHERE ' . implode(' AND ', $whereRel) : '';

$relatorios = consulta($conexao, "
    SELECT r.protocolo, r.tipo_relatorio, r.data_hora_registro,
           r.status_sumario, l.codigo_linha, t.prefixo,
           o.nome_completo AS operador
    FROM relatorios_operacionais r
    LEFT JOIN linhas l ON l.id = r.linha_id
    LEFT JOIN trens t ON t.id = r.trem_id
    LEFT JOIN operadores o ON o.id = r.operador_id
    $filtroRel
    ORDER BY r.data_hora_registro DESC
", $typesRel, $paramsRel);

$totalRelatorios = $relatorios->num_rows;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Relatórios - SuperTrain</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="estilo/relatorios.css?v=4">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<nav class="navbar navbar-expand-lg bg-blur">
    <div class="container-fluid">

        <a class="navbar-brand" href="dashboard.php">
            <img src="imagens/logo.png" alt="SuperTrain" width="36" height="28">
            SuperTrain
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSuperTrain">
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
                    <a class="nav-link active" href="relatorios.php">
                        <i class="bi bi-file-earmark-bar-graph"></i> Relatórios
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="perfil.php">
                        <i class="bi bi-person-circle"></i> Perfil
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="sair.php">
                        <i class="bi bi-box-arrow-right"></i> Sair
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<main class="relatorios-container">

<h1 class="page-title">Relatórios Operacionais</h1>

<!-- FILTROS -->
<form method="GET" class="card filter-card">
    <div class="row g-3 align-items-end">

        <div class="col-lg-3 col-md-6">
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

        <div class="col-lg-3 col-md-6">
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

        <div class="col-lg-2">
            <button class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Filtrar
            </button>
        </div>

    </div>
</form>

<!-- KPIs -->
<section class="row g-3 mb-4">

    <div class="col-lg-3 col-md-6">
        <div class="kpi-card">
            <span class="kpi-label">Pontualidade</span>
            <strong><?= number_format($kpi['pontualidade'], 1, ',', '.') ?>%</strong>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="kpi-card">
            <span class="kpi-label">Passageiros</span>
            <strong><?= number_format($kpi['passageiros'], 0, ',', '.') ?></strong>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="kpi-card">
            <span class="kpi-label">Consumo(kWh/km)</span>
            <strong><?= number_format($kpi['consumo'], 2, ',', '.') ?></strong>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="kpi-card">
            <span class="kpi-label">Confiabilidade</span>
            <strong><?= number_format($kpi['confiabilidade'], 1, ',', '.') ?>%</strong>
        </div>
    </div>

</section>

<!-- GRÁFICO -->
<section class="card visual-section-card">

    <div class="section-title">
        <h2>Evolução dos indicadores</h2>
        <span>Valores históricos</span>
    </div>

    <div class="chart-box">
        <canvas id="graficoRelatorios"></canvas>
    </div>

</section>

<!-- FLUXO -->
<section class="card visual-section-card">

    <div class="section-title">
        <h2>Fluxo por linha</h2>
        <span>Total: <?= $totalFluxo > 0 ? '100%' : '0%' ?></span>
    </div>

    <?php if (!$fluxos): ?>

        <p class="empty-message">Nenhum dado de fluxo encontrado.</p>

    <?php else: ?>

        <?php foreach ($fluxos as $f): ?>

            <div class="fluxo-bar-item">

                <div class="fluxo-bar-label">
                    <span>
                        <?= htmlspecialchars($f['codigo_linha'] . ' - ' . $f['nome_linha']) ?>
                    </span>
                    <strong><?= number_format($f['percentual'], 1, ',', '.') ?>%</strong>
                </div>

                <div class="progress">
                    <div class="progress-bar" style="width:<?= $f['percentual'] ?>%"></div>
                </div>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</section>

<!-- SENSORES -->
<section class="card visual-section-card">

    <div class="section-title">
        <h2>Situação dos sensores</h2>
        <span><?= $sensores->num_rows ?> sensores</span>
    </div>

    <div class="row g-3">

        <?php while ($s = $sensores->fetch_assoc()): ?>

            <?php
            $status = [
                'operacional' => 'Operacional',
                'alerta' => 'Alerta',
                'em_calibracao' => 'Em calibração',
                'inativo' => 'Inativo'
            ][$s['status_sensor']] ?? $s['status_sensor'];
            ?>

            <div class="col-xl-4 col-md-6">

                <div class="sensor-card">

                    <div class="sensor-top">

                        <div>
                            <strong><?= htmlspecialchars($s['codigo_identificador']) ?></strong>
                            <small><?= htmlspecialchars($s['nome_modelo']) ?></small>
                        </div>

                        <span class="sensor-status <?= htmlspecialchars($s['status_sensor']) ?>">
                            <i class="bi bi-circle-fill"></i>
                            <?= $status ?>
                        </span>

                    </div>

                    <p><?= htmlspecialchars($s['localizacao_detalhe']) ?></p>

                    <div class="sensor-reading">
                        <span>Última leitura</span>
                        <strong>
                            <?php if ($s['valor_lido'] !== null): ?>
                                <?= number_format($s['valor_lido'], 2, ',', '.') ?>
                                <?= htmlspecialchars($s['unidade_medida']) ?>
                            <?php else: ?>
                                Sem leitura
                            <?php endif; ?>
                        </strong>
                    </div>

                    <small class="sensor-message">
                        <?= htmlspecialchars($s['mensagem_status'] ?? 'Sem informação') ?>
                    </small>

                </div>

            </div>

        <?php endwhile; ?>

    </div>

</section>

<!-- TABELA -->
<section class="card table-card">

    <div class="table-header-custom">

        <h2 class="table-title">Relatórios</h2>

        <div class="table-actions">

            <span class="badge bg-primary">
                <?= $totalRelatorios ?> registros
            </span>

            <button class="btn-export" onclick="exportarCSV()">
                <i class="bi bi-filetype-csv"></i> Exportar
            </button>

            <button class="btn-export" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimir
            </button>

        </div>

    </div>

    <div class="table-responsive">

        <table class="table table-hover align-middle" id="tabelaRelatorios">

            <thead>
                <tr>
                    <th>Protocolo</th>
                    <th>Tipo</th>
                    <th>Linha</th>
                    <th>Trem</th>
                    <th>Operador</th>
                    <th>Data</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!$totalRelatorios): ?>

                    <tr>
                        <td colspan="7" class="text-center">
                            Nenhum relatório encontrado.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php while ($r = $relatorios->fetch_assoc()): ?>

                        <tr>

                            <td><?= htmlspecialchars($r['protocolo']) ?></td>

                            <td><?= htmlspecialchars($r['tipo_relatorio']) ?></td>

                            <td><?= htmlspecialchars($r['codigo_linha'] ?? '-') ?></td>

                            <td><?= htmlspecialchars($r['prefixo'] ?? '-') ?></td>

                            <td><?= htmlspecialchars($r['operador'] ?? '-') ?></td>

                            <td>
                                <?= date('d/m/Y H:i', strtotime($r['data_hora_registro'])) ?>
                            </td>

                            <td><?= htmlspecialchars($r['status_sumario']) ?></td>

                        </tr>

                    <?php endwhile; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>

</main>

<script>
const labels = <?= json_encode($labels) ?>;
const pontualidade = <?= json_encode($pontualidade) ?>;
const confiabilidade = <?= json_encode($confiabilidade) ?>;
const lotacao = <?= json_encode($lotacao) ?>;

new Chart(document.getElementById('graficoRelatorios'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Pontualidade',
                data: pontualidade,
                tension: .35,
                fill: false
            },
            {
                label: 'Confiabilidade',
                data: confiabilidade,
                tension: .35,
                fill: false
            },
            {
                label: 'Lotação',
                data: lotacao,
                tension: .35,
                fill: false
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false
        },
        scales: {
            y: {
                min: 0,
                max: 100,
                ticks: {
                    callback: value => value + '%'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

function exportarCSV() {
    const tabela = document.getElementById('tabelaRelatorios');

    const linhas = [...tabela.rows].map(linha =>
        [...linha.cells].map(celula =>
            '"' + celula.innerText.replace(/"/g, '""') + '"'
        ).join(';')
    );

    const arquivo = new Blob(
        [linhas.join('\n')],
        {type: 'text/csv;charset=utf-8;'}
    );

    const url = URL.createObjectURL(arquivo);
    const link = document.createElement('a');

    link.href = url;
    link.download = 'relatorios-supertrain.csv';
    link.click();

    URL.revokeObjectURL(url);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>