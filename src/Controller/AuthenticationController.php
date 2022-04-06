<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\UsersTable;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;

class AuthenticationController extends AppController
{
    private UsersTable $UsersTable;

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Authentication');

        $this->UsersTable = TableRegistry::getTableLocator()->get('Users');
    }

    public function login()
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException('Use POST method.');
        }

        $email = $this->request->getData('email');
        $password = $this->request->getData('password');

        if (is_null($email) || is_null($password)) {
            throw new BadRequestException('Email or password empty.');
        }

        $user = $this->UsersTable->findByEmail($email)->toArray();
        if(count($user) !== 1) {
            throw new NotFoundException('Invalid email.');
        }

        $user = $user[0];

        if(!$this->Authentication->password_verify($password, $user->password)) {
            throw new UnauthorizedException('Bad password.');
        }

        return $this->apiResponse(200, ['token' => $this->Authentication->generateJwt()]);
    }
}
