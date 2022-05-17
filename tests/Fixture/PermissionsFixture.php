<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PermissionsFixture
 */
class PermissionsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            ['permission_id' => 1, 'uid' => '627aa0f97f178', 'name' => 'PLATFORM_ADMINISTRATOR'],
            ['permission_id' => 2, 'uid' => '627aa0f97f17f', 'name' => 'CHURCH_MAIN_ADMINISTRATOR'],
            ['permission_id' => 3, 'uid' => '627aa0f97f180', 'name' => 'CHURCH_ADMINISTRATOR'],
            ['permission_id' => 4, 'uid' => '627aa0f97f181', 'name' => 'SERVICE_ADMINISTRATOR'],
            ['permission_id' => 5, 'uid' => '627aa0f97f182', 'name' => 'SERVICE_USER'],
        ];
        parent::init();
    }
}
