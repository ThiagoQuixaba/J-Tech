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
            <?= $this->Html->link(__('Edit Produto'), ['action' => 'edit', $produto->lote], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Produto'), ['action' => 'delete', $produto->lote], ['confirm' => __('Você realmente deseja excluir o produto registrado com o lote {0}?', $produto->lote), 'class' => 'side-nav-item', 'onmouseover' => "this.style.color='#d33c43'", 'onmouseout' => "this.style.color='#606c76'"]) ?>
            <?= $this->Html->link(__('List Produtos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Produto'), ['action' => 'add'], ['class' => 'side-nav-item', 'onmouseover' => "this.style.color='#1f9d55'", 'onmouseout' => "this.style.color='#606c76'"]) ?>
        </div>
    </aside>

    <div class="column column-80">
        <div class="produtos form content">
            <?= $this->Form->create($produto) ?>
            <fieldset>
                <legend><?= __('Edit Produto') ?></legend>
                <?= $this->Form->control('categoria', ['options' => $categorias, 'empty' => 'Selecione uma categoria']) ?>
                <?= $this->Form->control('fornecedor', ['options' => $fornecedores, 'empty' => 'Selecione um fornecedor']) ?>
                <?= $this->Form->control('nome') ?>
                <?= $this->Form->control('descricao', ['label' => 'Descrição']) ?>
                <?= $this->Form->control('quantidade') ?>
                <?= $this->Form->control('valor') ?>
                <?= $this->Form->control('fabricacao', ['type' => 'date']) ?>
                <?= $this->Form->control('validade', ['type' => 'date']) ?>
            </fieldset>
            <?= $this->Html->link('cancelar', ['controller' => 'Produtos', 'action' => 'index'], ['class' => 'button', 'style' => 'background-color: #d33c43; border-color: #d33c43', 'onmouseover' => "this.style.backgroundColor='#606c76', this.style.borderColor='#606c76'", 'onmouseout' => "this.style.backgroundColor='#d33c43', this.style.borderColor='#d33c43'"]) ?>
            <?= $this->Form->button(__('salvar')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
