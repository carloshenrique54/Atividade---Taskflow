<?php
require_once __DIR__ . '/includes/functions.php';
requireLogin();
$pageTitle = 'Meu perfil';
$user = currentUser();
$erroDados = '';
$erroSenha = '';
$mensagemDados = '';
$mensagemSenha = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'dados') {
        $nome = trim($_POST['nome'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));

        if ($nome === '' || $email === '') {
            $erroDados = 'Preencha nome e e-mail.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erroDados = 'Digite um e-mail válido.';
        } else {
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? AND id <> ?');
            $stmt->execute([$email, $user['id']]);
            if ($stmt->fetch()) {
                $erroDados = 'Este e-mail já pertence a outra conta.';
            } else {
                $stmt = $pdo->prepare('UPDATE usuarios SET nome = ?, email = ? WHERE id = ?');
                $stmt->execute([$nome, $email, $user['id']]);
                $_SESSION['user']['nome'] = $nome;
                $_SESSION['user']['email'] = $email;
                $user = currentUser();
                $mensagemDados = 'Dados atualizados.';
            }
        }
    } elseif ($action === 'senha') {
        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $stmt = $pdo->prepare('SELECT senha FROM usuarios WHERE id = ?');
        $stmt->execute([$user['id']]);
        $senhaHash = $stmt->fetchColumn();

        if (!password_verify($senhaAtual, $senhaHash)) {
            $erroSenha = 'Senha atual incorreta.';
        } elseif (strlen($novaSenha) < 6) {
            $erroSenha = 'A nova senha precisa ter pelo menos 6 caracteres.';
        } else {
            $stmt = $pdo->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
            $stmt->execute([password_hash($novaSenha, PASSWORD_DEFAULT), $user['id']]);
            $mensagemSenha = 'Senha alterada.';
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">CONTA</p><h1>Meu perfil</h1><p class="muted">Mantenha seus dados atualizados.</p></div></section>
<div class="two-column">
    <section class="panel">
        <div class="panel-heading"><div><h2>Dados pessoais</h2><p>Informações da sua conta.</p></div><span class="avatar"><?= h(strtoupper(substr($user['nome'], 0, 1))) ?></span></div>
        <?php if ($erroDados): ?><div class="alert alert-error"><?= h($erroDados) ?></div><?php endif; ?>
        <?php if ($mensagemDados): ?><div class="alert alert-success"><?= h($mensagemDados) ?></div><?php endif; ?>
        <form method="post" class="form-stack">
            <?= csrfField() ?><input type="hidden" name="action" value="dados">
            <label>Nome<input type="text" name="nome" maxlength="100" value="<?= h($user['nome']) ?>" required></label>
            <label>E-mail<input type="email" name="email" maxlength="150" value="<?= h($user['email']) ?>" required></label>
            <button class="btn btn-primary" type="submit">Salvar alterações</button>
        </form>
    </section>
    <section class="panel">
        <div class="panel-heading"><div><h2>Alterar senha</h2><p>Escolha uma senha com pelo menos 6 caracteres.</p></div></div>
        <?php if ($erroSenha): ?><div class="alert alert-error"><?= h($erroSenha) ?></div><?php endif; ?>
        <?php if ($mensagemSenha): ?><div class="alert alert-success"><?= h($mensagemSenha) ?></div><?php endif; ?>
        <form method="post" class="form-stack">
            <?= csrfField() ?><input type="hidden" name="action" value="senha">
            <label>Senha atual<input type="password" name="senha_atual" required></label>
            <label>Nova senha<input type="password" name="nova_senha" minlength="6" required></label>
            <button class="btn btn-dark" type="submit">Trocar senha</button>
        </form>
    </section>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
