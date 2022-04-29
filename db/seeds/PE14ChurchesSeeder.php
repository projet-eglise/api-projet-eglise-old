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
        $churches = $this->table('churches');
        $churches->insert([
            [
                'church_id' => 1,
                'name' => 'ADD Dijon',
                'uid' => uniqid(),
                'pastor_id' => 2,
                'main_administrator_id' => 2,
            ], [
                'church_id' => 2,
                'name' => 'ADD Autun',
                'uid' => uniqid(),
                'pastor_id' => 1,
                'main_administrator_id' => 1,
            ]
        ])
            ->saveData();


        $church_users = $this->table('church_users');
        $church_users->insert($church_users = [
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
        ])
            ->saveData();
    }
}
