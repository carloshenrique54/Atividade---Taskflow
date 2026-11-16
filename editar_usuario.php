<?php
require_once __DIR__ . '/includes/functions.php';
requireAdmin();
$pageTitle = 'Editar usuário';
$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT id, nome, email, tipo FROM usuarios WHERE id = ?');
$stmt->execute([$id]);
$usuario = $stmt->fetch();
if (!$usuario) {
    flash('error', 'Usuário não encontrado.');
    redirect('admin.php');
}
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $nome = trim($_POST['nome'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $tipo = $_POST['tipo'] ?? '';

    if ($nome === '' || $email === '') {
        $erro = 'Preencha nome e e-mail.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Digite um e-mail válido.';
    } elseif (!in_array($tipo, ['comum', 'admin'], true)) {
        $erro = 'Selecione um tipo válido.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? AND id <> ?');
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            $erro = 'Este e-mail já pertence a outra conta.';
        } else {
            $stmt = $pdo->prepare('UPDATE usuarios SET nome = ?, email = ?, tipo = ? WHERE id = ?');
            $stmt->execute([$nome, $email, $tipo, $id]);
            if ($id === (int) currentUser()['id']) {
                $_SESSION['user']['nome'] = $nome;
                $_SESSION['user']['email'] = $email;
                $_SESSION['user']['tipo'] = $tipo;
            }
            flash('success', 'Usuário atualizado.');
            redirect('admin.php');
        }
    }
    $usuario['nome'] = $nome;
    $usuario['email'] = $email;
    $usuario['tipo'] = $tipo;
}

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">PAINEL DE CONTROLE</p><h1>Editar usuário</h1><p class="muted">Atualize os dados e o nível de acesso da conta.</p></div><a class="btn btn-outline" href="admin.php">← Voltar</a></section>
<section class="panel edit-panel">
    <?php if ($erro): ?><div class="alert alert-error"><?= h($erro) ?></div><?php endif; ?>
    <form method="post" class="form-stack">
        <?= csrfField() ?>
        <label>Nome<input type="text" name="nome" maxlength="100" value="<?= h($usuario['nome']) ?>" required></label>
        <label>E-mail<input type="email" name="email" maxlength="150" value="<?= h($usuario['email']) ?>" required></label>
        <label>Tipo de conta<select name="tipo"><option value="comum" <?= $usuario['tipo'] === 'comum' ? 'selected' : '' ?>>Usuário comum</option><option value="admin" <?= $usuario['tipo'] === 'admin' ? 'selected' : '' ?>>Administrador</option></select></label>
        <button class="btn btn-primary" type="submit">Salvar usuário</button>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
