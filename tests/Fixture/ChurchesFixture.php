<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ChurchesFixture
 */
class ChurchesFixture extends TestFixture
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
                'church_id' => 1,
                'name' => 'ADD Dijon',
                'uid' =>'627041d90c74f',
                'pastor_id' => 2,
                'main_administrator_id' => 2,
            ], [
                'church_id' => 2,
                'name' => 'ADD Autun',
                'uid' => '627041d90c752',
                'pastor_id' => 1,
                'main_administrator_id' => 1,
            ]
        ];
        parent::init();
    }
}
