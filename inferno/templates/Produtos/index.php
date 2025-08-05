<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Produto> $produtos
 */
?>
<div class="produtos index content">
    <?= $this->Html->link(__('Novo Produto'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Produtos') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('lote') ?></th>
                    <th><?= $this->Paginator->sort('categoria') ?></th>
                    <th><?= $this->Paginator->sort('fornecedor') ?></th>
                    <th><?= $this->Paginator->sort('nome') ?></th>
                    <th><?= $this->Paginator->sort('quantidade') ?></th>
                    <th><?= $this->Paginator->sort('valor') ?></th>
                    <th><?= $this->Paginator->sort('fabricacao') ?></th>
                    <th><?= $this->Paginator->sort('validade') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?= $this->Number->format($produto->lote) ?></td>
                    <td><?= h($produto->categoria) ?></td>
                    <td><?= h($produto->fornecedor) ?></td>
                    <td><?= h($produto->nome) ?></td>
                    <td><?= $produto->quantidade === null ? '' : $this->Number->format($produto->quantidade) ?></td>
                    <td><?= $this->Number->format($produto->valor) ?></td>
                    <td><?= h($produto->fabricacao) ?></td>
                    <td><?= h($produto->validade) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(
                            '<i class="fa fa-eye" aria-hidden="true"></i>', 
                            ['action' => 'view', $produto->lote], 
                            ['escape' => false, 'title' => 'Visualizar']
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fa fa-pencil-alt" aria-hidden="true"></i>', 
                            ['action' => 'edit', $produto->lote], 
                            ['escape' => false, 'title' => 'Editar']
                        ) ?>
                        <?= $this->Form->postLink(
                            '<i class="fa fa-trash" aria-hidden="true"></i>', 
                            ['action' => 'delete', $produto->lote], 
                            [
                                'method' => 'delete', 
                                'confirm' => __('Você realmente deseja excluir o produto registrado com o lote {0}?', $produto->lote), 
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