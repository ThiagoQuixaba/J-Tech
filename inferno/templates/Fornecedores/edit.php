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
            <?= $this->Form->postLink(__('Delete Fornecedor'), ['action' => 'delete', $fornecedor->cnpj], ['confirm' => __('Você realmente deseja excluir o fornecedor registrado com o CNPJ {0}?', $fornecedor->cnpj), 'class' => 'side-nav-item', 'onmouseover' => "this.style.color='#d33c43'", 'onmouseout' => "this.style.color='#606c76'"]) ?>
            <?= $this->Html->link(__('List Fornecedor'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Fornecedor'), ['action' => 'add'], ['class' => 'side-nav-item', 'onmouseover' => "this.style.color='#1f9d55'", 'onmouseout' => "this.style.color='#606c76'"]) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="fornecedor form content">
            <?= $this->Form->create($fornecedor) ?>
            <fieldset>
                <legend><?= __('Edit Fornecedor') ?></legend>
                <?php
                    echo $this->Form->control('cnpj', ['label' => 'CNPJ', 'type' => 'text', 'required' => true]);
                    echo $this->Form->control('nome');
                    echo $this->Form->control('contato');
                ?>
            </fieldset>
            <?= $this->Html->link('cancelar', ['controller' => 'Fornecedores', 'action' => 'index'], ['class' => 'button', 'style' => 'background-color: #d33c43; border-color: #d33c43', 'onmouseover' => "this.style.backgroundColor='#606c76', this.style.borderColor='#606c76'", 'onmouseout' => "this.style.backgroundColor='#d33c43', this.style.borderColor='#d33c43'"]) ?>
            <?= $this->Form->button(__('concluir'))?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
