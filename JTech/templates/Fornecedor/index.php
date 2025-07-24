<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Fornecedor> $fornecedor
 */
?>
<div class="fornecedor index content">
    <?= $this->Html->link(__('New Fornecedor'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Fornecedor') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fornecedor as $fornecedor): ?>
                <tr>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $fornecedor->cnpj]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $fornecedor->cnpj]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $fornecedor->cnpj],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $fornecedor->cnpj),
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