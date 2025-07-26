<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Fluxo $fluxo
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Fluxo'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="fluxo form content">
            <?= $this->Form->create($fluxo) ?>
            <fieldset>
                <legend><?= __('Add Fluxo') ?></legend>
                <?php
                    echo $this->Form->control('lote', ['options' => $lote, 'empty' => 'Selecione uma categoria']);
                    echo $this->Form->control('tipo', ['options' => ['Entrada' => 'Entrada', 'Saida' => 'Saida'], 'empty' => 'Selecione uma categoria']);
                    echo $this->Form->control('data', ['empty' => true]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
