<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PE29Address extends AbstractMigration
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
        $addresses = $this->table('addresses', ['id' => 'address_id']);
        $addresses
            ->addColumn('uid', 'string')
            ->addColumn('address', 'string')
            ->addColumn('postal_code', 'string')
            ->addColumn('city', 'string')
            ->addTimestamps()
            ->create();

        $churches = $this->table('churches');
        $churches
            ->addColumn('address_id', 'integer', ['after' => 'main_administrator_id'])
            ->addForeignKey('address_id', 'addresses', 'address_id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->update();
    }
}
