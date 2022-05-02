<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\ChurchesController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ChurchesController Test Case
 *
 * @uses \App\Controller\ChurchesController
 */
class ChurchesControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Users',
        'app.Churches',
        'app.ChurchUsers',
    ];

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\ChurchesController::add()
     */
    public function testAdd(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
