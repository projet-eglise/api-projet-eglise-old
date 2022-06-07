<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\Component\AuthenticationComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\Constraint\Response\BodyEquals;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\AuthenticationController Test Case
 *
 * @uses \App\Controller\AuthenticationController
 */
class AuthenticationControllerTest extends TestCase
{
    use IntegrationTestTrait;

    protected $fixtures = ['app.Users', 'app.Addresses', 'app.Churches', 'app.ChurchUsers'];

    public function setUp(): void
    {
        parent::setUp();
        $this->Users = $this->getTableLocator()->get('Users');
        $this->ChurchUsers = $this->getTableLocator()->get('ChurchUsers');
    }

    /**
     * Test login success method
     *
     * @return void
     * @uses \App\Controller\AuthenticationController::login()
     */
    public function testLoginSuccess()
    {
        $this->post('/login', [
            'email' => 'timothe@hofmann.fr',
            'password' => 'rootAze'
        ]);

        $this->assertResponseOk();
    }

    /**
     * Test login error method
     *
     * @return void
     * @uses \App\Controller\AuthenticationController::login()
     */
    public function testLoginError()
    {
        $this->post('/login', [
            'email' => 'timothe@hofmann.fr',
            'password' => 'root'
        ]);

        $this->assertResponseError();
    }

    /** Test token content */
    public function testTokenData()
    {
        $Authentication = new AuthenticationComponent(new ComponentRegistry());

        $user = $this->Users->findByEmail('timothe@hofmann.fr')->toArray();
        $user = $user[0];

        $token = $Authentication->getTokenContent($Authentication->generateJwt($user));
        unset($token['exp']);

        $this->assertEquals('6265545515f21', $token['user']['uid']);
        $this->assertEquals("Timothé", $token['user']['firstname']);
        $this->assertEquals("HOFMANN", $token['user']['lastname']);
        $this->assertTrue($token['user']['is_admin']);
        $this->assertEquals(5, count($token['user']));

        $this->assertEquals(2, count($token['churches']));
        foreach ($token['churches'] as $church) {
            $hasOneEqual = false;

            if ($church["uid"] === "627041d90c74f" && $church["name"] === "ADD Dijon" || $church["uid"] === "627041d90c752" && $church["name"] === "ADD Autun")
                $hasOneEqual = true;

            $this->assertTrue($hasOneEqual);
            $this->assertEquals(4, count($church));
            $this->assertFalse($church['hasAtLeastOneRole']);
            $this->assertFalse($church['hasAtLeastOneRoleValidate']);
        }
    }

    /**
     * Test signin method
     *
     * @return void
     * @uses \App\Controller\AuthenticationController::signin()
     */
    public function testSignin()
    {
        $this->post('/signin', [
            'firstname' => 'Timothé',
            'lastname' => 'HOFMANN',
            'email' => 'timothe@projet-eglise.fr',
            'password' => 'Mypass4ever!',
            'birthdate' => '1900-01-01',
            'phone_number' => '+33 7 11 11 11 11',
        ]);

        $this->assertResponseOk();

        $user = $this->Users->findByEmail('timothe@projet-eglise.fr')->toArray();
        $this->assertEquals(1, count($user));

        $user = $user[0];

        $this->assertEquals(json_encode([
            'user_id' => $user->user_id,
            'uid' => $user->uid,
            'is_admin' => false,
            'firstname' => 'Timothé',
            'lastname' => 'HOFMANN',
            'email' => 'timothe@projet-eglise.fr',
            'phone_number' => '+33 7 11 11 11 11',
            'birthdate' => '1900-01-01',
            'has_profile_picture' => false,
            'profile_image_link' => null,
            'created_at' => $user->created_at,
            'updated_at' => null
        ]), json_encode($user));
    }

    /**
     * Test signin method
     *
     * @return void
     * @uses \App\Controller\AuthenticationController::signin()
     */
    public function testWhoami()
    {
        $Authentication = new AuthenticationComponent(new ComponentRegistry());

        $this->ChurchUsers->delete($this->ChurchUsers->get(4));
        $user = $this->Users->findByEmail('timothe@hofmann.fr')->first();

        $token = $Authentication->generateJwt($user);
        $this->configRequest(['headers' => ['Authorization' => 'Bearer ' . $token]]);

        $this->get('/whoami');

        $this->assertResponseOk();

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertEquals(200, $response['code']);
        $this->assertEquals("OK", $response['message']);

        $this->assertEquals("6265545515f21", $response['data']['user']['uid']);
        $this->assertEquals("Timothé", $response['data']['user']['firstname']);
        $this->assertEquals("HOFMANN", $response['data']['user']['lastname']);
        $this->assertTrue($response['data']['user']['is_admin']);
        $this->assertEquals(4, count($response['data']['user']));

        $this->assertEquals(1, count($response['data']['churches']));
        $this->assertEquals('627041d90c74f', $response['data']['churches'][0]['uid']);
        $this->assertEquals('ADD Dijon', $response['data']['churches'][0]['name']);
        $this->assertEquals(5, count($response['data']['churches'][0]));
        $this->assertFalse($response['data']['churches'][0]['hasAtLeastOneRole']);
        $this->assertFalse($response['data']['churches'][0]['hasAtLeastOneRoleValidate']);
    }
}
