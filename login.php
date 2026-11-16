<?php
require_once __DIR__ . '/includes/functions.php';
if (isLoggedIn()) {
    redirect('index.php');
}

$pageTitle = 'Entrar';
$erro = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha e-mail e senha.';
    } else {
        $stmt = $pdo->prepare('SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha'])) {
            setUserSession($user);
            redirect($user['tipo'] === 'admin' ? 'admin.php' : 'index.php');
        }

        $erro = 'E-mail ou senha inválidos.';
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="auth-layout auth-layout-login">
    <div class="auth-intro"><span class="pill">BOM TER VOCÊ DE VOLTA</span><h1>Continue de onde<br>você parou.</h1><p>Entre para visualizar e atualizar suas tarefas.</p></div>
    <div class="form-card">
        <div class="form-heading"><h2>Entrar</h2><p>Use os dados da sua conta.</p></div>
        <?php if ($erro): ?><div class="alert alert-error"><?= h($erro) ?></div><?php endif; ?>
        <form method="post" class="form-stack">
            <?= csrfField() ?>
            <label>E-mail<input type="email" name="email" value="<?= h($email) ?>" placeholder="voce@email.com" required></label>
            <label>Senha<input type="password" name="senha" placeholder="Sua senha" required></label>
            <div class="form-inline"><a href="redefinir.php">Esqueci minha senha</a></div>
            <button class="btn btn-primary btn-full" type="submit">Entrar na plataforma</button>
        </form>
        <p class="form-bottom">Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
