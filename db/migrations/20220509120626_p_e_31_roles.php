<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PE31Roles extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $permissions = $this->table('permissions', ['id' => 'permission_id']);
        $permissions
            ->addColumn('uid', 'string')
            ->addColumn('name', 'string')
            ->addTimestamps()
            ->create();

        $services = $this->table('services', ['id' => 'service_id']);
        $services
            ->addColumn('uid', 'string')
            ->addColumn('name', 'string')
            ->addTimestamps()
            ->create();

        $roles = $this->table('roles', ['id' => 'role_id']);
        $roles
            ->addColumn('uid', 'string')
            ->addColumn('name', 'string')
            ->addColumn('permission_id', 'integer', ["null" => false])
            ->addColumn('service_id', 'integer', ["null" => true, "default" => null])
            ->addTimestamps()
            ->addForeignKey('permission_id', 'permissions', 'permission_id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->addForeignKey('service_id', 'services', 'service_id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->create();

        $role_options = $this->table('role_options', ['id' => 'role_option_id']);
        $role_options
            ->addColumn('uid', 'string')
            ->addColumn('name', 'string')
            ->addColumn('role_id', 'integer')
            ->addTimestamps()
            ->addForeignKey('role_id', 'roles', 'role_id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->create();

        $church_user_roles = $this->table('church_user_roles', ['id' => 'church_user_role_id']);
        $church_user_roles
            ->addColumn('uid', 'string')
            ->addColumn('church_user_id', 'integer')
            ->addColumn('role_id', 'integer')
            ->addColumn('role_option_id', 'integer', ["null" => true, "default" => null])
            ->addTimestamps()
            ->addForeignKey('church_user_id', 'church_users', 'church_user_id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->addForeignKey('role_id', 'roles', 'role_id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->addForeignKey('role_option_id', 'role_options', 'role_option_id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->create();
    }
}
