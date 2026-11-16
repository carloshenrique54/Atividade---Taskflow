<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'TaskFlow';
$user = currentUser();
$notice = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#101827">
    <title><?= h($pageTitle) ?> | TaskFlow</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <div class="container nav-wrap">
        <a class="brand" href="index.php"><span class="brand-mark">T</span> taskflow</a>
        <nav class="nav-links">
            <a href="index.php">Início</a>
            <?php if ($user): ?>
                <a href="tarefas.php">Minhas tarefas</a>
                <a href="perfil.php">Perfil</a>
                <?php if ($user['tipo'] === 'admin'): ?>
                    <a href="admin.php">Administração</a>
                <?php endif; ?>
                <form action="logout.php" method="post" class="nav-form">
                    <?= csrfField() ?>
                    <button class="nav-logout" type="submit">Sair</button>
                </form>
            <?php else: ?>
                <a href="login.php">Entrar</a>
                <a class="nav-cta" href="cadastro.php">Criar conta</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container main-content">
    <?php if ($notice): ?>
        <div class="alert alert-<?= h($notice['type']) ?>"><?= h($notice['message']) ?></div>
    <?php endif; ?>
