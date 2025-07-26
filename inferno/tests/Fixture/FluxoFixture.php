<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FluxoFixture
 */
class FluxoFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'fluxo';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'lote' => 1,
                'tipo' => 'Lorem ipsum dolor sit amet',
                'data' => '2025-07-26',
            ],
        ];
        parent::init();
    }
}
