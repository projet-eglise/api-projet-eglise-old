<?php


use Phinx\Seed\AbstractSeed;

class PE31RolesSeeder extends AbstractSeed
{
    public function getDependencies()
    {
        return ['PE14ChurchesSeeder'];
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
        $permissions = $this->table('permissions');
        $permissions
            ->insert([
                ['permission_id' => 1, 'uid' => uniqid(), 'name' => 'PLATFORM_ADMINISTRATOR'],
                ['permission_id' => 2, 'uid' => uniqid(), 'name' => 'CHURCH_MAIN_ADMINISTRATOR'],
                ['permission_id' => 3, 'uid' => uniqid(), 'name' => 'CHURCH_ADMINISTRATOR'],
                ['permission_id' => 4, 'uid' => uniqid(), 'name' => 'SERVICE_ADMINISTRATOR'],
                ['permission_id' => 5, 'uid' => uniqid(), 'name' => 'SERVICE_USER'],
            ])
            ->saveData();

        $services = $this->table('services');
        $services
            ->insert([
                ['service_id' => 1, 'uid' => uniqid(), 'name' => 'Louange'],
                ['service_id' => 2, 'uid' => uniqid(), 'name' => 'Entretien'],
            ])
            ->saveData();

        $roles = $this->table('roles');
        $roles
            ->insert([
                [
                    'role_id' => 1,
                    'uid' => uniqid(),
                    'name' => 'Administrateur principal',
                    'permission_id' => 2,
                ],
                [
                    'role_id' => 2,
                    'uid' => uniqid(),
                    'name' => 'Administrateur',
                    'permission_id' => 3,
                ],
                [
                    'role_id' => 3,
                    'uid' => uniqid(),
                    'name' => 'Responsable de la louange',
                    'permission_id' => 4,
                    'service_id' => 1,
                ],
                [
                    'role_id' => 4,
                    'uid' => uniqid(),
                    'name' => 'Responsable du ménage',
                    'permission_id' => 4,
                    'service_id' => 2,
                ],
                [
                    'role_id' => 5,
                    'uid' => uniqid(),
                    'name' => 'Musicien',
                    'permission_id' => 5,
                    'service_id' => 1,
                ],
                [
                    'role_id' => 6,
                    'uid' => uniqid(),
                    'name' => 'Chanteur',
                    'permission_id' => 5,
                    'service_id' => 1,
                ],
                [
                    'role_id' => 7,
                    'uid' => uniqid(),
                    'name' => 'Ménage',
                    'permission_id' => 5,
                    'service_id' => 2,
                ],
                [
                    'role_id' => 8,
                    'uid' => uniqid(),
                    'name' => 'Réparations',
                    'permission_id' => 5,
                    'service_id' => 2,
                ]
            ])
            ->saveData();

        $role_options = $this->table('role_options');
        $role_options
            ->insert([
                ['role_option_id' => 1, 'uid' => uniqid(), 'name' => 'Piano', 'role_id' => 5],
                ['role_option_id' => 2, 'uid' => uniqid(), 'name' => 'Batterie', 'role_id' => 5],
                ['role_option_id' => 3, 'uid' => uniqid(), 'name' => 'Violon', 'role_id' => 5],
                ['role_option_id' => 4, 'uid' => uniqid(), 'name' => 'Guitare', 'role_id' => 5],
                ['role_option_id' => 5, 'uid' => uniqid(), 'name' => 'Basse', 'role_id' => 5],
                ['role_option_id' => 6, 'uid' => uniqid(), 'name' => 'Soprano', 'role_id' => 6],
                ['role_option_id' => 7, 'uid' => uniqid(), 'name' => 'Mezzo', 'role_id' => 6],
                ['role_option_id' => 8, 'uid' => uniqid(), 'name' => 'Alto', 'role_id' => 6],
                ['role_option_id' => 9, 'uid' => uniqid(), 'name' => 'Contralto', 'role_id' => 6],
                ['role_option_id' => 10, 'uid' => uniqid(), 'name' => 'Ténor', 'role_id' => 6],
                ['role_option_id' => 11, 'uid' => uniqid(), 'name' => 'Baryton', 'role_id' => 6],
                ['role_option_id' => 12, 'uid' => uniqid(), 'name' => 'Basse', 'role_id' => 6],
            ])
            ->saveData();

        $church_user_roles = $this->table('church_user_roles');
        $church_user_roles
            ->insert([
                ['church_user_role_id' => 1, 'uid' => uniqid(), 'church_user_id' => 1, 'role_id' => 6, 'role_option_id' => 3],
                ['church_user_role_id' => 2, 'uid' => uniqid(), 'church_user_id' => 2, 'role_id' => 1, 'role_option_id' => null],
                ['church_user_role_id' => 3, 'uid' => uniqid(), 'church_user_id' => 2, 'role_id' => 7, 'role_option_id' => null],
                ['church_user_role_id' => 4, 'uid' => uniqid(), 'church_user_id' => 3, 'role_id' => 1, 'role_option_id' => null],
                ['church_user_role_id' => 5, 'uid' => uniqid(), 'church_user_id' => 3, 'role_id' => 7, 'role_option_id' => null],
                ['church_user_role_id' => 6, 'uid' => uniqid(), 'church_user_id' => 3, 'role_id' => 6, 'role_option_id' => 3],
                ['church_user_role_id' => 7, 'uid' => uniqid(), 'church_user_id' => 4, 'role_id' => 7, 'role_option_id' => null],
            ])
            ->saveData();
    }
}
