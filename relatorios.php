<?php

require'proteger.php';

require 'config.php';
$tipo = $_GET['tipo'] ?? 'todos';
$linha = $_GET['linha'] ?? 'todas';
$inicio = $_GET ['data_inicio'] ?? '';
$fim = $_GET['data_fim'] ??'';

$linhas = $conexao-> query("
SELECT id, codigo_linha, nome_linha
FROM linhas
WHERE ativo = TRUE
ORDER BY codigo_linha
");

$whereKpi = [];
$paramsKpi = [];
$tiposKpi = '';

if($inicio) {
  $whereKpi[] = 'data_referencia >= ?';
  $paramsKpi[] = $inicio;
  $tiposKpi = 's';
}
if($fim){
  $whereKpi[] ='data_referencia <= ?';
  $paramsKpi[] = $fim;
  $tiposKpi ='s';
}

$sql = "
SELECT pontualidade_global_percentual, passageiros_transportados, consumo_energetico_kwh_km0"


?>










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

    <!-- FILTROS -->
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
            <button class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Filtrar
            </button>
        </div>

    </form>

    <!-- KPIs -->
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

    <!-- FLUXO -->
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

    <!-- RELATÓRIOS -->
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
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($relatorios->num_rows == 0): ?>

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
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick='abrirModal(<?= json_encode($r) ?>)'>
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

<!-- MODAL -->
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
function abrirModal(r) {
    document.getElementById('conteudoModal').innerHTML = `
        <p><strong>Protocolo:</strong> ${r.protocolo ?? '-'}</p>
        <p><strong>Tipo:</strong> ${r.tipo_relatorio ?? '-'}</p>
        <p><strong>Linha:</strong> ${r.codigo_linha ?? '-'} - ${r.nome_linha ?? '-'}</p>
        <p><strong>Trem:</strong> ${r.prefixo ?? '-'}</p>
        <p><strong>Modelo:</strong> ${r.modelo_composicao ?? '-'}</p>
        <p><strong>Operador:</strong> ${r.operador_nome ?? '-'}</p>
        <p><strong>Status:</strong> ${r.status_sumario ?? '-'}</p>
        <p><strong>Parecer técnico:</strong> ${r.parecer_tecnico ?? '-'}</p>
        <p><strong>Assinatura:</strong> ${r.assinatura_digital_sha256 ?? '-'}</p>
    `;

    new bootstrap.Modal(document.getElementById('modalRelatorio')).show();
}
</script>

</body>
</html>

