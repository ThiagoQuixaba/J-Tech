<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Fornecedor Model
 *
 * @method \App\Model\Entity\Fornecedor newEmptyEntity()
 * @method \App\Model\Entity\Fornecedor newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Fornecedor> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Fornecedor get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Fornecedor findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Fornecedor patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Fornecedor> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Fornecedor|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Fornecedor saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Fornecedor>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fornecedor>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Fornecedor>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fornecedor> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Fornecedor>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fornecedor>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Fornecedor>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fornecedor> deleteManyOrFail(iterable $entities, array $options = [])
 */
class FornecedorTable extends Table
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

        $this->setTable('fornecedor');
        $this->setDisplayField('cnpj');
        $this->setPrimaryKey('cnpj');
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
            ->scalar('nome')
            ->notEmptyString('nome')
            ->add('nome', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('contato')
            ->allowEmptyString('contato');

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
        $rules->add($rules->isUnique(['nome']), ['errorField' => 'nome']);

        return $rules;
    }
}
