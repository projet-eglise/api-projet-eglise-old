<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Errors Model
 *
 * @method \App\Model\Entity\Error newEmptyEntity()
 * @method \App\Model\Entity\Error newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Error[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Error get($primaryKey, $options = [])
 * @method \App\Model\Entity\Error findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Error patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Error[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Error|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Error saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Error[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Error[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Error[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Error[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ErrorsTable extends Table
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

        $this->setTable('errors');
        $this->setDisplayField('error_id');
        $this->setPrimaryKey('error_id');

        $this->hasMany('ErrorLogs', [
            'className' => 'ErrorLog',
            'foreignKey' => 'error_id',
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
            ->integer('error_id')
            ->allowEmptyString('error_id', null, 'create');

        $validator
            ->scalar('uid')
            ->maxLength('uid', 255)
            ->requirePresence('uid', 'create')
            ->notEmptyString('uid');

        $validator
            ->integer('code')
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

        $validator
            ->scalar('error')
            ->maxLength('error', 255)
            ->requirePresence('error', 'create')
            ->notEmptyString('error');

        $validator
            ->dateTime('created_at')
            ->notEmptyDateTime('created_at');

        $validator
            ->dateTime('updated_at')
            ->allowEmptyDateTime('updated_at');

        return $validator;
    }
}
