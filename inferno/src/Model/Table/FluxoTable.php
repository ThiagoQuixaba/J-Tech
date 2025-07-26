<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Fluxo Model
 *
 * @method \App\Model\Entity\Fluxo newEmptyEntity()
 * @method \App\Model\Entity\Fluxo newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Fluxo> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Fluxo get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Fluxo findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Fluxo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Fluxo> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Fluxo|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Fluxo saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Fluxo>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fluxo>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Fluxo>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fluxo> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Fluxo>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fluxo>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Fluxo>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fluxo> deleteManyOrFail(iterable $entities, array $options = [])
 */
class FluxoTable extends Table
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

        $this->setTable('fluxo');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Produtos', [
            'foreignKey' => 'lote',
            'bindingKey' => 'lote',
            'joinType' => 'INNER',
        ]);

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
            ->requirePresence('lote', 'create')
            ->notEmptyString('lote');

        $validator
            ->scalar('tipo')
            ->maxLength('tipo', 255)
            ->allowEmptyString('tipo');

        $validator
            ->date('data')
            ->allowEmptyDate('data');

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
        $rules->add($rules->isUnique(['id']), ['errorField' => 'id']);

        return $rules;
    }
}
