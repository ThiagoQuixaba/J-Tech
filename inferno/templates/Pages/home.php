<?php
/**
 * Página personalizada do sistema de estoque
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
    <?= $this->Html->css(['normalize.min', 'milligram.min', 'cake']) ?>
</head>
<body>
    <div class="container" style="margin-top: 50px;">
        <h1>J-Tech - Sistema de Estoque</h1>
        <p>Bem-vindo ao sistema de controle de estoque da loja J-Tech.</p>

        <h3>Menu</h3>
        <ul>
            <li><a href="/produtos">Produtos</a></li>
            <li><a href="/categorias">Categorias</a></li>
            <li><a href="/fornecedores">Fornecedores</a></li>
            <li><a href="/fluxo">Fluxo de Entrada e Saída</a></li>
        </ul>

        <footer style="margin-top: 50px;">
            <p>&copy; <?= date('Y') ?> J-Tech. Todos os direitos reservados.</p>
        </footer>
    </div>
</body>
</html>
