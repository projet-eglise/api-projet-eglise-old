<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PE34ResetPassword extends AbstractMigration
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
        $password_requests = $this->table('password_requests', ['id' => 'password_request_id']);
        $password_requests
            ->addColumn('uid', 'string')
            ->addColumn('token', 'string')
            ->addColumn('expiration', 'integer')
            ->addColumn('user_id', 'integer')
            ->addForeignKey('user_id', 'users', 'user_id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->addTimestamps()
            ->create();
    }
}
