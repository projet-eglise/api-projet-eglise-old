<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ChurchUserRolesFixture
 */
class ChurchUserRolesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            ['church_user_role_id' => 1, 'uid' => '627aa0f982308', 'church_user_id' => 1, 'role_id' => 6, 'role_option_id' => 3],
            ['church_user_role_id' => 2, 'uid' => '627aa0f98230c', 'church_user_id' => 2, 'role_id' => 1, 'role_option_id' => null],
            ['church_user_role_id' => 3, 'uid' => '627aa0f98230e', 'church_user_id' => 2, 'role_id' => 7, 'role_option_id' => null],
            ['church_user_role_id' => 4, 'uid' => '627aa0f98230f', 'church_user_id' => 3, 'role_id' => 1, 'role_option_id' => null],
            ['church_user_role_id' => 5, 'uid' => '627aa0f982310', 'church_user_id' => 3, 'role_id' => 7, 'role_option_id' => null],
            ['church_user_role_id' => 6, 'uid' => '627aa0f982311', 'church_user_id' => 3, 'role_id' => 6, 'role_option_id' => 3],
            ['church_user_role_id' => 7, 'uid' => '627aa0f982312', 'church_user_id' => 4, 'role_id' => 7, 'role_option_id' => null],
        ];
        parent::init();
    }
}
