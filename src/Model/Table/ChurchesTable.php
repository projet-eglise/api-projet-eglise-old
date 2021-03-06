<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Churches Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Church newEmptyEntity()
 * @method \App\Model\Entity\Church newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Church[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Church get($primaryKey, $options = [])
 * @method \App\Model\Entity\Church findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Church patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Church[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Church|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Church saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Church[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Church[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Church[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Church[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ChurchesTable extends Table
{
    private ChurchUsersTable $ChurchUsers;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('churches');
        $this->setDisplayField('name');
        $this->setPrimaryKey('church_id');

        $this->belongsTo('Pastor', [
            'foreignKey' => 'pastor_id',
            'joinType' => 'INNER',
            'className' => 'Users',
        ]);

        $this->belongsTo('MainAdministrator', [
            'foreignKey' => 'main_administrator_id',
            'joinType' => 'INNER',
            'className' => 'Users',
        ]);

        $this->belongsToMany('Users', [
            'through' => 'ChurchUsers',
            'foreignKey' => 'church_id',
            'joinTable' => 'ChurchUsers',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Address', [
            'foreignKey' => 'address_id',
            'joinType' => 'INNER',
            'className' => 'Addresses',
        ]);

        $this->ChurchUsers = TableRegistry::getTableLocator()->get('ChurchUsers');
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
            ->integer('church_id')
            ->allowEmptyString('church_id', null, 'create');

        $validator
            ->scalar('uid')
            ->maxLength('uid', 255)
            ->requirePresence('uid', 'create')
            ->notEmptyString('uid');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name', "Nom de l'Eglise incorrect");

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
        return $rules;
    }

    /**
     * Returns the churches that a user can join.
     *
     * @param User $user
     * @return array
     */
    public function getJoinable(User $user): array
    {
        $myChurches = $this->ChurchUsers->find('list', [
            'keyField' => 'church_id',
            'valueField' => 'church_id',
            'conditions' => ['user_id' => $user->user_id],
        ])->toArray();

        return $this
            ->find('all', [
                'fields' => [
                    'uid',
                    'name',
                    'address__address' => 'address',
                    'address__address2' => 'CONCAT(postal_code, \' \', city)',
                    'address__city' => 'city',
                    'pastor__name' => 'CONCAT(firstname, \' \', UPPER(lastname))',
                ],
                'contain' => [
                    'Address', 'Pastor'
                ]
            ])
            ->where(count($myChurches) > 0 ? [
                "church_id NOT IN (" . implode(", ", $myChurches) . ")"
            ] : [])
            ->toArray();
    }
}
