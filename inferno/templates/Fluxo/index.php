<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Fluxo> $fluxo
 */
?>
<div class="fluxo index content">
    <h3><?= __('Fluxo') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('lote') ?></th>
                    <th><?= $this->Paginator->sort('tipo') ?></th>
                    <th><?= $this->Paginator->sort('data') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fluxo as $fluxo): ?>
                <tr>
                    <td><?= $this->Number->format($fluxo->id) ?></td>
                    <td><?= h($fluxo->lote) ?></td>
                    <td><?= h($fluxo->tipo) ?></td>
                    <td><?= h($fluxo->data) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
        <!-- ✅ Botão Voltar para Home amarelo -->
    <div style="margin-top: 15px;">
        <?= $this->Html->link(
            '🏠 Voltar para Home',
            ['controller' => 'Pages', 'action' => 'display', 'home'],
            [
                'style' => 'background: #ffeb3b; color: #000; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold;'
            ]
        ) ?>
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