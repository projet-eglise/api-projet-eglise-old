<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\ServicesTable;
use Cake\ORM\TableRegistry;

/**
 * Roles Controller
 *
 * @property \App\Model\Table\RolesTable $Roles
 * @method \App\Model\Entity\Role[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RolesController extends AppController
{
    private ServicesTable $Services;

    public function initialize(): void
    {
        parent::initialize();
        $this->Services = TableRegistry::getTableLocator()->get('Services');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $services = $this->Services->find('all', [
            'fields' => ['service_id', 'uid', 'name'],
            'contain' => [
                'Roles' => [
                    'fields' => ['service_id', 'role_id', 'uid', 'name'],
                ],
                'Roles.RoleOptions' => [
                    'fields' => ['role_id', 'uid', 'name']
                ]
            ]
        ])->toArray();

        foreach ($services as $service) {
            unset($service->service_id);
            foreach ($service->roles as $role) {
                unset($role->service_id);
                unset($role->role_id);
                foreach ($role->role_options as $role_option) {
                    unset($role_option->role_id);
                }
            }
        }

        $this->apiResponse($services);
    }
}
