<?php

declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LogsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LogsTable Test Case
 */
class LogsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LogsTable
     */
    protected $Logs;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Logs') ? [] : ['className' => LogsTable::class];
        $this->Logs = $this->getTableLocator()->get('Logs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Logs);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ErrorsTable::validationDefault()
     */
    public function testAddingACascadingLog(): void
    {
        $data = [
            'uid' => uniqid(),
            'ip_address' => '0.0.0.0',
            'method' => 'GET',
            'route' => '/logout',
            'params' => '[]',
            'response_code' => "401",
            'response' => '{"file":"webroot\/index.php","line":40,"function":"run","class":"Cake\\Http\\Server","type":"->"}]}',
            'start_timestamp' => 16541827308706,
            'end_timestamp' => 16541827308802,
            'error_log' => [
                'uid' => uniqid(),
                'file' => 'src/Middleware/AuthenticationMiddleware.php',
                'line' => '31',
                'viewed' => false,
                'traces' => [
                    [
                        'uid' => uniqid(),
                        'file' => 'webroot/index.php',
                        'line' => 40,
                        'class' => 'Cake\Http\Server',
                        'type' => '->',
                        'function' => 'run',
                    ],
                    [
                        'uid' => uniqid(),
                        'file' => 'vendor/cakephp/cakephp/src/Http/Server.php',
                        'line' => 90,
                        'class' => 'Cake\Http\Runner',
                        'type' => '->',
                        'function' => 'run',
                    ],
                    [
                        'uid' => uniqid(),
                        'file' => 'vendor/cakephp/cakephp/src/Http/Runner.php',
                        'line' => 58,
                        'class' => 'Cake\Http\Runner',
                        'type' => '->',
                        'function' => 'handle',
                    ],
                    [
                        'uid' => uniqid(),
                        'file' => 'vendor/cakephp/cakephp/src/Http/Runner.php',
                        'line' => 73,
                        'class' => 'Cake\Error\Middleware\ErrorHandlerMiddleware',
                        'type' => '->',
                        'function' => 'process',
                    ],
                ],
                'error' => [
                    'uid' => uniqid(),
                    'code' => '401',
                    'error' => 'Bla bla bla bla',
                ],
            ],
        ];
        $log = $this->Logs->newEntity($data, ['associated' => [
            'ErrorLogs',
            'ErrorLogs.Traces',
            'ErrorLogs.Errors',
        ]]);
        $log->user = $this->Logs->Users->get(2);


        $expectedLog = $this->Logs->save($log);
        $this->assertNotFalse($expectedLog, 'Error during SQL cascade backup.');

        $actualLog = $this->Logs->get($expectedLog->log_id);
        $this->assertTrue($expectedLog->equals($actualLog), 'The two logs are not equal.');

        $this->assertNotNull($actualLog->error_log_id, 'error_log_id unset.');
        $actualErrorLog = $this->Logs->ErrorLogs->get($expectedLog->error_log->error_log_id);
        $this->assertTrue($expectedLog->error_log->equals($actualErrorLog), 'The two error_logs are not equal.');

        $this->assertNotNull($actualErrorLog->error_id, 'error_id unset.');
        $actualError = $this->Logs->ErrorLogs->Errors->get($expectedLog->error_log->error->error_id);
        $this->assertTrue($expectedLog->error_log->error->equals($actualError), 'The two errors are not equal.');

        $actualTraces = $this->Logs->ErrorLogs->Traces->findByErrorLogId($expectedLog->error_log->error_log_id)->toArray();
        $this->assertTrue(count($actualTraces) === count($expectedLog->error_log->traces), 'There are not the same number of traces.');
        foreach ($actualTraces as $actualTrace) {
            $hasOneEqual = false;

            foreach ($expectedLog->error_log->traces as $expectedTrace)
                if ($expectedTrace->equals($actualTrace))
                    $hasOneEqual = true;

            $this->assertTrue($hasOneEqual, 'This trace has no equal.');
        }
    }
}
