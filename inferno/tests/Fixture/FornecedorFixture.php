<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FornecedorFixture
 */
class FornecedorFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'fornecedor';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'cnpj' => 'd65d25f5-0047-4d4d-97a9-66316e314a33',
                'nome' => 'Lorem ipsum dolor sit amet',
                'contato' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
