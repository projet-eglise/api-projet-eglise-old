<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Controller\Component\FileComponent;
use App\Model\Table\ChurchesTable;
use App\Model\Table\ChurchUserRolesTable;
use App\Model\Table\ChurchUsersTable;
use App\Model\Table\RoleOptionsTable;
use App\Model\Table\RolesTable;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\InternalErrorException;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Laminas\Diactoros\UploadedFile;

/**
 * User Entity
 *
 * @property int $user_id
 * @property string $uid
 * @property bool $is_admin
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $password
 * @property string $phone_number
 * @property \Cake\I18n\FrozenDate $birthdate
 * @property bool $has_profile_picture
 * @property string|null $profile_image_link
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 */
class User extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'uid' => true,
        'is_admin' => true,
        'firstname' => true,
        'lastname' => true,
        'email' => true,
        'password' => true,
        'phone_number' => true,
        'birthdate' => true,
        'has_profile_picture' => true,
        'profile_image_link' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password',
    ];

    private array $hydrated = [
        'churches' => false,
        'roles' => false,
    ];

    private ChurchUsersTable $ChurchUsers;
    private ChurchUserRolesTable $ChurchUserRoles;
    private ChurchesTable $Churches;
    private RolesTable $Roles;
    private RoleOptionsTable $RoleOptions;
    private FileComponent $File;

    private array $churches = [];
    private array $roles = [];

    public function __construct(array $properties = [], array $options = [])
    {
        parent::__construct($properties, $options);

        $this->ChurchUsers = TableRegistry::getTableLocator()->get('ChurchUsers');
        $this->ChurchUserRoles = TableRegistry::getTableLocator()->get('ChurchUserRoles');
        $this->Churches = TableRegistry::getTableLocator()->get('Churches');
        $this->Roles = TableRegistry::getTableLocator()->get('Roles');
        $this->RoleOptions = TableRegistry::getTableLocator()->get('RoleOptions');

        $this->File = new FileComponent(new ComponentRegistry());
    }

    /**
     * Fills in the variable churches.
     */
    private function hydrateChurches()
    {
        if (!$this->hydrated['churches']) {
            $this->churches = [];
            $churchesIds = $this->ChurchUsers->find('list', [
                'keyField' => 'church_id',
                'valueField' => 'church_id',
                'conditions' => ['user_id' => $this->user_id],
            ])->toArray();

            foreach ($churchesIds as $churchId) {
                $this->churches[] = $this->Churches->get($churchId);
            }

            $this->hydrated['churches'] = true;
        }
    }

    /**
     * Fills in the variable roles.
     */
    private function hydrateRoles()
    {
        if (!$this->hydrated['roles']) {
            $this->roles = $this->ChurchUserRoles->find('all', [
                'contain' => ['ChurchUsers', 'ChurchUsers.Churches'],
                'conditions' => ['user_id' => $this->user_id]
            ])->toArray();
            $this->hydrated['roles'] = true;
        }
    }

    /**
     * Adds a profile picture to the user.
     *
     * @param UploadedFile $image
     * @return void
     */
    public function addProfilePicture(UploadedFile $image)
    {
        $this->File->checkImageFile($image);
        $this->profile_image_link = $this->File->upload($image->getStream()->getMetadata()["uri"]);
        $this->has_profile_picture = true;
    }

    public function getChurches(): array
    {
        $this->hydrateChurches();

        foreach ($this->churches as $church)
            $church->toApi();

        return $this->churches;
    }

    /**
     * Returns the ChurchUserId of the user based on a Church.
     *
     * @param Church $church
     * @return void
     */
    public function getChurchUserId(Church $church)
    {
        return $this->ChurchUsers->find('all', [
            'conditions' => ['user_id' => $this->user_id, 'church_id' => $church->church_id],
        ])->first()->church_user_id;
    }

    /**
     * Checks if the user belongs to the church passed in parameter.
     *
     * @param Church $church
     * @return boolean
     */
    public function isInChurch(Church $churchToCheck)
    {
        $this->hydrateChurches();

        foreach ($this->churches as $church)
            if ($church->church_id === $churchToCheck->church_id)
                return true;

        return false;
    }

    /**
     * Allows a user to join a church.
     *
     * @param Church $church
     * @return void
     */
    public function joinChurch(Church $church): ?ChurchUser
    {
        if ($this->isInChurch($church)) {
            throw new InternalErrorException('Vous ne pouvez pas rejoindre deux fois une même Eglise ...');
        }

        $churchUser = $this->ChurchUsers->newEntity([
            'uid' => uniqid(),
            'church_id' => $church->church_id,
            'user_id' => $this->user_id,
        ]);

        if (!$this->ChurchUsers->save($churchUser, ['associated' => false]))
            throw new InternalErrorException("Une erreur est survenue en rejoignant l'Eglise");

        $this->hydrated['churches'] = false;

        return $churchUser;
    }

    /**
     * Returns true if the role is already assigned to the user.
     *
     * @param ChurchUserRole $roleToCheck
     * @return boolean
     */
    public function hasRole(ChurchUserRole $roleToCheck): bool
    {
        $this->hydrateRoles();

        foreach ($this->roles as $role) {
            if (
                $role->church_user_id === $roleToCheck->church_user_id
                && $role->role_id === $roleToCheck->role_id
                && $role->role_option_id === $roleToCheck->role_option_id
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Adds roles that the user does not have yet.
     * Be careful, this operation saves the data at the end of the function.
     *
     * @param array $roles
     * @param Church $church
     * @return void
     */
    public function addRoles(array $roles, Church $church): void
    {
        if (!$this->isInChurch($church)) {
            throw new InternalErrorException('Vous n\'appartenez pas à cette Eglise. Vous devez la rejoindre avant de choisir des rôles.');
        }

        foreach ($roles as $value) {
            $role = $this->Roles->findByUid($value['role'])->first();
            $churchUserRole = $this->ChurchUserRoles->newEmptyEntity();

            $churchUserRole->role_option_id = null;
            if ($role->needOption()) {
                $option = $this->RoleOptions->findByUid($value['option'])->first();

                if (!$role->isAnOption($option)) {
                    throw new InternalErrorException("L'option " . $option->name . " ne peut être ajouté au rôle " . $role->name);
                }

                $churchUserRole->role_option_id = $option->role_option_id;
            }

            $churchUserRole->role_id = $role->role_id;
            $churchUserRole->church_user_id = $this->getChurchUserId($church);
            $churchUserRole->uid = uniqid();

            if (!$this->hasRole($churchUserRole)) {
                $this->ChurchUserRoles->save($churchUserRole);
                $hydrated['roles'] = false;
            }
        }
    }

    /**
     * Unset the variables needed for an api return.
     *
     * @return User
     */
    public function toApi(): User
    {
        unset($this->user_id);
        unset($this->created_at);
        unset($this->updated_at);

        unset($this->churches);
        unset($this->roles);

        return $this;
    }

    /**
     * Modifies variables to serve as tokens.
     *
     * @return User
     */
    public function toToken(): User
    {
        $this->hydrateChurches();
        foreach ($this->churches as $church)
            $church->toApi();

        unset($this->user_id);
        unset($this->created_at);
        unset($this->updated_at);
        unset($this->roles);

        return $this;
    }
}
