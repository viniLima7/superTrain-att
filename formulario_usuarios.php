<?php

require_once 'proteger.php';
require_once 'config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$id = (int) ($_GET['id'] ?? 0);

$nome = '';
$cargo = '';
$email = '';
$senha = '123456';
$pais_id = 1;
$erros = [];

// ======================================================
// CARREGAR USUÁRIO PARA EDIÇÃO
// ======================================================

if ($id > 0 && $_SERVER['REQUEST_METHOD'] === 'GET') {

    $stmt = $conexao->prepare(
        'SELECT id, nome_usuario AS nome, cargo, email
         FROM usuarios
         WHERE id = ?'
    );

    if (!$stmt) {
        die('Erro ao preparar a consulta: ' . $conexao->error);
    }

    $stmt->bind_param('i', $id);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    $stmt->close();

    if (!$usuario) {
        $_SESSION['mensagem'] = 'Usuário não encontrado.';
        header('Location: usuarios.php');
        exit;
    }

    $nome = $usuario['nome'];
    $cargo = $usuario['cargo'];
    $email = $usuario['email'];
}

// ======================================================
// PROCESSAR FORMULÁRIO
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int) ($_POST['id'] ?? 0);

    $nome = trim($_POST['nome'] ?? '');
    $cargo = trim($_POST['cargo'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validar nome
    if ($nome === '') {
        $erros[] = 'Informe o nome do usuário.';
    }

    // Validar cargo
    if ($cargo === '') {
        $erros[] = 'Informe o cargo do usuário.';
    }

    // Validar e-mail
    if ($email === '') {
        $erros[] = 'Informe o e-mail do usuário.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Informe um endereço de e-mail válido.';
    }

    // ==================================================
    // CADASTRAR OU ATUALIZAR
    // ==================================================

    if (empty($erros)) {

        // ----------------------------------------------
        // ATUALIZAR USUÁRIO EXISTENTE
        // ----------------------------------------------

        if ($id > 0) {

            $stmt = $conexao->prepare(
                'UPDATE usuarios
                 SET nome_usuario = ?, cargo = ?, email = ?
                 WHERE id = ?'
            );

            if (!$stmt) {
                die('Erro ao preparar a atualização: ' . $conexao->error);
            }

            $stmt->bind_param(
                'sssi',
                $nome,
                $cargo,
                $email,
                $id
            );

            $sucesso = $stmt->execute();

            $stmt->close();

            if ($sucesso) {
                $_SESSION['mensagem'] = 'Usuário atualizado com sucesso!';
            } else {
                $_SESSION['mensagem'] = 'Não foi possível atualizar o usuário.';
            }

        } else {

            // ------------------------------------------
            // CADASTRAR NOVO USUÁRIO
            // ------------------------------------------

            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt = $conexao->prepare(
                'INSERT INTO usuarios
                    (nome_usuario, cargo, email, senha_hash, pais_id)
                 VALUES (?, ?, ?, ?, ?)'
            );

            if (!$stmt) {
                die('Erro ao preparar o cadastro: ' . $conexao->error);
            }

            $stmt->bind_param(
                'ssssi',
                $nome,
                $cargo,
                $email,
                $senha_hash,
                $pais_id
            );

            $sucesso = $stmt->execute();

            $stmt->close();

            if ($sucesso) {
                $_SESSION['mensagem'] = 'Usuário cadastrado com sucesso!';
            } else {
                $_SESSION['mensagem'] = 'Não foi possível cadastrar o usuário.';
            }
        }

        // Redirecionar para a página de usuários
        header('Location: usuarios.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= $id > 0 ? 'Editar Usuário' : 'Novo Usuário' ?> - SuperTrain
    </title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <!-- CSS da página -->
    <link rel="stylesheet" href="estilo/formulario.css">

</head>

<body>

    <!-- ==================================================
         CABEÇALHO E NAVEGAÇÃO
    =================================================== -->

    <header>

        <nav class="navbar navbar-expand-lg bg-blur">

            <div class="container-fluid">

                <a class="navbar-brand" href="dashboard.php">

                    <img
                        src="imagens/logo.png"
                        alt="Logo SuperTrain"
                        width="36"
                        height="28">

                    SuperTrain

                </a>

                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarSuperTrain"
                    aria-controls="navbarSuperTrain"
                    aria-expanded="false"
                    aria-label="Abrir menu">

                    <span class="navbar-toggler-icon"></span>

                </button>

                <div
                    class="collapse navbar-collapse"
                    id="navbarSuperTrain">

                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                        <li class="nav-item">

                            <a class="nav-link" href="dashboard.php">
                                <i class="bi bi-grid-fill"></i>
                                Dashboard
                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link active" href="usuarios.php">
                                <i class="bi bi-people-fill"></i>
                                Usuários
                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link" href="trens.php">
                                <i class="bi bi-train-front"></i>
                                Trens
                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link" href="rotas.php">
                                <i class="bi bi-signpost-split"></i>
                                Rotas
                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link" href="sensores.php">
                                <i class="bi bi-broadcast-pin"></i>
                                Sensores
                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link" href="relatorios.php">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                                Relatórios
                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link" href="perfil.php">
                                <i class="bi bi-person-circle"></i>
                                Perfil
                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link text-danger" href="sair.php">
                                <i class="bi bi-box-arrow-right"></i>
                                Sair
                            </a>

                        </li>

                    </ul>

                </div>

            </div>

        </nav>

    </header>

    <!-- ==================================================
         FORMULÁRIO DE USUÁRIOS
    =================================================== -->

    <main>

        <div class="container formulario-wrapper">

            <h2 class="texto-indicativo">

                <?= $id > 0
                    ? 'Edição e Ajuste de Usuários'
                    : 'Cadastre um novo usuário' ?>

            </h2>

            <div class="card formulario-card">

                <h1 class="card-title">

                    <?= $id > 0
                        ? 'Editar Usuário'
                        : 'Novo Usuário' ?>

                </h1>

                <!-- Mensagens de erro -->

                <?php if (!empty($erros)): ?>

                    <div class="aviso aviso-erro" role="alert">

                        <ul class="mb-0">

                            <?php foreach ($erros as $erro): ?>

                                <li>
                                    <?= htmlspecialchars(
                                        $erro,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </li>

                            <?php endforeach; ?>

                        </ul>

                    </div>

                <?php endif; ?>

                <!-- Formulário -->

                <form
                    method="post"
                    action="formulario_usuarios.php<?= $id > 0 ? '?id=' . $id : '' ?>"
                    autocomplete="off">

                    <input
                        type="hidden"
                        name="id"
                        value="<?= $id ?>">

                    <div class="linha row">

                        <!-- Nome -->

                        <div class="campo col-md-6 form-group mb-3">

                            <label for="nome" class="form-label">
                                Nome
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="nome"
                                name="nome"
                                maxlength="20"
                                placeholder="José Roberto"
                                value="<?= htmlspecialchars(
                                    $nome,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required>

                        </div>

                        <!-- Cargo -->

                        <div class="campo col-md-6 form-group mb-3">

                            <label for="cargo" class="form-label">
                                Cargo
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="cargo"
                                name="cargo"
                                maxlength="50"
                                placeholder="Ex.: cliente"
                                value="<?= htmlspecialchars(
                                    $cargo,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required>

                        </div>

                    </div>

                    <!-- E-mail -->

                    <div class="campo form-group mb-3">

                        <label for="email" class="form-label">
                            E-mail
                        </label>

                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            maxlength="100"
                            placeholder="exemplo@dominio.com"
                            value="<?= htmlspecialchars(
                                $email,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            required>

                    </div>

                    <!-- Botões -->

                    <div class="acoes d-flex gap-3 align-items-center mt-3">

                        <button
                            type="submit"
                            class="botao botao-primario flex-grow-1">

                            <i class="bi <?= $id > 0
                                ? 'bi-check2-circle'
                                : 'bi-plus-circle' ?>"></i>

                            <?= $id > 0 ? 'Atualizar' : 'Cadastrar' ?>

                        </button>

                        <a
                            href="usuarios.php"
                            class="botao botao-secundario">

                            Cancelar

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </main>

    <!-- Bootstrap JavaScript -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>