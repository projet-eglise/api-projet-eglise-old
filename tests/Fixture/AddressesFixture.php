<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AddressesFixture
 */
class AddressesFixture extends TestFixture
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
                'address_id' => 1,
                'uid' => '627160a64db3c',
                'address' => '9 Rue Vivant Carion',
                'postal_code' => '21000',
                'city' => 'Dijon',
            ], [
                'address_id' => 2,
                'uid' => '627160c1541c0',
                'address' => '9 Grande Rue MarchauX',
                'postal_code' => '71400',
                'city' => 'Autun',
            ],
        ];
        parent::init();
    }
}
