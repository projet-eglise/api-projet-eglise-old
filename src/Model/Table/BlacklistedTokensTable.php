<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * BlacklistedTokens Model
 *
 * @method \App\Model\Entity\BlacklistedToken newEmptyEntity()
 * @method \App\Model\Entity\BlacklistedToken newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\BlacklistedToken[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\BlacklistedToken get($primaryKey, $options = [])
 * @method \App\Model\Entity\BlacklistedToken findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\BlacklistedToken patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\BlacklistedToken[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\BlacklistedToken|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BlacklistedToken saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BlacklistedToken[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\BlacklistedToken[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\BlacklistedToken[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\BlacklistedToken[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class BlacklistedTokensTable extends Table
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

        $this->setTable('blacklisted_tokens');
        $this->setDisplayField('blacklisted_token_id');
        $this->setPrimaryKey('blacklisted_token_id');

        $this->clear();
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
            ->integer('blacklisted_token_id')
            ->allowEmptyString('blacklisted_token_id', null, 'create');

        $validator
            ->scalar('token')
            ->requirePresence('token', 'create')
            ->notEmptyString('token');

        $validator
            ->integer('expiration')
            ->requirePresence('expiration', 'create')
            ->notEmptyString('expiration');

        $validator
            ->dateTime('created_at')
            ->notEmptyDateTime('created_at');

        $validator
            ->dateTime('updated_at')
            ->allowEmptyDateTime('updated_at');

        return $validator;
    }

    public function clear()
    {
        $expiredToken = $this->find('all', [
            'conditions' => ['expiration < ' => time()]
        ]);

        foreach ($expiredToken as $token) {
            $this->delete($token);
        }
    }
}
