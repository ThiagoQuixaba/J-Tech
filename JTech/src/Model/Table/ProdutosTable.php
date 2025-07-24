<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Produtos Model
 *
 * @method \App\Model\Entity\Produto newEmptyEntity()
 * @method \App\Model\Entity\Produto newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Produto> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Produto get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Produto findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Produto patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Produto> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Produto|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Produto saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Produto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Produto>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Produto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Produto> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Produto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Produto>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Produto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Produto> deleteManyOrFail(iterable $entities, array $options = [])
 */
class ProdutosTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('produtos');
        $this->setDisplayField('lote');
        $this->setPrimaryKey('lote');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('categoria')
            ->allowEmptyString('categoria');

        $validator
            ->scalar('fornecedor')
            ->allowEmptyString('fornecedor');

        $validator
            ->scalar('nome')
            ->notEmptyString('nome');

        $validator
            ->scalar('descricao')
            ->allowEmptyString('descricao');

        $validator
            ->allowEmptyString('quantidade');

        $validator
            ->numeric('valor')
            ->requirePresence('valor', 'create')
            ->notEmptyString('valor');

        $validator
            ->date('fabricacao')
            ->requirePresence('fabricacao', 'create')
            ->notEmptyDate('fabricacao');

        $validator
            ->date('validade')
            ->requirePresence('validade', 'create')
            ->notEmptyDate('validade');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['lote']), ['errorField' => 'lote']);

        return $rules;
    }
}
