<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @method \App\Model\Entity\User newEmptyEntity()
 * @method \App\Model\Entity\User newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('user_id');
        $this->setPrimaryKey('user_id');

        $this->belongsToMany('Churches', [
            'through' => 'ChurchUsers',
            'foreignKey' => 'user_id',
            'joinTable' => 'ChurchUsers',
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
            ->integer('user_id', "L'identifiant de l'utilisateur doit être un nombre")
            ->allowEmptyString('user_id', null, 'create');

        $validator
            ->scalar('uid')
            ->maxLength('uid', 255)
            ->requirePresence('uid', 'create')
            ->notEmptyString('uid', "L'uid ne peut pas être vide");

        $validator
            ->scalar('firstname')
            ->maxLength('firstname', 255)
            ->notEmptyString('firstname', "Le prénom ne peut pas être vide");

        $validator
            ->scalar('lastname')
            ->maxLength('lastname', 255)
            ->notEmptyString('lastname', "Le nom ne peut pas être vide");

        $validator
            ->email('email', false, "L'addresse mail ne convient pas")
            ->requirePresence('email', 'create')
            ->notEmptyString('email', "Le mail ne peut pas être vide");

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->notEmptyString('password', "Le mot de passe ne peut pas être vide");

        $validator
            ->scalar('phone_number')
            ->regex('phone_number', '^\+([0-9]{2,3}) ([0-9 ]{9,})^', "Le numéro de téléphone ne correspond pas")
            ->maxLength('phone_number', 255)
            ->notEmptyString('phone_number', "Le numéro de téléphone ne peut pas être vide");

        $validator
            ->date('birthdate', ['ymd'], "Date d'anniversaire non confome")
            ->notEmptyDate('birthdate', "La date d'anniversaire ne peut pas être vide");

        $validator
            ->boolean('has_profile_picture')
            ->notEmptyFile('has_profile_picture');

        $validator
            ->url('profile_image_link')
            ->scalar('profile_image_link')
            ->maxLength('profile_image_link', 255)
            ->allowEmptyFile('profile_image_link');

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
        $rules->add($rules->isUnique(['email']), ['errorField' => 'email', 'message' => "Cette adresse mail existe déjà dans la base de données"]);

        return $rules;
    }
}
