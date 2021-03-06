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

        $this->assertEquals(json_encode([
            "user" => [
                "uid" => '6265545515f21',
                "is_admin" => true
            ],
            "churches" => [
                [
                    "uid" => "627041d90c74f",
                    "name" => "ADD Dijon",
                ],
                [
                    "uid" => "627041d90c752",
                    "name" => "ADD Autun",
                ],
            ]
        ]), json_encode($token));
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
            'firstname' => 'Timoth??',
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
            'firstname' => 'Timoth??',
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

        $user = $this->Users->findByEmail('timothe@hofmann.fr')->toArray();
        $this->ChurchUsers->delete($this->ChurchUsers->get(4));
        $user = $this->Users->get($user[0]->user_id);

        $token = $Authentication->generateJwt($user);
        $this->configRequest(['headers' => ['Authorization' => 'Bearer ' . $token]]);

        $this->get('/whoami');

        $this->assertResponseOk();

        $this->assertEquals(
            json_encode(['code' => 200, "message" => "OK", "data" =>
            [
                "user" =>
                [
                    "uid" => "6265545515f21",
                    "is_admin" => true
                ],
                "churches" => [
                    ["uid" => "627041d90c74f", 'name' => 'ADD Dijon']
                ]
            ]], JSON_PRETTY_PRINT),
            json_encode(json_decode((new BodyEquals($this->_response))->toStringBody()), JSON_PRETTY_PRINT)
        );
    }
}
