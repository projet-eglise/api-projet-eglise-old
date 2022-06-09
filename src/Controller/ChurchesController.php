<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\AddressesTable;
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
    private AddressesTable $Addresses;
    private UsersTable $Users;

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Authentication');
        $this->loadComponent('File');

        $this->Addresses = TableRegistry::getTableLocator()->get('Addresses');
        $this->Users = TableRegistry::getTableLocator()->get('Users');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $churches = $this->Churches->find('all', ['order' => ['name' => 'ASC']])->toArray();

        foreach ($churches as $church)
            $church->toApi(true);

        $this->apiResponse($churches);
    }

    /**
     * Returns the churches that a user can join.
     * @return void
     */
    public function joinable()
    {
        $churches = $this->Churches->getJoinable($this->connectedUser);

        foreach ($churches as $church) {
            $churchesToReturn[] = $church;
        }

        return $this->apiResponse($churchesToReturn);
    }

    /**
     * View method
     *
     * @param string|null $churchUid
     */
    public function view(string $churchUid = null)
    {
        $church = $this->Churches
            ->findByUid(
                $churchUid ?? $this->request->getParam('uid'),
                [
                    'fields' => [
                        'uid',
                        'name',
                        'address_uid' => 'Address.uid',
                        'address_address' => 'Address.address',
                        'address_postal_code' => 'Address.postal_code',
                        'address_city' => 'Address.city',
                    ],
                    'contain' => ['Address']
                ]
            )->contain(['Address', 'Pastor', 'MainAdministrator'])
            ->first();

        return $this->apiResponse(['church' => $church->toApi()]);
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

        $pastor = $this->Users->newEntity([
            'uid' => uniqid(),
            'firstname' => $this->request->getData('pastor_firstname'),
            'lastname' => $this->request->getData('pastor_lastname'),
            'email' => $this->request->getData('pastor_email'),
            'password' => 'X',
            'phone_number' => '+00 0 00 00 00 00',
            'birthdate' => '1900-01-01',
            'has_profile_picture' => false,
        ]);

        $church = $this->Churches->newEntity([
            'uid' => uniqid(),
            'name' => $this->request->getData('church_name'),
            'address' => [
                'uid' => uniqid(),
                'address' => $this->request->getData('church_address'),
                'postal_code' => $this->request->getData('church_postal_code'),
                'city' => $this->request->getData('church_city'),
            ],
        ]);

        $church = $this->Churches->create($church, $this->connectedUser, $pastor);

        return $this->view($church->uid);
    }

    public function join()
    {
        if (!$this->request->is('get'))
            throw new MethodNotAllowedException('Utilisez une requête GET');

        $church = $this->Churches->findByUid($this->request->getParam('uid'))->first();
        if ($church == null)
            throw new BadRequestException('Eglise inexistant.');

        $this->connectedUser->joinChurch($church);

        return $this->apiResponse();
    }
}
