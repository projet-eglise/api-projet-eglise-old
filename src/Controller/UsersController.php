<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\ChurchesTable;
use App\Model\Table\UsersTable;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\TableRegistry;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    private UsersTable $Users;
    private ChurchesTable $Churches;

    public function initialize(): void
    {
        parent::initialize();

        $this->Users = TableRegistry::getTableLocator()->get('Users');
        $this->Churches = TableRegistry::getTableLocator()->get('Churches');
    }

    /**
     * Join church
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function addRolesInChurch()
    {
        $user = $this->Users->findByUid($this->request->getParam('userUid'))->first();
        if ($user == null) {
            throw new BadRequestException('Utilisateur inexistant.');
        }

        $church = $this->Churches->findByUid($this->request->getParam('churchUid'))->first();
        if ($church == null) {
            throw new BadRequestException('Eglise inexistant.');
        }

        $user->addRoles(json_decode($this->request->getData('roles'), true), $church);

        return $this->apiResponse();
    }
}
