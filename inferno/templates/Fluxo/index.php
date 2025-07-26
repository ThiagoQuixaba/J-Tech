<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Fluxo> $fluxo
 */
?>
<div class="fluxo index content">
    <?= $this->Html->link(__('New Fluxo'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Fluxo') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('lote') ?></th>
                    <th><?= $this->Paginator->sort('tipo') ?></th>
                    <th><?= $this->Paginator->sort('data') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fluxo as $fluxo): ?>
                <tr>
                    <td><?= $this->Number->format($fluxo->id) ?></td>
                    <td><?= $this->Number->format($fluxo->lote) ?></td>
                    <td><?= h($fluxo->tipo) ?></td>
                    <td><?= h($fluxo->data) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $fluxo->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $fluxo->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $fluxo->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $fluxo->id),
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