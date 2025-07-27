<?php
/**
 * Página personalizada do sistema de estoque com IHC melhorado
 * @var \App\View\AppView $this
 */
$this->disableAutoLayout();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>J-Tech - Estoque</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Container principal -->
    <div class="container mt-5">

        <!-- Cabeçalho -->
        <div class="text-center mb-4">
            <h1 class="text-primary">📦 J-Tech - Sistema de Estoque</h1>
            <p class="lead">Bem-vindo ao sistema de controle de estoque da loja <strong>J-Tech</strong>.</p>
        </div>

        <!-- Card com Menu -->
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">📋 Menu Principal</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">Selecione uma das opções abaixo para gerenciar o estoque:</p>

                <!-- Menu organizado em lista de botões -->
                <div class="d-grid gap-3">
                    <a href="/produtos" class="btn btn-primary text-start">
                        📦 Produtos
                    </a>
                    <a href="/categorias" class="btn btn-info text-start text-white">
                        🗂️ Categorias
                    </a>
                    <a href="/fornecedores" class="btn btn-warning text-start">
                        🏭 Fornecedores
                    </a>
                    <a href="/fluxo" class="btn btn-success text-start">
                        🔄 Fluxo de Entrada e Saída
                    </a>
                </div>
            </div>
        </div>

        <!-- Rodapé -->
        <footer class="text-center text-muted mt-4">
            <p>&copy; <?= date('Y') ?> J-Tech. Todos os direitos reservados.</p>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
