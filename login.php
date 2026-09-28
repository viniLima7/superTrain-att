<?php 
 if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
 }

 require 'config.php';

  if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
  }

  $erro = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['emailUsuario']);
    $senha = $_POST['senha'];

    if($email === '' || $senha === '') {
      $erro = 'Preencha o login e a senha.';
    } else {
      $stmt = $conexao->prepare('SELECT id, email, senha_hash FROM usuarios WHERE email = ? LIMIT 1');
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $usuario = $stmt->get_result()->fetch_assoc();
      $stmt->close();

      if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int) $usuario['id'];
        $_SESSION['usuario_email'] = $usuario['email'];

        header('Location: dashboard.php');
        exit;
      }

      $erro = 'Login ou senha inválidas.';
    }
  }
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - SuperTrain</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- CSS externo -->
  <link rel="stylesheet" href="estilo/login.css">
</head>

<body>
  <header>
    <nav class="navbar bg-blur">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">
          <img src="imagens/logo.png" alt="SuperTrain Logo" width="36" height="28" class="d-inline-block align-text-top">
          SuperTrain
        </a>
      </div>
    </nav>
  </header>

  <main>
    <div class="container login-wrapper">
      <div class="welcome-header">
        <h1 class="welcome-title">
          Bem-vindo ao
          <span class="supertrain-title">SUPERTRAIN</span>
        </h1>
      </div>

      <div class="card login-card">
        <h2 class="login-title">Acessar Conta</h2>

        <?php if ($erro !== ''): ?>
          <p class="aviso avios-erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="post" autocomplete="off">
          <div class="form-group mb-3">
            <label for="usuario" class="form-label">E-mail</label>
            <input type="text" id="emailUsuario" name="emailUsuario" class="form-control" placeholder="Seu E-mail de usuário" maxlength="150" required>
          </div>

          <div class="form-group mb-4">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" id="senha" name="senha" class="form-control" placeholder="Sua senha secreta" required autocomplete="current-password" required>
          </div>

          <button type="submit" class="btn w-100 btn-login">
            <i class="bi bi-box-arrow-in-right"></i> Entrar
          </button>

          <div class="auth-links">
            <p>Não possui uma conta?</p>
            <div>
              <a href="cadastro.php" class="auth-link">Cadastre-se</a>
              <span class="auth-divider">•</span>
              <a href="recuperacao.php" class="auth-link">Esqueceu a senha?</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </main>
</body>

</html>