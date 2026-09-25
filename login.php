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
        <form>
          <div class="form-group mb-3">
            <label for="usuario" class="form-label">Usuário</label>
            <input type="text" id="usuario" name="nomeUsuario" class="form-control" placeholder="Seu nome de usuário" required autocomplete="username">
          </div>

          <div class="form-group mb-4">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" id="senha" name="senha" class="form-control" placeholder="Sua senha secreta" required autocomplete="current-password">
          </div>

          <button type="submit" class="btn w-100 btn-login">
            <i class="bi bi-box-arrow-in-right"></i> Entrar
          </button>

          <div class="auth-links">
            <p>Não possui uma conta?</p>
            <div>
              <a href="cadastro.html" class="auth-link">Cadastre-se</a>
              <span class="auth-divider">•</span>
              <a href="recuperacao.html" class="auth-link">Esqueceu a senha?</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </main>

  <footer></footer>
</body>

</html>