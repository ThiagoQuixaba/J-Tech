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
            <?= $this->Html->link(__('Edit Categoria'), ['action' => 'edit', $categorium->nome], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Categoria'), ['action' => 'delete', $categorium->nome], ['confirm' => __('Você realmente deseja excluir o Categoria registrado com o CNPJ {0}?', $categorium->nome), 'class' => 'side-nav-item', 'onmouseover' => "this.style.color='#d33c43'", 'onmouseout' => "this.style.color='#606c76'"]) ?>
            <?= $this->Html->link(__('List Categorias'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Categoria'), ['action' => 'add'], ['class' => 'side-nav-item', 'onmouseover' => "this.style.color='#1f9d55'", 'onmouseout' => "this.style.color='#606c76'"]) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="categoria view content">
            <h3><?= h($categorium->nome) ?></h3>
            <table>
                <tr>
                    <th><?= __('Nome') ?></th>
                    <td><?= h($categorium->nome) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Descricao') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($categorium->descricao)); ?>
                </blockquote>
            </div>
            <?= $this->Html->link('voltar', ['controller' => 'Fornecedores', 'action' => 'index'], ['class' => 'button', 'style' => 'background-color: #d33c43; border-color: #d33c43', 'onmouseover' => "this.style.backgroundColor='#606c76', this.style.borderColor='#606c76'", 'onmouseout' => "this.style.backgroundColor='#d33c43', this.style.borderColor='#d33c43'"]) ?>
        </div>
    </div>
</div>