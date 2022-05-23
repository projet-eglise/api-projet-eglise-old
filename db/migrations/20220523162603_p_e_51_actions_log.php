<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PE51ActionsLog extends AbstractMigration
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
        $logs = $this->table('logs', ['id' => 'log_id']);
        $logs
            ->addColumn('uid', 'string')
            ->addColumn('user_id', 'integer', ['default' => null, 'null' => true])
            ->addColumn('ip_address', 'string')
            ->addColumn('method', 'string')
            ->addColumn('route', 'string')
            ->addColumn('params', 'text')
            ->addColumn('response_code', 'integer')
            ->addColumn('response', 'text', ['default' => null, 'null' => true])
            ->addColumn('file', 'string', ['default' => null, 'null' => true])
            ->addColumn('trace', 'text', ['default' => null, 'null' => true])
            ->addColumn('start_timestamp', 'string', ['default' => null, 'null' => true])
            ->addColumn('end_timestamp', 'string', ['default' => null, 'null' => true])
            ->addColumn('viewed', 'boolean', ['default' => null, 'null' => true])
            ->addForeignKey('user_id', 'users', 'user_id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->addTimestamps()
            ->create();
    }
}
