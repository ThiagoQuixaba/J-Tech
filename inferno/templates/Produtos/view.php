<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Produto $produto
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Produto'), ['action' => 'edit', $produto->lote], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(
                __('Delete Produto'),
                ['action' => 'delete', $produto->lote],
                ['confirm' => __('Are you sure you want to delete # {0}?', $produto->lote), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Produtos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Produto'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>

    <div class="column column-80">
        <div class="produtos view content">
            <h3><?= h($produto->nome) ?></h3>
            <table class="table table-striped table-bordered">
                <tr>
                    <th><?= __('Categoria') ?></th>
                    <td><?= h($produto->categoria) ?></td>
                </tr>
                <tr>
                    <th><?= __('Fornecedor') ?></th>
                    <td><?= h($produto->fornecedor) ?></td>
                </tr>
                <tr>
                    <th><?= __('Nome') ?></th>
                    <td><?= h($produto->nome) ?></td>
                </tr>
                <tr>
                    <th><?= __('Lote') ?></th>
                    <td><?= $this->Number->format($produto->lote) ?></td>
                </tr>
                <tr>
                    <th><?= __('Quantidade') ?></th>
                    <td><?= $produto->quantidade === null ? '' : $this->Number->format($produto->quantidade) ?></td>
                </tr>
                <tr>
                    <th><?= __('Valor') ?></th>
                    <td><?= $this->Number->format($produto->valor) ?></td>
                </tr>
                <tr>
                    <th><?= __('Fabricação') ?></th>
                    <td><?= h($produto->fabricacao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Validade') ?></th>
                    <td><?= h($produto->validade) ?></td>
                </tr>
            </table>

            <div class="text">
                <strong><?= __('Descrição') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($produto->descricao)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>
