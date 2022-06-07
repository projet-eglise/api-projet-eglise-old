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
    private ChurchesTable $Churches;

    public function initialize(): void
    {
        parent::initialize();

        $this->Users = TableRegistry::getTableLocator()->get('Users');
        $this->Churches = TableRegistry::getTableLocator()->get('Churches');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $users = $this->Users->find('all', ['order' => ['firstname' => 'ASC', 'lastname' => 'ASC']])->toArray();

        foreach ($users as $user)
            $user->toApi();

        $this->apiResponse($users);
    }

    /**
     * Adds roles for a user of a church.
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function addRolesInChurch()
    {
        $user = $this->connectedUser;

        $church = $this->Churches->findByUid($this->request->getParam('churchUid'))->first();
        if ($church == null) {
            throw new BadRequestException('Eglise inexistant.');
        }

        $user->addRoles($this->request->getData('roles') ?? [], $church);

        return $this->apiResponse();
    }
}
