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
                    <td class="Ações">
                        <?= $this->Html->link(__('Visualizar'), ['action' => 'view', $categorium->nome]) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $categorium->nome]) ?>
                        <?= $this->Form->postLink(
                            __('Deletar'),
                            ['action' => 'delete', $categorium->nome],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $categorium->nome),
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- ✅ Botão Voltar para Home amarelo -->
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
