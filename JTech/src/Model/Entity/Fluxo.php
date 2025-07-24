<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Fluxo Entity
 *
 * @property int $id
 * @property int $lote
 * @property string|null $tipo
 * @property \Cake\I18n\Date|null $data
 */
class Fluxo extends Entity
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
        'lote' => true,
        'tipo' => true,
        'data' => true,
    ];
}
