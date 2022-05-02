<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\AuthenticationController;
use App\Controller\Component\AuthenticationComponent;
use Cake\Controller\ComponentRegistry;
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

    protected $fixtures = ['app.Users', 'app.Churches', 'app.ChurchUsers'];

    public function setUp(): void
    {
        parent::setUp();
        $this->Users = $this->getTableLocator()->get('Users');
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
}
