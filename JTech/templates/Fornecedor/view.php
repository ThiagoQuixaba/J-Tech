<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Fornecedor $fornecedor
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Fornecedor'), ['action' => 'edit', $fornecedor->cnpj], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Fornecedor'), ['action' => 'delete', $fornecedor->cnpj], ['confirm' => __('Are you sure you want to delete # {0}?', $fornecedor->cnpj), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Fornecedor'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Fornecedor'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="fornecedor view content">
            <h3><?= h($fornecedor->cnpj) ?></h3>
            <table>
            </table>
            <div class="text">
                <strong><?= __('Cnpj') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($fornecedor->cnpj)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Nome') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($fornecedor->nome)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Contato') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($fornecedor->contato)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>