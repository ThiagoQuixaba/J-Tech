<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Fornecedor> $fornecedor
 */
?>
<div class="fornecedor index content">
    <?= $this->Html->link(__('Novo Fornecedor'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Fornecedores') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('CNPJ') ?></th>
                    <th><?= $this->Paginator->sort('nome') ?></th>
                    <th><?= $this->Paginator->sort('contato') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fornecedor as $fornecedor): ?>
                <tr>
                    <td><?= h($fornecedor->cnpj) ?></td>
                    <td><?= h($fornecedor->nome) ?></td>
                    <td><?= h($fornecedor->contato) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(
                            '<i class="fa fa-eye" aria-hidden="true"></i>',
                            ['action' => 'view', $fornecedor->cnpj],
                            ['escape' => false, 'title' => 'Visualizar']
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fa fa-pencil-alt" aria-hidden="true"></i>',
                            ['action' => 'edit', $fornecedor->cnpj],
                            ['escape' => false, 'title' => 'Editar']
                        ) ?>
                        <?= $this->Form->postLink(
                            '<i class="fa fa-trash" aria-hidden="true"></i>',
                            ['action' => 'delete', $fornecedor->cnpj],
                            [
                                'method' => 'post',
                                'confirm' => __('Você realmente deseja excluir o fornecedor com CNPJ {0}?', $fornecedor->cnpj),
                                'escape' => false,
                                'title' => 'Excluir',
                                'onmouseover' => "this.style.color='#d33c43'",
                                'onmouseout' => "this.style.color='#606c76'",
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