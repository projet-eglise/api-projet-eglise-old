<?php


use Phinx\Seed\AbstractSeed;

class PE14ChurchesSeeder extends AbstractSeed
{
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
        $churches = [
            [
                'church_id' => 1,
                'name' => 'ADD Dijon',
                'uid' => uniqid(),
                'pastor_id' => 2,
                'main_administrator_id' => 2,
                'created' => date('Y-m-d H:i:s'),
            ], [
                'church_id' => 2,
                'name' => 'ADD Autun',
                'uid' => uniqid(),
                'pastor_id' => 1,
                'main_administrator_id' => 1,
                'created' => date('Y-m-d H:i:s'),
            ]
        ];

        $churches = $this->table('churches');
        $churches->insert($churches)
            ->saveData();


        $church_users = [
            [
                'user_id' => 1,
                'church_id' => 1,
                'created' => date('Y-m-d H:i:s'),
            ], [
                'user_id' => 2,
                'church_id' => 1,
                'created' => date('Y-m-d H:i:s'),
            ],
            [
                'user_id' => 1,
                'church_id' => 2,
                'created' => date('Y-m-d H:i:s'),
            ], [
                'user_id' => 2,
                'church_id' => 2,
                'created' => date('Y-m-d H:i:s'),
            ]
        ];

        $church_users = $this->table('church_users');
        $church_users->insert($church_users)
            ->saveData();
    }
}
