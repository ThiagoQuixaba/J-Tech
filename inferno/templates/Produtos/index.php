<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Produto> $produtos
 */
?>
<div class="produtos index content">
    <?= $this->Html->link(__('New Produto'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Produtos') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('lote') ?></th>
                    <th><?= $this->Paginator->sort('categoria') ?></th>
                    <th><?= $this->Paginator->sort('fornecedor') ?></th>
                    <th><?= $this->Paginator->sort('nome') ?></th>
                    <th><?= $this->Paginator->sort('quantidade') ?></th>
                    <th><?= $this->Paginator->sort('valor') ?></th>
                    <th><?= $this->Paginator->sort('fabricacao') ?></th>
                    <th><?= $this->Paginator->sort('validade') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?= $this->Number->format($produto->lote) ?></td>
                    <td><?= h($produto->categoria) ?></td>
                    <td><?= h($produto->fornecedor) ?></td>
                    <td><?= h($produto->nome) ?></td>
                    <td><?= $produto->quantidade === null ? '' : $this->Number->format($produto->quantidade) ?></td>
                    <td><?= $this->Number->format($produto->valor) ?></td>
                    <td><?= h($produto->fabricacao) ?></td>
                    <td><?= h($produto->validade) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $produto->lote]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $produto->lote]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $produto->lote],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $produto->lote),
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>