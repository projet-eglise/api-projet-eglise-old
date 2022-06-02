<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Traces Model
 *
 * @property \App\Model\Table\ErrorLogsTable&\Cake\ORM\Association\BelongsTo $ErrorLogs
 *
 * @method \App\Model\Entity\Trace newEmptyEntity()
 * @method \App\Model\Entity\Trace newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Trace[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Trace get($primaryKey, $options = [])
 * @method \App\Model\Entity\Trace findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Trace patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Trace[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Trace|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Trace saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Trace[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Trace[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Trace[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Trace[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class TracesTable extends Table
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

        $this->setTable('traces');
        $this->setDisplayField('trace_id');
        $this->setPrimaryKey('trace_id');

        $this->belongsTo('ErrorLogs', [
            'foreignKey' => 'error_log_id',
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
            ->integer('trace_id')
            ->allowEmptyString('trace_id', null, 'create');

        $validator
            ->scalar('uid')
            ->maxLength('uid', 255)
            ->requirePresence('uid', 'create')
            ->notEmptyString('uid');

        $validator
            ->scalar('file')
            ->maxLength('file', 255)
            ->requirePresence('file', 'create')
            ->notEmptyFile('file');

        $validator
            ->integer('line')
            ->requirePresence('line', 'create')
            ->notEmptyString('line');

        $validator
            ->scalar('class')
            ->maxLength('class', 255)
            ->requirePresence('class', 'create')
            ->notEmptyString('class');

        $validator
            ->scalar('type')
            ->maxLength('type', 255)
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->scalar('function')
            ->maxLength('function', 255)
            ->requirePresence('function', 'create')
            ->notEmptyString('function');

        $validator
            ->dateTime('created_at')
            ->notEmptyDateTime('created_at');

        $validator
            ->dateTime('updated_at')
            ->allowEmptyDateTime('updated_at');

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
        $rules->add($rules->existsIn('error_log_id', 'ErrorLogs'), ['errorField' => 'error_log_id']);

        return $rules;
    }
}
