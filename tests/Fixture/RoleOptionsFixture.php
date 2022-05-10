<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * RoleOptionsFixture
 */
class RoleOptionsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            ['role_option_id' => 1, 'uid' => '627aa0f981e15', 'name' => 'Piano', 'role_id' => 6],
            ['role_option_id' => 2, 'uid' => '627aa0f981e18', 'name' => 'Batterie', 'role_id' => 6],
            ['role_option_id' => 3, 'uid' => '627aa0f981e19', 'name' => 'Violon', 'role_id' => 6],
            ['role_option_id' => 4, 'uid' => '627aa0f981e1a', 'name' => 'Guitare', 'role_id' => 6],
            ['role_option_id' => 5, 'uid' => '627aa0f981e1b', 'name' => 'Basse', 'role_id' => 6],
            ['role_option_id' => 6, 'uid' => '627aa0f981e1c', 'name' => 'Soprano', 'role_id' => 7],
            ['role_option_id' => 7, 'uid' => '627aa0f981e1d', 'name' => 'Mezzo', 'role_id' => 7],
            ['role_option_id' => 8, 'uid' => '627aa0f981e1e', 'name' => 'Alto', 'role_id' => 7],
            ['role_option_id' => 9, 'uid' => '627aa0f981e1f', 'name' => 'Contralto', 'role_id' => 7],
            ['role_option_id' => 10, 'uid' => '627aa0f981e20', 'name' => 'Ténor', 'role_id' => 7],
            ['role_option_id' => 11, 'uid' => '627aa0f981e21', 'name' => 'Baryton', 'role_id' => 7],
            ['role_option_id' => 12, 'uid' => '627aa0f981e22', 'name' => 'Basse', 'role_id' => 7],
        ];
        parent::init();
    }
}
