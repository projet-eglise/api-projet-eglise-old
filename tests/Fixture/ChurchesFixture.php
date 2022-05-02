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
                'name' => 'Lorem ipsum dolor sit amet',
                'uid' => 'Lorem ipsum dolor sit amet',
                'pastor_id' => 1,
                'main_administrator_id' => 1,
                'created_at' => 1651505893,
                'updated_at' => 1651505893,
            ],
        ];
        parent::init();
    }
}
