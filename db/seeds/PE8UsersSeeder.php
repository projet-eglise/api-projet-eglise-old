<?php


use Phinx\Seed\AbstractSeed;

class PE8UsersSeeder extends AbstractSeed
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
        $users = [
            [
                'user_id' => 1,
                'uid' => '626554417f45e',
                'is_admin' => true,
                'firstname' => 'Florence',
                'lastname' => 'HOFMANN',
                'email' => 'fgaconcamoz@gmail.com',
                'password' => '$2y$12$ZvSuPO3Eokh/NFSuHq5nc.uxJ8yPJ8PqQbOfklOZfswuAi1Yy5jrG',
                'phone_number' => '+33 6 74 31 15 74',
                'birthdate' => '1987-03-03',
                'has_profile_picture' => true,
                'profile_image_link' => 'https://cdn.filestackcontent.com/dOM9FGJpTiWXuqVp0uHI',
                'created' => date('Y-m-d H:i:s'),
            ], [
                'user_id' => 2,
                'uid' => '6265545515f21',
                'is_admin' => true,
                'firstname' => 'Timothé',
                'lastname' => 'HOFMANN',
                'email' => 'timothe@hofmann.fr',
                'password' => '$2y$12$7hAgs92GqYU69RjOUPwA/OtXMoSJ2WlwVxJt.LY3.B3iYQzlKbRUS',
                'phone_number' => '+33 7 81 28 03 28',
                'birthdate' => '2001-04-11',
                'has_profile_picture' => true,
                'profile_image_link' => 'https://cdn.filestackcontent.com/l92jeAdTeusVOZzNLxiw',
                'created' => date('Y-m-d H:i:s'),
            ]
        ];

        $users = $this->table('users');
        $users->insert($users)
            ->saveData();
    }
}
