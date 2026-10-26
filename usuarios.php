<?php

require_once 'proteger.php';
require_once 'config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// ======================================================
// EXCLUIR USUÁRIO
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {

    $id_excluir = (int) $_POST['excluir_id'];

    if ($id_excluir > 0) {

        $stmt = $conexao->prepare(
            'DELETE FROM usuarios WHERE id = ?'
        );

        if (!$stmt) {
            die('Erro ao preparar a exclusão: ' . $conexao->error);
        }

        $stmt->bind_param('i', $id_excluir);

        if ($stmt->execute()) {
            $_SESSION['mensagem'] = 'Usuário excluído com sucesso!';
        } else {
            $_SESSION['mensagem'] = 'Não foi possível excluir o usuário.';
        }

        $stmt->close();
    }

    header('Location: usuarios.php');
    exit;
}

// ======================================================
// CONSULTAR USUÁRIOS
// ======================================================

$sql = '
    SELECT
        id,
        nome_usuario AS nome,
        cargo,
        email
    FROM usuarios
    ORDER BY id DESC
';

$resultado = $conexao->query($sql);

if (!$resultado) {
    die('Erro ao consultar usuários: ' . $conexao->error);
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Usuários - SuperTrain</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <!-- CSS da página -->
    <link rel="stylesheet" href="estilo/usuario.css">

    <style>
        .user-row {
            cursor: pointer;
        }

        .user-row:focus-visible {
            outline: 2px solid #0d6efd;
            outline-offset: -2px;
        }
    </style>

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
                        alt="SuperTrain Logo"
                        width="36"
                        height="28"
                    >

                    SuperTrain

                </a>

                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarSuperTrain"
                    aria-controls="navbarSuperTrain"
                    aria-expanded="false"
                    aria-label="Abrir menu"
                >

                    <span class="navbar-toggler-icon"></span>

                </button>

                <div
                    class="collapse navbar-collapse"
                    id="navbarSuperTrain"
                >

                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="bi bi-grid-fill"></i>
                                Dashboard
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                class="nav-link active"
                                aria-current="page"
                                href="usuarios.php"
                            >
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
         CONTEÚDO PRINCIPAL
    =================================================== -->

    <main>

        <div class="container trens-container">

            <!-- Botão para adicionar usuário -->

            <div class="header-actions mb-3">

                <a
                    href="formulario_usuarios.php?id=0"
                    class="btn btn-primary"
                >
                    <i class="bi bi-plus-lg"></i>
                    <span>Adicionar usuário</span>
                </a>

            </div>

            <div class="card card-bg-blur trens-card">

                <!-- Cabeçalho do cartão -->

                <div class="card-header-custom">

                    <h1 class="card-title">
                        Usuários
                    </h1>

                    <p class="card-subtitle">
                        <i class="bi bi-cursor"></i>
                        Clique em um usuário para expandir suas informações.
                    </p>

                </div>

                <div class="card-body">

                    <!-- Mensagem de sucesso ou erro -->

                    <?php if (!empty($_SESSION['mensagem'])): ?>

                        <div
                            class="alert alert-info alert-dismissible fade show"
                            role="alert"
                        >

                            <?= htmlspecialchars(
                                $_SESSION['mensagem'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Fechar"
                            ></button>

                        </div>

                        <?php unset($_SESSION['mensagem']); ?>

                    <?php endif; ?>

                    <!-- Tabela de usuários -->

                    <div class="table-responsive">

                        <table class="table align-middle">

                            <thead>

                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Cargo</th>
                                    <th scope="col">E-mail</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php if ($resultado->num_rows > 0): ?>

                                    <?php while ($linha = $resultado->fetch_assoc()): ?>

                                        <?php

                                        $id_usuario = (int) $linha['id'];

                                        $nome_usuario = htmlspecialchars(
                                            $linha['nome'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );

                                        $cargo_usuario = htmlspecialchars(
                                            $linha['cargo'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );

                                        $email_usuario = htmlspecialchars(
                                            $linha['email'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );

                                        $id_detalhes = 'detalhes-usuario-' . $id_usuario;

                                        ?>

                                        <!-- Linha clicável do usuário -->

                                        <tr
                                            class="user-row"
                                            role="button"
                                            tabindex="0"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#<?= $id_detalhes ?>"
                                            aria-expanded="false"
                                            aria-controls="<?= $id_detalhes ?>"
                                        >

                                            <td>
                                                <?= $id_usuario ?>
                                            </td>

                                            <td>
                                                <i class="bi bi-person-circle me-2"></i>
                                                <?= $nome_usuario ?>
                                            </td>

                                            <td>
                                                <?= $cargo_usuario ?>
                                            </td>

                                            <td>
                                                <?= $email_usuario ?>
                                            </td>

                                        </tr>

                                        <!-- Informações expandidas -->

                                        <tr
                                            class="collapse"
                                            id="<?= $id_detalhes ?>"
                                        >

                                            <td colspan="4">

                                                <div class="p-3">

                                                    <h5 class="mb-3">

                                                        <i class="bi bi-person-vcard"></i>
                                                        Informações do usuário

                                                    </h5>

                                                    <p>
                                                        <strong>ID:</strong>
                                                        <?= $id_usuario ?>
                                                    </p>

                                                    <p>
                                                        <strong>Nome:</strong>
                                                        <?= $nome_usuario ?>
                                                    </p>

                                                    <p>
                                                        <strong>Cargo:</strong>
                                                        <?= $cargo_usuario ?>
                                                    </p>

                                                    <p>
                                                        <strong>E-mail:</strong>
                                                        <?= $email_usuario ?>
                                                    </p>

                                                    <!-- Botões de ação -->

                                                    <div class="d-flex flex-wrap gap-2 mt-3">

                                                        <!-- Editar -->

                                                        <a
                                                            href="formulario_usuarios.php?id=<?= $id_usuario ?>"
                                                            class="btn btn-sm btn-outline-primary"
                                                        >

                                                            <i class="bi bi-pencil"></i>
                                                            Editar

                                                        </a>

                                                        <!-- Excluir -->

                                                        <form
                                                            method="post"
                                                            action="usuarios.php"
                                                            onsubmit="return confirm('Tem certeza de que deseja excluir este usuário?');"
                                                        >

                                                            <input
                                                                type="hidden"
                                                                name="excluir_id"
                                                                value="<?= $id_usuario ?>"
                                                            >

                                                            <button
                                                                type="submit"
                                                                class="btn btn-sm btn-outline-danger"
                                                            >

                                                                <i class="bi bi-trash"></i>
                                                                Excluir

                                                            </button>

                                                        </form>

                                                    </div>

                                                </div>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <!-- Nenhum usuário cadastrado -->

                                    <tr>

                                        <td
                                            colspan="4"
                                            class="text-center py-5"
                                        >

                                            <i class="bi bi-people display-5"></i>

                                            <h5 class="mt-3">
                                                Nenhum usuário cadastrado
                                            </h5>

                                            <p>
                                                Cadastre um usuário para que ele
                                                apareça nesta página.
                                            </p>

                                            <a
                                                href="formulario_usuarios.php?id=0"
                                                class="btn btn-primary"
                                            >

                                                <i class="bi bi-plus-lg"></i>
                                                Cadastrar primeiro usuário

                                            </a>

                                        </td>

                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </main>

    <!-- ==================================================
         JAVASCRIPT
    =================================================== -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Permitir abrir e fechar os detalhes usando Enter ou Espaço.
        document.querySelectorAll('.user-row').forEach(function (linha) {

            linha.addEventListener('keydown', function (evento) {

                if (evento.key === 'Enter' || evento.key === ' ') {

                    evento.preventDefault();

                    const seletor = linha.getAttribute('data-bs-target');
                    const detalhes = document.querySelector(seletor);

                    if (detalhes) {
                        bootstrap.Collapse
                            .getOrCreateInstance(detalhes, {
                                toggle: false
                            })
                            .toggle();
                    }
                }

            });

        });
    </script>

</body>

</html>

<?php

$resultado->free();
$conexao->close();

?>