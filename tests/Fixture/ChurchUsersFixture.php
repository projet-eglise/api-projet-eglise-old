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
                'user_id' => 1,
                'church_id' => 1,
            ], [
                'church_user_id' => 2,
                'user_id' => 2,
                'church_id' => 1,
            ],
            [
                'church_user_id' => 3,
                'user_id' => 1,
                'church_id' => 2,
            ], [
                'church_user_id' => 4,
                'user_id' => 2,
                'church_id' => 2,
            ]
        ];
        parent::init();
    }
}
