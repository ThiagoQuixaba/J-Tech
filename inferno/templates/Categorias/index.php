<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Categorium> $categoria
 */
?>
<div class="categoria index content">
    <?= $this->Html->link(__('Nova Categoria'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Categoria') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('nome') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categoria as $categorium): ?>
                <tr>
                    <td><?= h($categorium->nome) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(
                            '<i class="fa fa-eye" aria-hidden="true"></i>', 
                            ['action' => 'view', $categorium->nome], 
                            ['escape' => false, 'title' => 'Visualizar']
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fa fa-pencil-alt" aria-hidden="true"></i>', 
                            ['action' => 'edit', $categorium->nome], 
                            ['escape' => false, 'title' => 'Editar']
                        ) ?>
                        <?= $this->Form->postLink(
                            '<i class="fa fa-trash" aria-hidden="true"></i>', 
                            ['action' => 'delete', $categorium->nome], 
                            [
                                'method' => 'delete', 
                                'confirm' => __('Você realmente deseja excluir o fornecedor registrado com o CNPJ {0}?', $categorium->nome), 
                                'escape' => false,
                                'onmouseover' => "this.style.color='#d33c43'", 
                                'onmouseout' => "this.style.color='#606c76'",
                                'title' => 'Excluir'
                            ]
                        ) ?>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 15px;">
        <?= $this->Html->link(
            '🏠 Voltar para Home',
            ['controller' => 'Pages', 'action' => 'display', 'home'],
            [
                'style' => 'background: #ffeb3b; color: #000; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold;'
            ]
        ) ?>
    </div>

    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('primeira')) ?>
            <?= $this->Paginator->prev('< ' . __('anterior')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('próxima') . ' >') ?>
            <?= $this->Paginator->last(__('última') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registro(s) de um total de {{count}}')) ?></p>
    </div>
</div>
