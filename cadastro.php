<?php
require_once __DIR__ . '/includes/functions.php';
if (isLoggedIn()) {
    redirect('index.php');
}

$pageTitle = 'Criar conta';
$erro = '';
$nome = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $nome = trim($_POST['nome'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || $email === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Digite um e-mail válido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha precisa ter pelo menos 6 caracteres.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, \'comum\')');
            $stmt->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT)]);
            flash('success', 'Cadastro realizado! Agora é só entrar.');
            redirect('login.php');
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="auth-layout">
    <div class="auth-intro"><span class="pill">BEM-VINDO AO TASKFLOW</span><h1>Comece a organizar<br>suas ideias.</h1><p>Crie sua conta e tenha suas tarefas sempre à mão.</p></div>
    <div class="form-card">
        <div class="form-heading"><h2>Criar conta</h2><p>Preencha seus dados para começar.</p></div>
        <?php if ($erro): ?><div class="alert alert-error"><?= h($erro) ?></div><?php endif; ?>
        <form method="post" class="form-stack">
            <?= csrfField() ?>
            <label>Nome completo<input type="text" name="nome" value="<?= h($nome) ?>" maxlength="100" placeholder="Seu nome" required></label>
            <label>E-mail<input type="email" name="email" value="<?= h($email) ?>" maxlength="150" placeholder="voce@email.com" required></label>
            <label>Senha<input type="password" name="senha" minlength="6" placeholder="Pelo menos 6 caracteres" required></label>
            <button class="btn btn-primary btn-full" type="submit">Criar minha conta</button>
        </form>
        <p class="form-bottom">Já tem uma conta? <a href="login.php">Entrar</a></p>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
