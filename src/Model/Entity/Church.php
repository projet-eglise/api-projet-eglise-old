<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Interfaces\ApiRessource;
use App\Model\Table\AddressesTable;
use App\Model\Table\ChurchesTable;
use App\Model\Table\ChurchUserRolesTable;
use App\Model\Table\UsersTable;
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
 */
class Church extends Entity implements ApiRessource
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
        'name' => true,
        'uid' => true,
        'pastor_id' => true,
        'main_administrator_id' => true,
        'address_id' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    private $hydrated = [
        'mainAdministrator' => false,
        'mainPastor' => false,
        'address' => false,
    ];

    private ?User $mainAdministrator;
    private ?User $mainPastor;

    private AddressesTable $Addresses;
    private ChurchUserRolesTable $ChurchUserRoles;
    private ChurchesTable $Churches;
    private UsersTable $Users;

    public function __construct(array $properties = [], array $options = [])
    {
        parent::__construct($properties, $options);

        $this->Addresses = TableRegistry::getTableLocator()->get('Addresses');
        $this->ChurchUserRoles = TableRegistry::getTableLocator()->get('ChurchUserRoles');
        $this->Churches = TableRegistry::getTableLocator()->get('Churches');
        $this->Users = TableRegistry::getTableLocator()->get('Users');
    }

    /**
     * Fills in the variable mainAdministrator.
     */
    private function hydrateMainAdministrator()
    {
        if (!$this->hydrated['mainAdministrator'] && !isset($this->mainAdministrator)) {
            if ($this->main_administrator_id !== null)
                $this->mainAdministrator = $this->Users->get($this->main_administrator_id);
            else
                $this->mainAdministrator = null;
        }
        $this->hydrated['mainAdministrator'] = true;
    }

    /**
     * Fills in the variable pastor.
     */
    private function hydratePastor()
    {
        if (!$this->hydrated['mainPastor'] && !isset($this->mainPastor)) {
            if ($this->pastor_id !== null)
                $this->mainPastor = $this->Users->get($this->pastor_id);
            else
                $this->mainPastor = null;
        }
        $this->hydrated['mainPastor'] = true;
    }

    /**
     * Fills in the variable addres.
     */
    private function hydrateAddres()
    {
        if (!isset($this->addres)) {
            if ($this->address_id !== null)
                $this->addres = $this->Addresses->get($this->address_id);
            else
                $this->addres = null;
        }
        $this->hydrated['address'] = true;
    }

    /**
     * Add a senior administrator to the church if it does not have one.
     *
     * @param User $user
     * @return void
     */
    public function addMainAdministrator(User $user, bool $validate = false)
    {
        $this->hydrateMainAdministrator();

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
        $this->hydratePastor();

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
            $this->hydrateMainAdministrator();
            $this->hydratePastor();
            $this->hydrateAddres();
        }

        unset($this->church_id);
        unset($this->address_id);
        unset($this->pastor_id);
        unset($this->main_administrator_id);
        unset($this->created_at);
        unset($this->updated_at);
        unset($this->pastor);

        $this->address = $this->addres;
        unset($this->addres);

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
