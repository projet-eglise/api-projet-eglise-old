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

        header("WWW-Authenticate: " . $this->Authentication->generateJwt($user));

        return $this->apiResponse();
    }

    public function signin()
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException('Utilisez une requête POST');
        }

        if (is_null($this->request->getData('firstname'))) {
            throw new BadRequestException('Prénom non renseignée.');
        }

        if ($this->request->getData('firstname') === '') {
            throw new BadRequestException('Prénom vide.');
        }

        if (is_null($this->request->getData('lastname'))) {
            throw new BadRequestException('Nom non renseignée.');
        }

        if ($this->request->getData('lastname') === '') {
            throw new BadRequestException('Nom vide.');
        }

        if (is_null($this->request->getData('email'))) {
            throw new BadRequestException('Adresse mail non renseignée.');
        }

        if (!filter_var($this->request->getData('email'), FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestException('Adresse mail invalide.');
        }

        if (is_null($this->request->getData('password'))) {
            throw new BadRequestException('Mot de passe non renseignée.');
        }

        if(!preg_match('^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$^', $this->request->getData('password'))) {
            throw new BadRequestException('Mot de passe non conforme.');
        }


        if ($this->request->getData('lastname') === '') {
            throw new BadRequestException('Nom vide.');
        }


        if (is_null($this->request->getData('birthdate'))) {
            throw new BadRequestException('Date d\'anniversaire non renseignée.');
        }

        $d = DateTime::createFromFormat('Y-m-d', $this->request->getData('birthdate'));
        if (!($d && $d->format('Y-m-d') === $this->request->getData('birthdate'))) {
            throw new BadRequestException('Anniversaire invalide.');
        }

        if (is_null($this->request->getData('phone_number'))) {
            throw new BadRequestException('Numéro de téléphone non renseignée.');
        }

        if(!preg_match('^\+([0-9]{2,3}) ([0-9 ]{9,})^', $this->request->getData('phone_number'))) {
            throw new BadRequestException('Numéro de téléphone non conforme.');
        }

        $user = $this->UsersTable->newEntity($this->request->getData());
        $user->uid = uniqid();
        $user->password = $this->Authentication->hashPassword($this->request->getData('password'));

        if (!empty($user->getErrors())) {
            throw new BadRequestException('Une erreur est survenue.');
        }

        if(!$this->UsersTable->save($user)) {
            throw new BadRequestException('Une erreur est survenue.');
        }

        header("WWW-Authenticate: " . $this->Authentication->generateJwt($user));

        return $this->apiResponse();
    }
}
