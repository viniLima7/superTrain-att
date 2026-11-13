<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once 'config.php';

// Se já estiver logado, redireciona ao dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$erro = '';
$sucesso = '';
$tokenValido = false;
$emailConta = '';
$senhaAlterada = false;

// 1. Validação do formato do token
if ($token === '') {
    $erro = 'Token de redefinição não informado.';
} elseif (!ctype_xdigit($token) || strlen($token) !== 32) {
    $erro = 'Formato do token de redefinição é inválido.';
} else {
    // 2. Validação do token e expiração na tabela recuperacao_senha
    $stmt = $conexao->prepare('SELECT id, email, data_expiracao FROM recuperacao_senha WHERE token = ? LIMIT 1');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $recuperacao = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$recuperacao) {
        $erro = 'Este link de recuperação é inválido ou já foi utilizado.';
    } else {
        $agora = time();
        $expiracao = strtotime($recuperacao['data_expiracao']);

        // Verifica se o token expirou (limite de 30 minutos)
        if ($agora > $expiracao) {
            // Remove o token expirado do banco de dados
            $stmtDel = $conexao->prepare('DELETE FROM recuperacao_senha WHERE token = ?');
            $stmtDel->bind_param('s', $token);
            $stmtDel->execute();
            $stmtDel->close();

            $erro = 'Este link de recuperação expirou (validade de 30 minutos). Por favor, solicite um novo link.';
        } else {
            $tokenValido = true;
            $emailConta = $recuperacao['email'];
        }
    }
}

// 3. Processamento do formulário de nova senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValido) {
    $novaSenha = $_POST['novaSenha'] ?? '';
    $confirmarSenha = $_POST['confirmarSenha'] ?? '';

    if ($novaSenha === '') {
        $erro = 'Por favor, informe a nova senha.';
    } elseif (mb_strlen($novaSenha) < 8) {
        $erro = 'A nova senha deve ter no mínimo 8 caracteres.';
    } elseif ($novaSenha !== $confirmarSenha) {
        $erro = 'As senhas informadas não coincidem.';
    } else {
        // Criptografa a nova senha com password_hash (padrão de autenticação atual do sistema)
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        // Atualiza a senha e deleta o token de forma transacional e atômica
        $conexao->begin_transaction();
        try {
            // Atualiza a senha na tabela usuarios
            $stmtUp = $conexao->prepare('UPDATE usuarios SET senha_hash = ? WHERE email = ?');
            $stmtUp->bind_param('ss', $senhaHash, $emailConta);
            $stmtUp->execute();
            $stmtUp->close();

            // Deleta o token utilizado para invalidar reusos
            $stmtDel = $conexao->prepare('DELETE FROM recuperacao_senha WHERE token = ?');
            $stmtDel->bind_param('s', $token);
            $stmtDel->execute();
            $stmtDel->close();

            $conexao->commit();

            $senhaAlterada = true;
            $tokenValido = false; // Token consumido
            $sucesso = 'Sua senha foi redefinida com sucesso! Você já pode entrar com suas novas credenciais.';
        } catch (Exception $e) {
            $conexao->rollback();
            $erro = 'Não foi possível atualizar a senha. Por favor, tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Redefinir Senha - SuperTrain</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="estilo/recuperacao.css">
</head>

<body>

  <header>
    <nav class="navbar bg-blur">
      <div class="container-fluid">
        <a class="navbar-brand" href="login.php">
          <img src="imagens/logo.png" alt="SuperTrain Logo" width="36" height="28" class="d-inline-block">
          SuperTrain
        </a>
      </div>
    </nav>
  </header>

  <main>
    <div class="container recuperacao-wrapper">
      <h2 class="texto-indicativo">
        Atualização segura de credenciais de acesso.
      </h2>

      <div class="card recuperacao-card">

        <?php if ($senhaAlterada): ?>
          <!-- TELA SUCESSO APÓS REDEFINIÇÃO -->
          <h1 class="card-title">Senha Redefinida!</h1>
          <p class="texto-info">
            Sua conta já está atualizada com a nova senha de segurança.
          </p>

          <div class="aviso aviso-sucesso" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <div><?= htmlspecialchars($sucesso) ?></div>
          </div>

          <a href="login.php" class="btn w-100 btn-recuperar">
            <i class="bi bi-box-arrow-in-right"></i> Ir para o Login
          </a>

        <?php elseif ($tokenValido): ?>
          <!-- TELA B: FORMULÁRIO DE NOVA SENHA (TOKEN VÁLIDO) -->
          <h1 class="card-title">Nova Senha</h1>
          <p class="texto-info">
            Defina uma nova senha para a conta <strong class="text-white"><?= htmlspecialchars($emailConta) ?></strong>.
          </p>

          <?php if ($erro !== ''): ?>
            <div class="aviso aviso-erro" role="alert">
              <i class="bi bi-exclamation-triangle-fill"></i>
              <div><?= htmlspecialchars($erro) ?></div>
            </div>
          <?php endif; ?>

          <form method="post" action="redefinir_senha.php?token=<?= htmlspecialchars($token) ?>" id="formRedefinir" autocomplete="off">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="form-group mb-3">
              <label for="novaSenha" class="form-label">Nova Senha</label>
              <div class="input-password-wrapper">
                <input type="password" class="form-control" id="novaSenha" name="novaSenha" placeholder="Mínimo de 8 caracteres" required minlength="8" autocomplete="new-password">
                <button type="button" class="btn-toggle-senha" data-target="novaSenha" aria-label="Mostrar ou ocultar senha">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              <div class="senha-dica">Mínimo de 8 caracteres.</div>
            </div>

            <div class="form-group mb-4">
              <label for="confirmarSenha" class="form-label">Confirmar Nova Senha</label>
              <div class="input-password-wrapper">
                <input type="password" class="form-control" id="confirmarSenha" name="confirmarSenha" placeholder="Repita a nova senha" required minlength="8" autocomplete="new-password">
                <button type="button" class="btn-toggle-senha" data-target="confirmarSenha" aria-label="Mostrar ou ocultar senha">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              <div id="senhaFeedback" class="senha-feedback"></div>
            </div>

            <button type="submit" class="btn w-100 btn-recuperar" id="btnSalvarSenha">
              <i class="bi bi-shield-check"></i> Redefinir Senha
            </button>

            <div class="auth-links">
              <a href="login.php" class="voltar-login">
                <i class="bi bi-arrow-left"></i> Cancelar e voltar ao Login
              </a>
            </div>
          </form>

        <?php else: ?>
          <!-- TOKEN INVÁLIDO OU EXPIRADO -->
          <h1 class="card-title">Link Indisponível</h1>
          <p class="texto-info">
            Não foi possível prosseguir com a redefinição de senha.
          </p>

          <div class="aviso aviso-erro" role="alert">
            <i class="bi bi-exclamation-octagon-fill"></i>
            <div><?= htmlspecialchars($erro) ?></div>
          </div>

          <a href="recuperacao.php" class="btn w-100 btn-recuperar mb-3">
            <i class="bi bi-arrow-clockwise"></i> Solicitar Novo Link
          </a>

          <div class="auth-links">
            <a href="login.php" class="voltar-login">
              <i class="bi bi-arrow-left"></i> Voltar para o Login
            </a>
          </div>

        <?php endif; ?>

      </div>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Alternância de visualização da senha
      document.querySelectorAll('.btn-toggle-senha').forEach(function (button) {
        button.addEventListener('click', function () {
          var targetId = this.getAttribute('data-target');
          var input = document.getElementById(targetId);
          var icon = this.querySelector('i');
          if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
          } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
          }
        });
      });

      // Validação em tempo real e antes do envio
      var form = document.getElementById('formRedefinir');
      var novaSenha = document.getElementById('novaSenha');
      var confirmarSenha = document.getElementById('confirmarSenha');
      var feedback = document.getElementById('senhaFeedback');

      function validarSenhas() {
        if (!novaSenha || !confirmarSenha || !feedback) return true;
        var p1 = novaSenha.value;
        var p2 = confirmarSenha.value;

        if (p2.length === 0) {
          feedback.textContent = '';
          feedback.className = 'senha-feedback';
          return false;
        }

        if (p1 !== p2) {
          feedback.innerHTML = '<i class="bi bi-x-circle me-1"></i> As senhas não coincidem.';
          feedback.className = 'senha-feedback invalido';
          return false;
        } else {
          feedback.innerHTML = '<i class="bi bi-check-circle me-1"></i> As senhas coincidem.';
          feedback.className = 'senha-feedback valido';
          return true;
        }
      }

      if (novaSenha && confirmarSenha) {
        novaSenha.addEventListener('input', validarSenhas);
        confirmarSenha.addEventListener('input', validarSenhas);

        if (form) {
          form.addEventListener('submit', function (e) {
            if (novaSenha.value.length < 8) {
              e.preventDefault();
              feedback.innerHTML = '<i class="bi bi-x-circle me-1"></i> A senha deve ter no mínimo 8 caracteres.';
              feedback.className = 'senha-feedback invalido';
              novaSenha.focus();
              return false;
            }

            if (novaSenha.value !== confirmarSenha.value) {
              e.preventDefault();
              feedback.innerHTML = '<i class="bi bi-x-circle me-1"></i> As senhas não coincidem.';
              feedback.className = 'senha-feedback invalido';
              confirmarSenha.focus();
              return false;
            }
          });
        }
      }
    });
  </script>
</body>

</html>
