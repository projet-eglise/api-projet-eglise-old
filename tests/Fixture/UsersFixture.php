<?php

declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
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
                'user_id' => 1,
                'uid' => '626554417f45e',
                'is_admin' => true,
                'firstname' => 'Florence',
                'lastname' => 'HOFMANN',
                'email' => 'fgaconcamoz@gmail.com',
                'password' => '$2y$12$nL44SXSauQMr/YdH9KLIl..V.5uJ/3ivHXxHtCYvFgp5okam4cD76',
                'phone_number' => '+33 6 74 31 15 74',
                'birthdate' => '1987-03-03',
                'has_profile_picture' => true,
                'profile_image_link' => 'https://cdn.filestackcontent.com/dOM9FGJpTiWXuqVp0uHI',
            ], [
                'user_id' => 2,
                'uid' => '6265545515f21',
                'is_admin' => true,
                'firstname' => 'Timothé',
                'lastname' => 'HOFMANN',
                'email' => 'timothe@hofmann.fr',
                'password' => '$2y$12$ja/Ozxo/wEOQKMRyTnBWhODg3Oc.o3JwClo8a.hyvNZwIU5OJ88.W',
                'phone_number' => '+33 7 81 28 03 28',
                'birthdate' => '2001-04-11',
                'has_profile_picture' => true,
                'profile_image_link' => 'https://cdn.filestackcontent.com/l92jeAdTeusVOZzNLxiw',
            ]
        ];
        parent::init();
    }
}
