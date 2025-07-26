<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Fluxo $fluxo
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Fluxo'), ['action' => 'edit', $fluxo->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Fluxo'), ['action' => 'delete', $fluxo->id], ['confirm' => __('Are you sure you want to delete # {0}?', $fluxo->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Fluxo'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Fluxo'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="fluxo view content">
            <h3><?= h($fluxo->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Tipo') ?></th>
                    <td><?= h($fluxo->tipo) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($fluxo->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Lote') ?></th>
                    <td><?= $this->Number->format($fluxo->lote) ?></td>
                </tr>
                <tr>
                    <th><?= __('Data') ?></th>
                    <td><?= h($fluxo->data) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>