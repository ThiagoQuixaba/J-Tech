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
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $categorium->nome],
                ['confirm' => __('Are you sure you want to delete # {0}?', $categorium->nome), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Categoria'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="categoria form content">
            <?= $this->Form->create($categorium) ?>
            <fieldset>
                <legend><?= __('Edit Categorium') ?></legend>
                <?php
                    echo $this->Form->control('nome', ['label' => 'Nome da Categoria', 'type' => 'text', 'required' => true]);
                    echo $this->Form->control('descricao');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>