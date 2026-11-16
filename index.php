<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Início';
$user = currentUser();

if ($user) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tarefas WHERE usuario_id = ?");
    $stmt->execute([$user['id']]);
    $totalTarefas = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tarefas WHERE usuario_id = ? AND status = 'concluida'");
    $stmt->execute([$user['id']]);
    $concluidas = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tarefas WHERE usuario_id = ? AND status <> 'concluida'");
    $stmt->execute([$user['id']]);
    $pendentes = (int) $stmt->fetchColumn();
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($user): ?>
    <section class="welcome-row">
        <div>
            <p class="eyebrow">SEU ESPAÇO DE ORGANIZAÇÃO</p>
            <h1>Olá, <?= h($user['nome']) ?>.</h1>
            <p class="muted">Um bom plano transforma uma lista de tarefas em progresso.</p>
        </div>
        <a class="btn btn-primary" href="tarefas.php">+ Nova tarefa</a>
    </section>

    <section class="stats-grid">
        <article class="stat-card"><span class="stat-label">Total de tarefas</span><strong><?= $totalTarefas ?></strong><span class="stat-foot">Todas as suas atividades</span></article>
        <article class="stat-card"><span class="stat-label">Em aberto</span><strong><?= $pendentes ?></strong><span class="stat-foot">Ainda precisam de atenção</span></article>
        <article class="stat-card"><span class="stat-label">Concluídas</span><strong><?= $concluidas ?></strong><span class="stat-foot">Ótimo trabalho!</span></article>
    </section>

    <section class="panel home-panel">
        <div>
            <p class="eyebrow">PRÓXIMO PASSO</p>
            <h2>O que você precisa resolver hoje?</h2>
            <p class="muted">Adicione uma tarefa, defina a prioridade e acompanhe seu progresso.</p>
        </div>
        <a class="btn btn-dark" href="tarefas.php">Acessar minhas tarefas</a>
    </section>
<?php else: ?>
    <section class="hero">
        <div class="hero-copy">
            <span class="pill">ORGANIZAÇÃO SEM COMPLICAÇÃO</span>
            <h1>Menos bagunça.<br><span>Mais coisas feitas.</span></h1>
            <p>O TaskFlow é um espaço simples para organizar suas tarefas, acompanhar prazos e tirar seus planos do papel.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="cadastro.php">Começar agora <span>→</span></a>
                <a class="btn btn-outline" href="login.php">Já tenho uma conta</a>
            </div>
            <div class="hero-note"><span class="check-dot">✓</span> Gratuito e fácil de usar</div>
        </div>
        <div class="hero-visual">
            <div class="visual-glow"></div>
            <div class="task-preview preview-back"><span class="tiny-check">✓</span><div><b>Revisar conteúdo</b><small>Hoje · Prioridade alta</small></div></div>
            <div class="task-preview preview-main"><div class="preview-heading"><span>MINHA ROTINA</span><span class="preview-dots">•••</span></div><h3>Pequenos passos,<br>grandes resultados.</h3><div class="progress-line"><span></span></div><div class="progress-label"><small>Progresso semanal</small><b>75%</b></div><div class="preview-item"><span class="tiny-check">✓</span><span>Organizar materiais</span><small>Concluída</small></div><div class="preview-item"><span class="empty-check"></span><span>Finalizar projeto</span><small>Em aberto</small></div></div>
            <div class="floating-note"><span>✦</span> Um dia de cada vez</div>
        </div>
    </section>
    <section class="features-grid">
        <article class="feature-card"><div class="feature-icon">↗</div><h3>Tenha clareza</h3><p>Veja tudo o que precisa fazer em um só lugar.</p></article>
        <article class="feature-card"><div class="feature-icon">◷</div><h3>Cuide dos prazos</h3><p>Defina datas e prioridades para cada atividade.</p></article>
        <article class="feature-card"><div class="feature-icon">✓</div><h3>Acompanhe o avanço</h3><p>Marque tarefas concluídas e acompanhe sua evolução.</p></article>
    </section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
