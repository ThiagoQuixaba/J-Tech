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
                    <th><?= $this->Paginator->sort('cnpj') ?></th>
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
                        <?= $this->Html->link(__('View'), ['action' => 'view', $fornecedor->cnpj]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $fornecedor->cnpj]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $fornecedor->cnpj], ['method' => 'delete', 'confirm' => __('Você realmente deseja excluir o fornecedor registrado com o CNPJ {0}?', $fornecedor->cnpj), 'onmouseover' => "this.style.color='#d33c43'", 'onmouseout' => "this.style.color='#606c76'"]) ?>
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
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>