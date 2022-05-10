<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Services Model
 *
 * @method \App\Model\Entity\Service newEmptyEntity()
 * @method \App\Model\Entity\Service newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Service[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Service get($primaryKey, $options = [])
 * @method \App\Model\Entity\Service findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Service patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Service[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Service|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Service saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Service[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Service[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Service[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Service[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ServicesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('services');
        $this->setDisplayField('name');
        $this->setPrimaryKey('service_id');
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
            ->integer('service_id')
            ->allowEmptyString('service_id', null, 'create');

        $validator
            ->scalar('uid')
            ->maxLength('uid', 255)
            ->requirePresence('uid', 'create')
            ->notEmptyString('uid');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->dateTime('created_at')
            ->notEmptyDateTime('created_at');

        $validator
            ->dateTime('updated_at')
            ->allowEmptyDateTime('updated_at');

        return $validator;
    }
}
