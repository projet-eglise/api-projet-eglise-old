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
        $errors = $this->table('errors', ['id' => 'error_id']);
        $errors
            ->addColumn('uid', 'string')
            ->addColumn('code', 'integer')
            ->addColumn('error', 'string')
            ->addTimestamps()
            ->create();

        $error_logs = $this->table('error_logs', ['id' => 'error_log_id']);
        $error_logs
            ->addColumn('uid', 'string')
            ->addColumn('error_id', 'integer', ['default' => null, 'null' => true])
            ->addColumn('file', 'string')
            ->addColumn('line', 'integer')
            ->addColumn('viewed', 'boolean', ['default' => false])
            ->addForeignKey('error_id', 'errors', 'error_id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->addTimestamps()
            ->create();

        $logs = $this->table('logs', ['id' => 'log_id']);
        $logs
            ->addColumn('uid', 'string')
            ->addColumn('user_id', 'integer', ['default' => null, 'null' => true])
            ->addColumn('error_log_id', 'integer', ['default' => null, 'null' => true])
            ->addColumn('ip_address', 'string')
            ->addColumn('method', 'string')
            ->addColumn('route', 'string')
            ->addColumn('params', 'text')
            ->addColumn('response_code', 'integer')
            ->addColumn('response', 'text', ['default' => null, 'null' => true])
            ->addColumn('start_timestamp', 'biginteger')
            ->addColumn('end_timestamp', 'biginteger')
            ->addForeignKey('user_id', 'users', 'user_id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('error_log_id', 'error_logs', 'error_log_id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addTimestamps()
            ->create();

        $traces = $this->table('traces', ['id' => 'trace_id']);
        $traces
            ->addColumn('uid', 'string')
            ->addColumn('error_log_id', 'integer', ['default' => null, 'null' => true])
            ->addColumn('file', 'string')
            ->addColumn('line', 'integer')
            ->addColumn('class', 'string')
            ->addColumn('type', 'string')
            ->addColumn('function', 'string')
            ->addForeignKey('error_log_id', 'error_logs', 'error_log_id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addTimestamps()
            ->create();
    }
}
