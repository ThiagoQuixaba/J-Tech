<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Produto $produto
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $produto->lote],
                ['confirm' => __('Are you sure you want to delete # {0}?', $produto->lote), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Produtos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="produtos form content">
            <?= $this->Form->create($produto) ?>
            <fieldset>
                <legend><?= __('Edit Produto') ?></legend>
                <?php
                    echo $this->Form->control('categoria', ['options' => $categorias, 'empty' => 'Selecione uma categoria']);
                    echo $this->Form->control('fornecedor', ['options' => $fornecedores, 'empty' => 'Selecione um fornecedor']);
                    echo $this->Form->control('nome');
                    echo $this->Form->control('descricao');
                    echo $this->Form->control('quantidade');
                    echo $this->Form->control('valor');
                    echo $this->Form->control('fabricacao');
                    echo $this->Form->control('validade');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
