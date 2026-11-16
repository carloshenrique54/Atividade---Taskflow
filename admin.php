<?php
require_once __DIR__ . '/includes/functions.php';
requireAdmin();
$pageTitle = 'Administração';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $id = (int) ($_POST['usuario_id'] ?? 0);
    if ($id === (int) currentUser()['id']) {
        flash('error', 'Você não pode excluir sua própria conta.');
    } else {
        $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
        flash('success', 'Usuário excluído.');
    }
    redirect('admin.php');
}

$usuarios = $pdo->query('SELECT id, nome, email, tipo, criado_em FROM usuarios ORDER BY criado_em DESC')->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">PAINEL DE CONTROLE</p><h1>Administração</h1><p class="muted">Gerencie as contas cadastradas na plataforma.</p></div><span class="admin-count"><?= count($usuarios) ?> usuários</span></section>
<section class="panel table-panel">
    <div class="panel-heading"><div><h2>Usuários cadastrados</h2><p>Edite informações ou remova contas.</p></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Tipo</th><th>Cadastro</th><th>Ações</th></tr></thead>
            <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td>#<?= (int) $usuario['id'] ?></td>
                    <td class="table-name"><?= h($usuario['nome']) ?></td>
                    <td><?= h($usuario['email']) ?></td>
                    <td><span class="role role-<?= h($usuario['tipo']) ?>"><?= $usuario['tipo'] === 'admin' ? 'Admin' : 'Comum' ?></span></td>
                    <td><?= h(date('d/m/Y', strtotime($usuario['criado_em']))) ?></td>
                    <td><div class="table-actions"><a class="text-link" href="editar_usuario.php?id=<?= (int) $usuario['id'] ?>">Editar</a>
                        <?php if ((int) $usuario['id'] !== (int) currentUser()['id']): ?>
                        <form method="post" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                            <?= csrfField() ?><input type="hidden" name="usuario_id" value="<?= (int) $usuario['id'] ?>"><button class="text-danger" type="submit">Excluir</button>
                        </form>
                        <?php else: ?><span class="muted small-text">Sua conta</span><?php endif; ?>
                    </div></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$usuarios): ?><tr><td colspan="6" class="empty-cell">Nenhum usuário cadastrado.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
