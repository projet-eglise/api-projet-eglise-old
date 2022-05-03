<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PE11NotionOfChurch extends AbstractMigration
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
        $churches = $this->table('churches', ['id' => 'church_id']);
        $churches
            ->addColumn('uid', 'string')
            ->addColumn('name', 'string')
            ->addColumn('pastor_id', 'integer')
            ->addColumn('main_administrator_id', 'integer')
            ->addTimestamps()
            ->addForeignKey('pastor_id', 'users', 'user_id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->addForeignKey('main_administrator_id', 'users', 'user_id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->create();

        $church_users = $this->table('church_users', ['id' => 'church_user_id']);
        $church_users
            ->addColumn('user_id', 'integer')
            ->addColumn('church_id', 'integer')
            ->addTimestamps()
            ->addForeignKey('user_id', 'users', 'user_id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->addForeignKey('church_id', 'churches', 'church_id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->create();

        $users = $this->table('users');
        $users
            ->addColumn('is_admin', 'boolean', ['after' => 'uid', 'default' => false])
            ->update();
    }
}
