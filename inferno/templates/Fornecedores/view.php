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
            <?= $this->Form->postLink(__('Delete Fornecedor'), ['action' => 'delete', $fornecedor->cnpj], ['confirm' => __('Você realmente deseja excluir o fornecedor registrado com o CNPJ {0}?', $fornecedor->cnpj), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Fornecedor'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Fornecedor'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="fornecedor view content">
            <h3><?= h($fornecedor->cnpj) ?></h3>
            <table>
                <tr>
                    <th><?= __('Cnpj') ?></th>
                    <td><?= h($fornecedor->cnpj) ?></td>
                </tr>
                <tr>
                    <th><?= __('Nome') ?></th>
                    <td><?= h($fornecedor->nome) ?></td>
                </tr>
                <tr>
                    <th><?= __('Contato') ?></th>
                    <td><?= h($fornecedor->contato) ?></td>
                </tr>
            </table>
            <?= $this->Html->link('voltar', ['controller' => 'Fornecedores', 'action' => 'index'], ['class' => 'button', 'style' => 'background-color: #d33c43; border-color: #d33c43', 'onmouseover' => "this.style.backgroundColor='#606c76', this.style.borderColor='#606c76'", 'onmouseout' => "this.style.backgroundColor='#d33c43', this.style.borderColor='#d33c43'"]) ?>
        </div>
    </div>
</div>