<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Categorium $categorium
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Categorium'), ['action' => 'edit', $categorium->nome], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Categorium'), ['action' => 'delete', $categorium->nome], ['confirm' => __('Are you sure you want to delete # {0}?', $categorium->nome), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Categoria'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Categorium'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="categoria view content">
            <h3><?= h($categorium->nome) ?></h3>
            <table>
            </table>
            <div class="text">
                <strong><?= __('Nome') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($categorium->nome)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Descricao') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($categorium->descricao)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>