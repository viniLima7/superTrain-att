<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once 'config.php';

// Garante que a tabela recuperacao_senha exista no banco de dados
$conexao->query("CREATE TABLE IF NOT EXISTS `recuperacao_senha` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(150) NOT NULL,
    `token` VARCHAR(64) NOT NULL UNIQUE,
    `data_expiracao` DATETIME NOT NULL,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_recuperacao_token` (`token`),
    INDEX `idx_recuperacao_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Se já estiver logado, redireciona ao dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';
$sucesso = '';
$emailInformado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailInformado = trim($_POST['emailRecuperacao'] ?? '');

    if ($emailInformado === '') {
        $erro = 'Por favor, informe seu e-mail cadastrado.';
    } elseif (!filter_var($emailInformado, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Por favor, informe um endereço de e-mail válido.';
    } elseif (mb_strlen($emailInformado) > 150) {
        $erro = 'O e-mail não pode exceder 150 caracteres.';
    } else {
        // 1. Verifica se o e-mail inserido existe na tabela de usuários via Prepared Statement
        $stmt = $conexao->prepare('SELECT id, nome_usuario FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $emailInformado);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$usuario) {
            $erro = 'E-mail não encontrado em nosso sistema.';
        } else {
            // 2. Gera token seguro via bin2hex(random_bytes(16)) -> 32 caracteres hexadecimais
            $token = bin2hex(random_bytes(16));

            // 3. Define data de expiração para 30 minutos a partir do momento atual
            $expiracao = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            // 4. Limpa solicitações anteriores deste e-mail para evitar tokens pendentes órfãos
            $stmtDel = $conexao->prepare('DELETE FROM recuperacao_senha WHERE email = ?');
            $stmtDel->bind_param('s', $emailInformado);
            $stmtDel->execute();
            $stmtDel->close();

            // 5. Salva novo token e expiração na tabela recuperacao_senha
            $stmtIns = $conexao->prepare('INSERT INTO recuperacao_senha (email, token, data_expiracao) VALUES (?, ?, ?)');
            $stmtIns->bind_param('sss', $emailInformado, $token, $expiracao);

            if ($stmtIns->execute()) {
                $stmtIns->close();

                // Montagem da URL dinâmica de redefinição
                $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
                $protocolo = $isHttps ? 'https://' : 'http://';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $caminhoDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
                $linkRedefinicao = $protocolo . $host . $caminhoDir . '/redefinir_senha.php?token=' . urlencode($token);

                // Configura cabeçalhos do e-mail em formato HTML com charset=UTF-8
                $headers = [
                    'MIME-Version: 1.0',
                    'Content-type: text/html; charset=UTF-8',
                    'From: SuperTrain <nao-responder@supertrain.com>',
                    'Reply-To: suporte@supertrain.com',
                    'X-Mailer: PHP/' . phpversion()
                ];
                $headersStr = implode("\r\n", $headers);

                // Codifica o assunto em Base64 para evitar quebras de caracteres acentuados
                $assunto = 'Recuperação de Senha - SuperTrain';
                $assuntoCodificado = '=?UTF-8?B?' . base64_encode($assunto) . '?=';

                $nomeExibicao = htmlspecialchars($usuario['nome_usuario'] ?? 'Operador');

                // Corpo do e-mail formatado em HTML nativo
                $corpoEmail = <<<HTML
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Recuperação de Senha - SuperTrain</title>
</head>
<body style="margin: 0; padding: 0; background-color: #080c14; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #f8fafc;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #080c14; padding: 40px 15px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" style="max-width: 540px; background-color: #0f172a; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
          <tr>
            <td height="4" style="background: linear-gradient(90deg, #3b82f6, #60a5fa, #1d4ed8);"></td>
          </tr>
          <tr>
            <td style="padding: 32px 32px 16px 32px; text-align: center;">
              <h1 style="margin: 0; font-size: 24px; font-weight: 700; color: #ffffff; letter-spacing: 1.5px;">
                SUPER<span style="color: #60a5fa;">TRAIN</span>
              </h1>
              <p style="margin: 6px 0 0 0; font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.08em;">
                Controle Operacional de Trânsito Ferroviário
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding: 16px 32px 32px 32px;">
              <h2 style="margin: 0 0 16px 0; font-size: 18px; font-weight: 600; color: #ffffff;">
                Olá, {$nomeExibicao}!
              </h2>
              <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.6; color: #cbd5e1;">
                Recebemos uma solicitação de redefinição de senha para a conta associada ao e-mail <strong style="color: #ffffff;">{$emailInformado}</strong>.
              </p>
              <p style="margin: 0 0 26px 0; font-size: 15px; line-height: 1.6; color: #cbd5e1;">
                Para cadastrar sua nova senha de acesso, clique no botão seguro abaixo:
              </p>
              <table role="presentation" cellspacing="0" cellpadding="0" style="margin: 0 auto 26px auto;">
                <tr>
                  <td align="center" style="border-radius: 10px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                    <a href="{$linkRedefinicao}" target="_blank" style="display: inline-block; padding: 14px 28px; font-size: 15px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 10px;">
                      Redefinir Minha Senha
                    </a>
                  </td>
                </tr>
              </table>
              <div style="background-color: rgba(59, 130, 246, 0.1); border-left: 3px solid #3b82f6; border-radius: 6px; padding: 12px 16px; margin-bottom: 24px;">
                <p style="margin: 0; font-size: 13px; color: #93c5fd; line-height: 1.5;">
                  <strong>Atenção:</strong> Por motivos de segurança, este link é válido por <strong>30 minutos</strong> e só pode ser utilizado uma única vez.
                </p>
              </div>
              <p style="margin: 0 0 8px 0; font-size: 13px; color: #94a3b8; line-height: 1.5;">
                Caso não consiga clicar no botão, copie e cole o link diretamente no navegador:
              </p>
              <p style="margin: 0 0 24px 0; font-size: 12px; word-break: break-all; color: #60a5fa; line-height: 1.4; background-color: #080c14; padding: 10px 14px; border-radius: 6px; border: 1px solid rgba(255, 255, 255, 0.08);">
                <a href="{$linkRedefinicao}" target="_blank" style="color: #60a5fa; text-decoration: underline;">{$linkRedefinicao}</a>
              </p>
              <hr style="border: none; border-top: 1px solid rgba(255, 255, 255, 0.08); margin: 24px 0;">
              <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.5;">
                Se você não solicitou essa redefinição, desconsidere esta mensagem. Sua senha atual permanecerá inalterada e sua conta protegida.
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding: 20px 32px; background-color: rgba(8, 12, 20, 0.5); text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.06);">
              <p style="margin: 0; font-size: 12px; color: #64748b;">
                &copy; 2026 SuperTrain. Todos os direitos reservados.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

                // Envia o e-mail utilizando a função nativa mail() do PHP
                $enviado = @mail($emailInformado, $assuntoCodificado, $corpoEmail, $headersStr);

                if ($enviado) {
                    $sucesso = 'Link de redefinição enviado com sucesso para o seu e-mail! Verifique sua caixa de entrada e spam.';
                    $emailInformado = '';
                } else {
                    $isLocalhost = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1']) || ($_SERVER['SERVER_NAME'] ?? '') === 'localhost';
                    if ($isLocalhost) {
                        $sucesso = 'Link de redefinição gerado com sucesso!'
                            . '<div class="modo-local-card">'
                            . '<div class="modo-local-header">'
                            . '<i class="bi bi-info-circle"></i>'
                            . '<span>Modo Local (XAMPP sem SMTP configurado)</span>'
                            . '</div>'
                            . '<p class="modo-local-desc">O envio de e-mails não está configurado neste ambiente. Acesse diretamente a redefinição pelo botão abaixo:</p>'
                            . '<a href="' . htmlspecialchars($linkRedefinicao) . '" class="btn-modo-local">'
                            . '<i class="bi bi-box-arrow-up-right"></i> Acessar Link de Redefinição (Ambiente Local)'
                            . '</a>'
                            . '</div>';
                        $emailInformado = '';
                    } else {
                        $erro = 'Não foi possível enviar o e-mail no momento. Por favor, tente novamente mais tarde.';
                    }
                }
            } else {
                $stmtIns->close();
                $erro = 'Não foi possível registrar a solicitação de recuperação. Tente novamente.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar Senha - SuperTrain</title>

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
        Informe seu e-mail para redefinir o acesso à sua conta.
      </h2>

      <div class="card recuperacao-card">
        <h1 class="card-title">Recuperação de Senha</h1>
        <p class="texto-info">
          Enviaremos um link seguro de redefinição com validade temporária diretamente para o seu e-mail.
        </p>

        <?php if ($erro !== ''): ?>
          <div class="aviso aviso-erro" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div><?= htmlspecialchars($erro) ?></div>
          </div>
        <?php endif; ?>

        <?php if ($sucesso !== ''): ?>
          <div class="aviso aviso-sucesso" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <div><?= $sucesso ?></div>
          </div>
        <?php endif; ?>

        <form method="post" action="recuperacao.php" autocomplete="off">
          <div class="form-group mb-4">
            <label for="emailRecuperacao" class="form-label">E-mail Cadastrado</label>
            <input type="email" class="form-control" id="emailRecuperacao" name="emailRecuperacao" placeholder="seuemail@supertrain.com" required autocomplete="email" value="<?= htmlspecialchars($emailInformado) ?>">
          </div>

          <button type="submit" class="btn w-100 btn-recuperar">
            <i class="bi bi-send"></i> Enviar Link de Redefinição
          </button>

          <div class="auth-links">
            <a href="login.php" class="voltar-login">
              <i class="bi bi-arrow-left"></i> Voltar para o Login
            </a>
          </div>
        </form>
      </div>
    </div>
  </main>

</body>

</html>
