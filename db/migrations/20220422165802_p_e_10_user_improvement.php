<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PE10UserImprovement extends AbstractMigration
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
        $users = $this->table('users');
        $users
            ->addColumn('uid', 'string', ['after' => 'user_id'])
            ->addColumn('firstname', 'string', ['after' => 'uid', 'default' => 'X'])
            ->addColumn('lastname', 'string', ['after' => 'firstname', 'default' => 'X'])
            ->addColumn('phone_number', 'string', ['after' => 'password', 'default' => '+00 0 00 00 00 00'])
            ->addColumn('birthdate', 'date', ['after' => 'phone_number', 'default' => '1900-01-01'])
            ->addColumn('has_profile_picture', 'boolean', ['after' => 'birthdate', 'default' => false])
            ->update();
    }
}
