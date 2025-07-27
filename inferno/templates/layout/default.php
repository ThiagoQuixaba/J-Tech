<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

$cakeDescription = 'CakePHP: the rapid development php framework';
?>
<!DOCTYPE html>
<html>
<head>
    <style>
    :root {
        --bg-color: #f4f6f9;
        --text-color: #111;
        --card-bg: #fff;
        --font-size: 1rem;
    }

    body {
        font-size: var(--font-size);
        background-color: var(--bg-color);
        color: var(--text-color);
    }



    .btn-acess {
        background: #333;
        color: #fff;
        border: none;
        padding: 0.4rem 0.6rem;
        margin-left: 5px;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .flash-success { background: #d4edda; color: #155724; padding: 1rem; margin: 1rem 0; border-radius: 5px; }
    .flash-error { background: #f8d7da; color: #721c24; padding: 1rem; margin: 1rem 0; border-radius: 5px; }
    .flash-warning { background: #fff3cd; color: #856404; padding: 1rem; margin: 1rem 0; border-radius: 5px; }
    .flash-info { background: #d1ecf1; color: #0c5460; padding: 1rem; margin: 1rem 0; border-radius: 5px; }
</style>

    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css(['normalize.min', 'milligram.min', 'fonts', 'cake']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>

    <nav class="top-nav">
        <div class="top-nav-title">
            <a href="<?= $this->Url->build('/') ?>">J<span>Tech</span></a>
        </div>
        <div class="top-nav-links">
            <!-- <a target="_blank" rel="noopener" href="https://book.cakephp.org/5/">Documentation</a>
            <a target="_blank" rel="noopener" href="https://api.cakephp.org/">API</a> -->
        </div>
    </nav>
    <main class="main">
        <div class="container">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>
    <footer>
    </footer>
</body>
</html>
