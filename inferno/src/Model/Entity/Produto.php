<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Produto Entity
 *
 * @property int $lote
 * @property string|null $categoria
 * @property string|null $fornecedor
 * @property string $nome
 * @property string|null $descricao
 * @property int|null $quantidade
 * @property float $valor
 * @property \Cake\I18n\Date $fabricacao
 * @property \Cake\I18n\Date $validade
 */
class Produto extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'categoria' => true,
        'fornecedor' => true,
        'nome' => true,
        'descricao' => true,
        'quantidade' => true,
        'valor' => true,
        'fabricacao' => true,
        'validade' => true,
    ];
}
