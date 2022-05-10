<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ServicesFixture
 */
class ServicesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            ['service_id' => 1, 'uid' => '627aa0f97f574', 'name' => 'Louange'],
            ['service_id' => 2, 'uid' => '627aa0f97f577', 'name' => 'Entretien'],
        ];
        parent::init();
    }
}
