<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChurchUserRoles Model
 *
 * @property \App\Model\Table\ChurchUsersTable&\Cake\ORM\Association\BelongsTo $ChurchUsers
 * @property \App\Model\Table\RolesTable&\Cake\ORM\Association\BelongsTo $Roles
 * @property \App\Model\Table\RoleOptionsTable&\Cake\ORM\Association\BelongsTo $RoleOptions
 *
 * @method \App\Model\Entity\ChurchUserRole newEmptyEntity()
 * @method \App\Model\Entity\ChurchUserRole newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ChurchUserRole[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ChurchUserRole get($primaryKey, $options = [])
 * @method \App\Model\Entity\ChurchUserRole findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ChurchUserRole patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ChurchUserRole[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ChurchUserRole|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ChurchUserRole saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ChurchUserRole[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ChurchUserRole[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ChurchUserRole[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ChurchUserRole[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ChurchUserRolesTable extends Table
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

        $this->setTable('church_user_roles');
        $this->setDisplayField('church_user_role_id');
        $this->setPrimaryKey('church_user_role_id');

        $this->belongsTo('ChurchUsers', [
            'foreignKey' => 'church_user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('RoleOptions', [
            'foreignKey' => 'role_option_id',
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
            ->integer('church_user_role_id')
            ->allowEmptyString('church_user_role_id', null, 'create');

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
        $rules->add($rules->existsIn('church_user_id', 'ChurchUsers'), ['errorField' => 'church_user_id']);
        $rules->add($rules->existsIn('role_id', 'Roles'), ['errorField' => 'role_id']);
        $rules->add($rules->existsIn('role_option_id', 'RoleOptions'), ['errorField' => 'role_option_id']);

        return $rules;
    }
}
