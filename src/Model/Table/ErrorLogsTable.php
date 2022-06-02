<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ErrorLogs Model
 *
 * @property \App\Model\Table\ErrorsTable&\Cake\ORM\Association\BelongsTo $Errors
 *
 * @method \App\Model\Entity\ErrorLog newEmptyEntity()
 * @method \App\Model\Entity\ErrorLog newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ErrorLog[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ErrorLog get($primaryKey, $options = [])
 * @method \App\Model\Entity\ErrorLog findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ErrorLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ErrorLog[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ErrorLog|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ErrorLog saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ErrorLog[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ErrorLog[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ErrorLog[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ErrorLog[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ErrorLogsTable extends Table
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

        $this->setTable('error_logs');
        $this->setDisplayField('error_log_id');
        $this->setPrimaryKey('error_log_id');

        $this->belongsTo('Errors', [
            'foreignKey' => 'error_id',
        ]);

        $this->hasMany('Traces', [
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
            ->integer('error_log_id')
            ->allowEmptyString('error_log_id', null, 'create');

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
            ->boolean('viewed')
            ->notEmptyString('viewed');

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
        // $rules->add($rules->existsIn('error_id', 'Errors'), ['errorField' => 'error_id']);

        return $rules;
    }
}
