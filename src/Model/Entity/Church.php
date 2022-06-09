<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Interfaces\ApiRessource;
use App\Model\Table\AddressesTable;
use App\Model\Table\ChurchesTable;
use App\Model\Table\ChurchUserRolesTable;
use App\Model\Table\UsersTable;
use App\Traits\Hydrate;
use Cake\Http\Exception\InternalErrorException;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Church Entity
 *
 * @property int $church_id
 * @property string $name
 * @property string $uid
 * @property int $pastor_id
 * @property int $main_administrator_id
 * @property int $address_id
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 * 
 * @property Address $address
 * @property User $pastor
 * @property User $main_administrator
 */
class Church extends Entity implements ApiRessource
{
    use Hydrate;

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
        'name' => true,
        'uid' => true,
        'pastor_id' => true,
        'main_administrator_id' => true,
        'address_id' => true,
        'created_at' => true,
        'updated_at' => true,
        'address' => true,
        'pastor' => true,
        'main_administrator' => true,
    ];

    private AddressesTable $Addresses;
    private ChurchUserRolesTable $ChurchUserRoles;
    private ChurchesTable $Churches;
    private UsersTable $Users;

    public ?User $main_administrator;
    public ?User $pastor;
    public ?Address $address;

    public function __construct(array $properties = [], array $options = [])
    {
        parent::__construct($properties, $options);

        $this->Addresses = TableRegistry::getTableLocator()->get('Addresses');
        $this->ChurchUserRoles = TableRegistry::getTableLocator()->get('ChurchUserRoles');
        $this->Churches = TableRegistry::getTableLocator()->get('Churches');
        $this->Users = TableRegistry::getTableLocator()->get('Users');
    }

    public function setPastor(User $pastor): User
    {
        if ($pastor->user_id !== null) $this->pastor_id = $pastor->user_id;
        return $pastor;
    }

    public function setMainAdministrator(User $admin): User
    {
        if ($admin->user_id !== null) $this->main_administrator_id = $admin->user_id;
        return $admin;
    }

    /**
     * Fills in the variable mainAdministrator.
     */
    private function retrieveMainAdministrator()
    {
        return $this->Users->get($this->main_administrator_id);
    }

    /**
     * Fills in the variable pastor.
     */
    private function retrievePastor()
    {
        return $this->Users->get($this->pastor_id);
    }

    /**
     * Fills in the variable address.
     */
    private function retrieveAddress()
    {
        return $this->Addresses->get($this->address_id);
    }

    /**
     * Add a senior administrator to the church if it does not have one.
     *
     * @param User $user
     * @return void
     */
    public function addMainAdministrator(User $user, bool $validate = false)
    {
        $this->hydrate('main_administrator');

        if ($this->mainAdministrator !== null) {
            throw new InternalErrorException('Un administrateur principal est déjà affecté à cet Eglise');
        }

        if (!$user->isInChurch($this)) {
            throw new InternalErrorException("L'administrateur présumé n'appartient pas à cette Eglise");
        }

        $administrator = $this->ChurchUserRoles->newEntity([
            'uid' => uniqid(),
            'validate' => $validate,
            'role_id' => 1,
            'church_user_id' => $user->getChurchUserId($this),
            'role_option_id' => null,
        ]);

        if (!$this->ChurchUserRoles->save($administrator, ['associated' => false]))
            throw new InternalErrorException("Une erreur est survenue l'ajout de l'administrateur principal");

        $this->main_administrator_id = $user->user_id;

        if (!$this->Churches->save($this, ['associated' => false]))
            throw new InternalErrorException("Une erreur est survenue la sauvegarde de l'administrateur principal");
    }

    /**
     * Add a pastor to the church if it does not have one.
     *
     * @param User $user
     * @return void
     */
    public function addPastor(User $user)
    {
        $this->hydrate('pastor');

        if ($this->mainPastor !== null) {
            throw new InternalErrorException('Un pasteur est déjà affecté à cet Eglise');
        }

        if (!$user->isInChurch($this)) {
            throw new InternalErrorException("Le pasteur présumé n'appartient pas à cette Eglise");
        }

        $mainPastor = $this->ChurchUserRoles->newEntity([
            'uid' => uniqid(),
            'role_id' => 1,
            'church_user_id' => $user->getChurchUserId($this),
            'role_option_id' => null,
        ]);

        if (!$this->ChurchUserRoles->save($mainPastor, ['associated' => false]))
            throw new InternalErrorException("Une erreur est survenue l'ajout du pasteur");

        $this->pastor_id = $user->user_id;

        if (!$this->Churches->save($this, ['associated' => false]))
            throw new InternalErrorException("Une erreur est survenue la sauvegarde du pasteur");
    }

    /**
     * Unset the variables needed for an api return.
     *
     * @return Church
     */
    public function toApi(bool $withRelations = false): Church
    {
        if ($withRelations) {
            $this->hydrate('main_administrator');
            $this->hydrate('pastor');
            $this->hydrate('address');
        }

        unset($this->church_id);
        unset($this->address_id);
        unset($this->pastor_id);
        unset($this->main_administrator_id);
        unset($this->created_at);
        unset($this->updated_at);
        unset($this->pastor);

        if (isset($this->mainAdministrator)) {
            $this->main_administrator = $this->mainAdministrator->toApi();
        }

        if (isset($this->mainPastor)) {
            $this->pastor = $this->mainPastor->toApi();
        }

        if (isset($this->address))
            $this->address->toApi();

        return $this;
    }

    /**
     * Modifies variables to serve as tokens.
     *
     * @return Church
     */
    public function toToken(): Church
    {
        unset($this->church_id);
        unset($this->address_id);
        unset($this->pastor_id);
        unset($this->main_administrator_id);
        unset($this->created_at);
        unset($this->updated_at);

        if (isset($this->main_administrator))
            $this->main_administrator->toApi();

        if (isset($this->pastor))
            $this->pastor->toApi();

        if (isset($this->addres))
            $this->address = $this->addres->toApi();
        else
            unset($this->address);

        unset($this->addres);
        unset($mainAdministrator);
        unset($mainPastor);

        return $this;
    }
}
