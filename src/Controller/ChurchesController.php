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
    private ChurchUsersTable $ChurchUsers;
    private UsersTable $Users;

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Authentication');
        $this->loadComponent('File');

        $this->Addresses = TableRegistry::getTableLocator()->get('Addresses');
        $this->ChurchUsers = TableRegistry::getTableLocator()->get('ChurchUsers');
        $this->Users = TableRegistry::getTableLocator()->get('Users');
    }

    /**
     * @todo refacto
     * @return void
     */
    public function index()
    {
        $this->apiResponse(
            $this->Churches->find('all', [
                'fields' => [
                    'uid',
                    'name',
                    'address__address' => 'address',
                    'address__address2' => 'CONCAT(postal_code, \' \', city)',
                    'address__city' => 'city',
                    'pastor__name' => 'CONCAT(firstname, \' \', UPPER(lastname))'
                ],
                'contain' => [
                    'Address', 'Pastor'
                ]
            ])->toArray()
        );
    }

    /**
     * @todo refacto
     * @return void
     */
    public function getAllForJoin()
    {
        $myChurches = $this->ChurchUsers->find('list', [
            'keyField' => 'church_id',
            'valueField' => 'church_id',
            'conditions' => ['user_id' => $this->connectedUser->user_id],
        ])->toArray();

        $this->apiResponse(
            $this->Churches
                ->find('all', [
                    'fields' => [
                        'uid',
                        'name',
                        'address__address' => 'address',
                        'address__address2' => 'CONCAT(postal_code, \' \', city)',
                        'address__city' => 'city',
                        'pastor__name' => 'CONCAT(firstname, \' \', UPPER(lastname))'
                    ],
                    'contain' => [
                        'Address', 'Pastor'
                    ]
                ])
                ->where(count($myChurches) > 0 ? [
                    "church_id NOT IN (" . implode(", ", $myChurches) . ")"
                ] : [])
                ->toArray()
        );
    }

    /**
     * View method
     *
     * @todo refacto
     * @param string|null $churchUid
     */
    public function view(string $churchUid = null)
    {
        $church = $this->Churches->findByUid(
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
        )->contain(
            [
                'Pastor' => ['fields' => [
                    'uid',
                    'firstname',
                    'lastname',
                    'email',
                    'phone_number',
                    'has_profile_picture',
                    'profile_image_link',
                ]],
                'MainAdministrator' => ['fields' => [
                    'uid',
                    'firstname',
                    'lastname',
                    'email',
                    'phone_number',
                    'has_profile_picture',
                    'profile_image_link',
                ]],
                'Address'
            ]
        )->first();

        return $this->apiResponse(['church' => $church]);
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

        $pastorUser = $this->Users->findByEmail($pastor->email)->first();

        $pastorIsAdmin = isset($pastorUser) && $pastorUser->email === $this->connectedUser->email;
        if (isset($pastorUser) && !$pastorIsAdmin)
            throw new BadRequestException('Cette adresse mail est déjà affectée à un compte.');

        $pastor = $pastorIsAdmin ? $pastorUser : $pastor;

        $church = $this->Churches->newEntity([
            'uid' => uniqid(),
            'name' => $this->request->getData('church_name'),
        ]);

        $churchAddress = $this->Addresses->newEntity([
            'uid' => uniqid(),
            'address' => $this->request->getData('church_address'),
            'postal_code' => $this->request->getData('church_postal_code'),
            'city' => $this->request->getData('church_city'),
        ]);

        $this->Users->getConnection()->begin();

        if (!$pastorIsAdmin) {
            if (!$this->Users->save($pastor, ['checkRules' => false])) {
                $errors = $pastor->getErrors();
                if (empty($errors))
                    throw new BadRequestException('Une erreur est survenue lors de l\'enregistrement');

                $field = reset($errors);
                throw new BadRequestException(reset($field));
            }
        }

        if (!$this->Addresses->save($churchAddress, ['associated' => false])) {
            $errors = $churchAddress->getErrors();
            if (empty($errors))
                throw new BadRequestException('Une erreur est survenue lors de l\'enregistrement');

            $field = reset($errors);
            throw new BadRequestException(reset($field));
        }

        $church->address_id = $churchAddress->address_id;

        if (!$this->Churches->save($church, ['associated' => false])) {
            $errors = $church->getErrors();
            if (empty($errors))
                throw new BadRequestException('Une erreur est survenue lors de l\'enregistrement');

            $field = reset($errors);
            throw new BadRequestException(reset($field));
        }

        $this->connectedUser->joinChurch($church);
        $church->addMainAdministrator($this->connectedUser);

        $pastor->joinChurch($church);
        $church->addPastor($pastor);

        $this->Users->getConnection()->commit();
        return $this->view($church->uid);
    }
}
