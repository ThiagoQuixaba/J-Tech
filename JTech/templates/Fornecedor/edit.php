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
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $fornecedor->cnpj],
                ['confirm' => __('Are you sure you want to delete # {0}?', $fornecedor->cnpj), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Fornecedor'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="fornecedor form content">
            <?= $this->Form->create($fornecedor) ?>
            <fieldset>
                <legend><?= __('Edit Fornecedor') ?></legend>
                <?php
                    echo $this->Form->control('nome');
                    echo $this->Form->control('contato');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
