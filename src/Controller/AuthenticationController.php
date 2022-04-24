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
            throw new MethodNotAllowedException('Utilisez une requête POST');
        }

        $email = $this->request->getData('email');
        $password = $this->request->getData('password');

        if (is_null($email) || is_null($password)) {
            throw new BadRequestException('Email ou mot de passe vide');
        }

        $user = $this->UsersTable->findByEmail($email)->toArray();
        if (count($user) !== 1) {
            throw new NotFoundException('Identifiants invalide');
        }

        $user = $user[0];

        if (!$this->Authentication->password_verify($password, $user->password)) {
            throw new UnauthorizedException('Identifiants invalide');
        }

        header("WWW-Authenticate: " . $this->Authentication->generateJwt());

        return $this->apiResponse();
    }

    public function signin()
    {
        return $this->apiResponse();
    }
}
