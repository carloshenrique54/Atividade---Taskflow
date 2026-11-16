<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Redefinir senha';
$erro = '';
$sucesso = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email = strtolower(trim($_POST['email'] ?? ''));

    if ($email === '') {
        $erro = 'Digite o e-mail cadastrado.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Digite um e-mail válido.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            $erro = 'E-mail não encontrado.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO redefinicoes_senha (usuario_id, email) VALUES (?, ?)');
            $stmt->execute([$usuario['id'], $email]);
            $sucesso = 'Solicitação registrada para ' . $email . '. Em uma versão real, as instruções seriam enviadas por e-mail.';
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="auth-layout auth-layout-single">
    <div class="form-card form-card-wide">
        <div class="form-heading"><span class="feature-icon">↻</span><h2>Redefinir senha</h2><p>Informe o e-mail associado à sua conta.</p></div>
        <?php if ($erro): ?><div class="alert alert-error"><?= h($erro) ?></div><?php endif; ?>
        <?php if ($sucesso): ?><div class="alert alert-success"><?= h($sucesso) ?></div><?php endif; ?>
        <form method="post" class="form-stack">
            <?= csrfField() ?>
            <label>E-mail cadastrado<input type="email" name="email" value="<?= h($email) ?>" placeholder="voce@email.com" required></label>
            <button class="btn btn-primary btn-full" type="submit">Enviar solicitação</button>
        </form>
        <p class="form-bottom"><a href="login.php">Voltar para o login</a></p>
        <p class="small-note">Demonstração acadêmica: a solicitação fica registrada no banco, mas não envia um e-mail nem altera a senha.</p>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
