<?php
  require "config.php";

  $email = '';
  $erros = [];
  $sucesso = '';
  $nomePadrao = 'vazio';
  $paisPadrao = '1';
  $statusPadrao = 'ativo';

  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['login'] ?? '');
    $pais = $_POST['paisUsuario'] ?? '';
    $senha = $_POST['senhaUsuario'] ?? '';
    $confirmacaoSenha = $_POST['confirmacaoSenhaUsuario'];

    if($email === '') {
      $erros[] = 'Informe o email.';
    }elseif (mb_strlen($email) > 150){
      $erros[] = 'O login deve ter no máximo 150 caracteres.';
    }

    if($pais === '') {
      $erros[] = 'Informe o seu país.';
    }

    if($senha === ''){
      $erros[] = 'Informe sua senha.';
    }elseif (mb_strlen($senha) < 8) {
      $erros[] = 'A senha deve conter no mínimo 8 caracteres.';
    }

    
    if($confirmacaoSenha === ''){
      $erros[] = 'Confirme sua senha.';
    }elseif (mb_strlen($senha) < 8) {
      $erros[] = 'A senha deve conter no mínimo 8 caracteres.';
    }elseif ($senha !== $confirmacaoSenha) {
      $erros[] = 'As senhas não coincidem.';
    }

    if(count($erros) === 0) {
      $stmt = $conexao->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $existe = $stmt->get_result()->fetch_assoc();
      $stmt -> close();

      if($existe){
        $erros[] = 'Este email já possui cadastro.';
      } else {
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $conexao->prepare('INSERT INTO usuarios (`email`, `nome_usuario`, `senha_hash`, `pais_id`, `status_conta`) VALUES (?,?,?,?,?)');
        $stmt-> bind_param('sssis', $email, $nomePadrao, $hash, $paisPadrao, $statusPadrao);

        if($stmt->execute()){
          $sucesso = 'Usúario cadastrado com sucesso!';
          $email = '';
        } else {
          $erros[] = 'Não foi possível cadastrar o usuário.';
        }

        $stmt -> close();
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cadastre-se - SuperTrain</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="estilo/cadastro.css" />
</head>

<body>
  <header>
    <!-- NAV -->
    <nav class="navbar bg-blur">
      <div class="container-fluid">
        <a class="navbar-brand" href="login.html">
          <img src="imagens/logo.png" alt="SuperTrain Logo" width="36" height="28" class="d-inline-block" />
          SuperTrain
        </a>
      </div>
    </nav>
  </header>

  <main>
    <div class="container cadastro-wrapper">
      <h2 class="texto-indicativo">Preencha os dados e crie sua conta na SuperTrain!</h2>

      <div class="card cadastro-card">
        <h1 class="card-title">Criar Conta</h1>

        <?php if (count($erros) > 0): ?>
          <div class="aviso aviso-erro">
            <ul>
              <?php foreach ($erros as $erro): ?>
                <li><?= htmlspecialchars($erro) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

          <?php if ($sucesso !== ''): ?>
            <p class="aviso"><?= htmlspecialchars($sucesso) ?></p>
          <?php endif; ?>

          <!-- FORMS -->
        <form method="post" autocomplete="off">

          <!-- E-MAIL -->
          <div class="form-group mb-3">
            <label for="emailUsuario" class="form-label">E-mail Corporativo</label>
            <input type="email" class="form-control" id="emailUsuario" maxlength="150" name="emailUsuario" placeholder="exemplo@supertrain.com" require value="<?= htmlspecialchars($email) ?>"/>
          </div>

          <!-- SELECT PAÍS -->
          <div class="form-group mb-3">
            <label for="selectPaisUsuario" class="form-label">País / Região</label>
            <select id="selectPaisUsuario" name="paisUsuario" class="form-select">
              <option selected value="brasil">Brasil</option>
              <option value="estadosUnidos">Estados Unidos</option>
              <option value="russia">Rússia</option>
              <option value="camboja">Camboja</option>
              <option value="japao">Japão</option>
              <option value="alemanha">Alemanha</option>
            </select>
          </div>

          <!-- SENHA USUÁRIO -->
          <div class="form-group mb-3">
            <label for="senhaUsuario" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senhaUsuario" name="senhaUsuario" placeholder="••••••••••••" required minlength="8" />
          </div>

          <!-- CONFIRMAÇÃO SENHA -->
          <div class="form-group mb-4">
            <label for="confirmacaoSenhaUsuario" class="form-label">Confirme sua senha</label>
            <input type="password" class="form-control" id="confirmacaoSenhaUsuario" name="confirmacaoSenhaUsuario" placeholder="••••••••••••" minlength="8" />
          </div>

          <button type="submit" class="btn w-100 btn-cadastro">
            <i class="bi bi-person-plus"></i> Cadastrar
          </button>

          <div class="auth-links">
            <p>Já possui uma conta ativa?</p>
            <a href="login.php" class="auth-link">Voltar para o Login</a>
          </div>
        </form>

      </div>
    </div>
  </main>
</body>

</html>
