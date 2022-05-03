<?php


use Phinx\Seed\AbstractSeed;

class PE29AddressesSeeder extends AbstractSeed
{
    public function getDependencies()
    {
        return ['PE8UsersSeeder'];
    }

    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $addresses = $this->table('addresses');
        $addresses->insert([
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
            ]
        ])
            ->saveData();
    }
}
