<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\Component\AuthenticationComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\Constraint\Response\BodyEquals;
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
        $this->Churches = $this->getTableLocator()->get('Churches');
    }

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Users',
        'app.Addresses',
        'app.Churches',
        'app.ChurchUsers',
    ];

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\ChurchesController::view()
     */
    public function testView(): void
    {
        $Authentication = new AuthenticationComponent(new ComponentRegistry());

        $user = $this->Users->findByEmail('timothe@hofmann.fr')->toArray();
        $user = $user[0];

        $token = $Authentication->generateJwt($user);
        $this->configRequest(['headers' => ['Authorization' => 'Bearer ' . $token]]);

        $this->get('/church/627041d90c74f');

        $this->assertResponseOk();

        $church = $this->Churches->findByUid('627041d90c74f')->toArray()[0];
        $church = $this->Churches->get($church->church_id, ['contain' => ['Pastor', 'MainAdministrator', 'Address']])->toArray();
        
        unset($church['church_id']);
        unset($church['address_id']);
        unset($church['pastor_id']);
        unset($church['main_administrator_id']);
        unset($church['created_at']);
        unset($church['updated_at']);
        unset($church['addres']['address_id']);
        unset($church['addres']['created_at']);
        unset($church['addres']['updated_at']);
        unset($church['main_administrator']['user_id']);
        unset($church['main_administrator']['created_at']);
        unset($church['main_administrator']['updated_at']);
        unset($church['pastor']['user_id']);
        unset($church['pastor']['created_at']);
        unset($church['pastor']['updated_at']);

        $this->assertEquals(
            json_encode(['code' => 200, "message" => "OK", "data" => ["church" => $church]], JSON_PRETTY_PRINT),
            json_encode(json_decode((new BodyEquals($this->_response))->toStringBody()), JSON_PRETTY_PRINT)
        );
    }

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
