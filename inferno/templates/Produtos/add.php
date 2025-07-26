<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Produto $produto
 * @var array $categorias
 * @var array $fornecedores
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Produtos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>

    <div class="column column-80">
        <div class="produtos form content">
            <?= $this->Form->create($produto) ?>
            <fieldset>
                <legend><?= __('Add Produto') ?></legend>
                <?= $this->Form->control('categoria', ['options' => $categorias, 'empty' => 'Selecione uma categoria']) ?>
                <?= $this->Form->control('fornecedor', ['options' => $fornecedores, 'empty' => 'Selecione um fornecedor']) ?>
                <?= $this->Form->control('nome') ?>
                <?= $this->Form->control('descricao') ?>
                <?= $this->Form->control('quantidade') ?>
                <?= $this->Form->control('valor') ?>
                <?= $this->Form->control('fabricacao', ['type' => 'date']) ?>
                <?= $this->Form->control('validade', ['type' => 'date']) ?>
            </fieldset>
            <?= $this->Form->button(__('Salvar')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
