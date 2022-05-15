<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\UsersTable;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use DateTime;

class AuthenticationController extends AppController
{
    private UsersTable $Users;

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Authentication');
        $this->loadComponent('File');

        $this->Users = TableRegistry::getTableLocator()->get('Users');
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

        $user = $this->Users->findByEmail($email)->first();
        if ($user === null) {
            throw new NotFoundException('Identifiants invalides');
        }

        if (!$this->Authentication->passwordVerify($password, $user->password)) {
            throw new UnauthorizedException('Identifiants invalides');
        }

        return $this->apiResponse(['token' => $this->Authentication->generateJwt($user)]);
    }

    public function signin()
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException('Utilisez une requête POST');
        }

        $user = $this->Users->newEntity($this->request->getData());
        $user->uid = uniqid();
        $user->password = $this->Authentication->hashPassword($this->request->getData('password'));

        $image = $this->request->getData('profile_image');
        if ($image !== null)
            $user->addProfilePicture($image);


        if (!$this->Users->save($user)) {
            $errors = $user->getErrors();
            if (empty($errors))
                throw new BadRequestException('Une erreur est survenue lors de l\'enregistrement.');

            $field = reset($errors);
            throw new BadRequestException(reset($field));
        }

        return $this->apiResponse(['token' => $this->Authentication->generateJwt($user)]);
    }

    /**
     * Sends the user account information.
     */
    public function whoami()
    {
        if (!$this->request->is('get')) {
            throw new MethodNotAllowedException('Utilisez une requête GET');
        }

        $token = $this->Authentication->getTokenContent($this->request->getSession()->read('token'));
        $user = $this->Users->findByUid($token['user']['uid'])->first();
        $data = $this->Authentication->generageTokenContent($user);
        unset($data['exp']);

        return $this->apiResponse($data);
    }
}
