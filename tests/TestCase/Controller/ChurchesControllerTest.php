<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\Component\AuthenticationComponent;
use Cake\Controller\ComponentRegistry;
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

    public function setUp(): void
    {
        parent::setUp();
        $this->Users = $this->getTableLocator()->get('Users');
    }

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
        $Authentication = new AuthenticationComponent(new ComponentRegistry());

        $user = $this->Users->findByEmail('timothe@hofmann.fr')->toArray();
        $user = $user[0];

        $token = $Authentication->generateJwt($user);
        $this->configRequest(['headers' => ['Authorization' => 'Bearer ' . $token]]);

        $this->post('/church/add', [
            'pastor_firstname' => 'Firstname',
            'pastor_lastname' => 'Lastname',
            'pastor_email' => 'pastor@church.fr',
            'church_name' => 'Eglise de test',
            'church_address' => '1 rue du Paradis',
            'church_postal_code' => '00000',
            'church_city' => 'Le Royaume de Dieu',
        ]);

        $this->assertResponseOk();
    }
}
