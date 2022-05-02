<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\ChurchUsersTable;
use App\Model\Table\UsersTable;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\HttpException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\ORM\TableRegistry;

/**
 * Churches Controller
 *
 * @property \App\Model\Table\ChurchesTable $Churches
 * @method \App\Model\Entity\Church[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ChurchesController extends AppController
{
    private ChurchUsersTable $ChurchUsers;
    private UsersTable $Users;

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Authentication');
        $this->loadComponent('File');

        $this->ChurchUsers = TableRegistry::getTableLocator()->get('ChurchUsers');
        $this->Users = TableRegistry::getTableLocator()->get('Users');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        if (!$this->request->is('post'))
            throw new MethodNotAllowedException('Utilisez une requête POST');

        if (is_null($this->request->getData('pastor_firstname')))
            throw new BadRequestException('Prénom du responsable non renseignée.');

        if ($this->request->getData('pastor_firstname') === '')
            throw new BadRequestException('Prénom du responsable vide.');


        if (is_null($this->request->getData('pastor_lastname')))
            throw new BadRequestException('Nom du responsable non renseignée.');

        if ($this->request->getData('pastor_lastname') === '')
            throw new BadRequestException('Nom du responsable vide.');

        if (is_null($this->request->getData('pastor_email')))
            throw new BadRequestException('Adresse mail du responsable non renseignée.');

        if (!filter_var($this->request->getData('pastor_email'), FILTER_VALIDATE_EMAIL))
            throw new BadRequestException('Adresse mail du responsable invalide.');

        // TODO Check if administrator mail == pastor mail
        $existingUser = $this->Users->findByEmail($this->request->getData('pastor_email'))->toArray();
        $pastorIsAdmin = isset($existingUser[0]) && $existingUser[0]->email === $this->request->getData('pastor_email');
        if (count($existingUser) !== 0 && !$pastorIsAdmin)
            throw new BadRequestException('Cette adresse mail du responsable est déjà affectée à un compte.');

        if (is_null($this->request->getData('church_name')))
            throw new BadRequestException('Nom de l\'Eglise non renseignée.');

        if ($this->request->getData('church_name') === '')
            throw new BadRequestException('Nom de l\'Eglise vide.');

        if (is_null($this->request->getData('church_address')))
            throw new BadRequestException('Adresse de l\'Eglise non renseignée.');

        if ($this->request->getData('church_address') === '')
            throw new BadRequestException('Adresse de l\'Eglise vide.');

        if (is_null($this->request->getData('church_postal_code')))
            throw new BadRequestException('Code postal de l\'Eglise non renseignée.');

        if ($this->request->getData('church_postal_code') === '')
            throw new BadRequestException('Code postal de l\'Eglise vide.');

        if (!preg_match('^[0-9][0-9A-B][0-9]{3}^', $this->request->getData('church_postal_code')))
            throw new BadRequestException('Code postal de l\'Eglise invalide.');

        if (is_null($this->request->getData('church_city')))
            throw new BadRequestException('Ville de l\'Eglise non renseignée.');

        if ($this->request->getData('church_city') === '')
            throw new BadRequestException('Ville de l\'Eglise vide.');

        $pastor = $pastorIsAdmin ? $existingUser[0] : $this->Users->newEntity([
            'uid' => uniqid(),
            'firstname' => $this->request->getData('pastor_firstname'),
            'lastname' => $this->request->getData('pastor_lastname'),
            'email' => $this->request->getData('pastor_email'),
            'password' => 'X',
            'phone_number' => 'X',
            'birthdate' => '1900-01-01',
            'has_profile_picture' => false,
        ]);

        if (count($pastor->getErrors()) > 0)
            throw new HttpException("Une erreur est survenu lors de la création du pasteur.\n" . json_encode($pastor->getErrors()), 422);

        $church = $this->Churches->newEntity([
            'uid' => uniqid(),
            'name' => $this->request->getData('church_name'),
            'pastor_id' => 1,
            'main_administrator_id' => $this->getUserId(),
        ]);

        if (count($church->getErrors()) > 0)
            throw new HttpException("Une erreur est survenu lors de la création de l'Eglise.\n" . json_encode($church->getErrors()), 422);

        if (!$pastorIsAdmin)
            if (!$this->Users->save($pastor))
                throw new InternalErrorException("Une erreur est survenu lors de l'ajout du pasteur.\n");

        $church->pastor_id = $pastor->user_id;

        if (!$this->Churches->save($church, ['associated' => false]))
            throw new InternalErrorException("Une erreur est survenu lors de l'ajout de l'Eglise.\n");

        $churchPastor = $this->ChurchUsers->newEntity([
            'church_id' => $church->church_id,
            'user_id' => $church->pastor_id,
        ]);

        $churchMainAdministrator = $this->ChurchUsers->newEntity([
            'church_id' => $church->church_id,
            'user_id' => $church->main_administrator_id,
        ]);

        if (count($churchPastor->getErrors()) > 0)
            throw new HttpException("Une erreur est survenu lors de la création du pasteur.\n" . json_encode($pastor->getErrors()), 422);

        if (count($churchMainAdministrator->getErrors()) > 0)
            throw new HttpException("Une erreur est survenu lors de la création du pasteur.\n" . json_encode($pastor->getErrors()), 422);

        if (!$pastorIsAdmin)
            if (!$this->ChurchUsers->save($churchPastor, ['associated' => false]))
                throw new InternalErrorException("Une erreur est survenu lors de l'ajout de l'Eglise.\n");

        if (!$this->ChurchUsers->save($churchMainAdministrator, ['associated' => false]))
            throw new InternalErrorException("Une erreur est survenu lors de l'ajout de l'Eglise.\n");

        $this->apiResponse(['pastor' => $pastor, 'church' => $church, 'churchPastor' => $churchPastor, 'churchMainAdministrator' => $churchMainAdministrator]);
    }
}
