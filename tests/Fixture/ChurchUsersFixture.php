<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ChurchUsersFixture
 */
class ChurchUsersFixture extends TestFixture
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
                'church_user_id' => 1,
                'uid' => '627aa0f97d62b',
                'user_id' => 1,
                'church_id' => 1,
            ], [
                'church_user_id' => 2,
                'uid' => '627aa0f97d62d',
                'user_id' => 2,
                'church_id' => 1,
            ],
            [
                'church_user_id' => 3,
                'uid' => '627aa0f97d62e',
                'user_id' => 1,
                'church_id' => 2,
            ], [
                'church_user_id' => 4,
                'uid' => '627aa0f97d62f',
                'user_id' => 2,
                'church_id' => 2,
            ]
        ];
        parent::init();
    }
}
