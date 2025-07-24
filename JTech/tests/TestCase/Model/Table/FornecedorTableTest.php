<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FornecedorTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FornecedorTable Test Case
 */
class FornecedorTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FornecedorTable
     */
    protected $Fornecedor;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Fornecedor',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Fornecedor') ? [] : ['className' => FornecedorTable::class];
        $this->Fornecedor = $this->getTableLocator()->get('Fornecedor', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Fornecedor);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\FornecedorTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\FornecedorTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
