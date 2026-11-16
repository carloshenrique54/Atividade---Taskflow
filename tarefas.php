<?php
require_once __DIR__ . '/includes/functions.php';
requireLogin();
$pageTitle = 'Minhas tarefas';
$user = currentUser();
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'criar') {
        $titulo = trim($_POST['titulo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $prioridade = $_POST['prioridade'] ?? 'media';
        $prazo = $_POST['prazo'] ?? '';
        $prioridades = ['baixa', 'media', 'alta'];

        if ($titulo === '') {
            $erro = 'Informe um título para a tarefa.';
        } elseif (!in_array($prioridade, $prioridades, true)) {
            $erro = 'Selecione uma prioridade válida.';
        } elseif ($prazo !== '' && (($dataPrazo = DateTime::createFromFormat('!Y-m-d', $prazo)) === false || $dataPrazo->format('Y-m-d') !== $prazo)) {
            $erro = 'Informe um prazo válido.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO tarefas (usuario_id, titulo, descricao, prioridade, prazo) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$user['id'], $titulo, $descricao, $prioridade, $prazo !== '' ? $prazo : null]);
            flash('success', 'Tarefa adicionada.');
            redirect('tarefas.php');
        }
    } elseif ($action === 'status') {
        $id = (int) ($_POST['tarefa_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (in_array($status, ['pendente', 'andamento', 'concluida'], true)) {
            $stmt = $pdo->prepare('UPDATE tarefas SET status = ? WHERE id = ? AND usuario_id = ?');
            $stmt->execute([$status, $id, $user['id']]);
            flash('success', 'Status atualizado.');
            redirect('tarefas.php');
        }
        $erro = 'Status inválido.';
    } elseif ($action === 'excluir') {
        $id = (int) ($_POST['tarefa_id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM tarefas WHERE id = ? AND usuario_id = ?');
        $stmt->execute([$id, $user['id']]);
        flash('success', 'Tarefa excluída.');
        redirect('tarefas.php');
    }
}

$filtro = $_GET['status'] ?? 'todas';
$sql = 'SELECT * FROM tarefas WHERE usuario_id = ?';
$params = [$user['id']];
if (in_array($filtro, ['pendente', 'andamento', 'concluida'], true)) {
    $sql .= ' AND status = ?';
    $params[] = $filtro;
}
$sql .= ' ORDER BY CASE WHEN status = \'concluida\' THEN 1 ELSE 0 END, prazo IS NULL, prazo ASC, criado_em DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tarefas = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">PLANEJAMENTO</p><h1>Minhas tarefas</h1><p class="muted">Organize o que precisa ser feito e acompanhe cada etapa.</p></div></section>
<div class="tasks-layout">
    <section class="panel create-panel">
        <div class="panel-heading"><div><h2>Nova tarefa</h2><p>Adicione uma atividade à sua lista.</p></div><span class="feature-icon">＋</span></div>
        <?php if ($erro): ?><div class="alert alert-error"><?= h($erro) ?></div><?php endif; ?>
        <form method="post" class="form-stack">
            <?= csrfField() ?><input type="hidden" name="action" value="criar">
            <label>Título<input type="text" name="titulo" maxlength="160" placeholder="Ex.: Estudar para a prova" required></label>
            <label>Descrição<textarea name="descricao" rows="3" placeholder="Detalhes (opcional)"></textarea></label>
            <div class="form-row">
                <label>Prioridade<select name="prioridade"><option value="baixa">Baixa</option><option value="media" selected>Média</option><option value="alta">Alta</option></select></label>
                <label>Prazo<input type="date" name="prazo"></label>
            </div>
            <button class="btn btn-primary btn-full" type="submit">Adicionar tarefa</button>
        </form>
    </section>
    <section class="task-list-section">
        <div class="list-heading"><div><h2>Sua lista</h2><p><?= count($tarefas) ?> tarefa(s) nesta visualização</p></div>
            <form method="get" class="filter-form"><select name="status" onchange="this.form.submit()"><option value="todas" <?= $filtro === 'todas' ? 'selected' : '' ?>>Todas</option><option value="pendente" <?= $filtro === 'pendente' ? 'selected' : '' ?>>Pendentes</option><option value="andamento" <?= $filtro === 'andamento' ? 'selected' : '' ?>>Em andamento</option><option value="concluida" <?= $filtro === 'concluida' ? 'selected' : '' ?>>Concluídas</option></select></form>
        </div>
        <?php if (!$tarefas): ?>
            <div class="empty-state"><div class="empty-icon">✓</div><h3>Nada por aqui ainda</h3><p>Adicione uma tarefa ou altere o filtro para visualizar outras atividades.</p></div>
        <?php else: ?>
            <div class="task-list">
                <?php foreach ($tarefas as $tarefa): ?>
                    <article class="task-card <?= $tarefa['status'] === 'concluida' ? 'task-done' : '' ?>">
                        <div class="task-card-top"><div><span class="priority priority-<?= h($tarefa['prioridade']) ?>"><?= ucfirst($tarefa['prioridade']) ?></span><span class="task-date">Prazo: <?= h(formatDate($tarefa['prazo'])) ?></span></div>
                            <form method="post" onsubmit="return confirm('Excluir esta tarefa?')">
                                <?= csrfField() ?><input type="hidden" name="action" value="excluir"><input type="hidden" name="tarefa_id" value="<?= (int) $tarefa['id'] ?>"><button class="icon-button" type="submit" title="Excluir tarefa">×</button>
                            </form>
                        </div>
                        <h3><?= h($tarefa['titulo']) ?></h3>
                        <?php if ($tarefa['descricao']): ?><p><?= nl2br(h($tarefa['descricao'])) ?></p><?php endif; ?>
                        <div class="task-card-bottom"><span class="status-label status-<?= h($tarefa['status']) ?>"><?= $tarefa['status'] === 'andamento' ? 'Em andamento' : ($tarefa['status'] === 'concluida' ? 'Concluída' : 'Pendente') ?></span>
                            <form method="post" class="status-form">
                                <?= csrfField() ?><input type="hidden" name="action" value="status"><input type="hidden" name="tarefa_id" value="<?= (int) $tarefa['id'] ?>">
                                <select name="status" onchange="this.form.submit()" aria-label="Alterar status"><option value="pendente" <?= $tarefa['status'] === 'pendente' ? 'selected' : '' ?>>Pendente</option><option value="andamento" <?= $tarefa['status'] === 'andamento' ? 'selected' : '' ?>>Em andamento</option><option value="concluida" <?= $tarefa['status'] === 'concluida' ? 'selected' : '' ?>>Concluída</option></select>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
