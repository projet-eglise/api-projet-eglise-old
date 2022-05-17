<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * RolesFixture
 */
class RolesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'role_id' => 1,
                'uid' => '627aa0f97f902',
                'name' => 'Administrateur principal',
                'permission_id' => 2,
            ],
            [
                'role_id' => 2,
                'uid' => '627aa0f97f912',
                'name' => 'Administrateur',
                'permission_id' => 3,
            ],
            [
                'role_id' => 3,
                'uid' => '627aa0f97f914',
                'name' => 'Responsable de la louange',
                'permission_id' => 4,
                'service_id' => 1,
            ],
            [
                'role_id' => 4,
                'uid' => '627aa0f97f916',
                'name' => 'Responsable du ménage',
                'permission_id' => 4,
                'service_id' => 2,
            ],
            [
                'role_id' => 5,
                'uid' => '627aa0f97f917',
                'name' => 'Musicien',
                'permission_id' => 5,
                'service_id' => 1,
            ],
            [
                'role_id' => 6,
                'uid' => '627aa0f97f918',
                'name' => 'Chanteur',
                'permission_id' => 5,
                'service_id' => 1,
            ],
            [
                'role_id' => 7,
                'uid' => '627aa0f97f919',
                'name' => 'Ménage',
                'permission_id' => 5,
                'service_id' => 2,
            ],
            [
                'role_id' => 8,
                'uid' => '627aa0f97f91a',
                'name' => 'Réparations',
                'permission_id' => 5,
                'service_id' => 2,
            ]
        ];
        parent::init();
    }
}
