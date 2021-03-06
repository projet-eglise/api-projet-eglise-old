<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Table\RoleOptionsTable;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Role Entity
 *
 * @property int $role_id
 * @property string $uid
 * @property string $name
 * @property int $permission_id
 * @property int|null $service_id
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 */
class Role extends Entity
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
        'name' => true,
        'permission_id' => true,
        'service_id' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    private $hydrated = [
        'availableOptions' => false,
    ];

    private RoleOptionsTable $RoleOptions;

    /** List of options available for this role. */
    private array $availableOptions;

    public function __construct(array $properties = [], array $options = [])
    {
        parent::__construct($properties, $options);
        $this->RoleOptions = TableRegistry::getTableLocator()->get('RoleOptions');
    }

    /** 
     * Hydrate the availableOptions attribute. 
     * 
     * @return void
     */
    private function hydrateAvailableOptions(): void
    {
        if (!$this->hydrated['availableOptions']) {
            $this->availableOptions = $this->RoleOptions->find('all', ['conditions' => ['role_id' => $this->role_id]])->toArray();
            $this->hydrated['availableOptions'] = true;
        }
    }

    /**
     * Checks if this role needs an option or not.
     * 
     * @return boolean
     */
    public function needOption(): bool
    {
        $this->hydrateAvailableOptions();
        return count($this->availableOptions) > 0;
    }

    /**
     * Checks if an option belongs to a role or not.
     *
     * @param RoleOption $roleOption
     * @return boolean
     */
    public function isAnOption(RoleOption $roleOptionToCheck)
    {
        $this->hydrateAvailableOptions();

        foreach ($this->availableOptions as $roleOption)
            if ($roleOption->role_option_id === $roleOptionToCheck->role_option_id)
                return true;

        return false;
    }

    /**
     * Unset the variables needed for an api return.
     *
     * @return Role
     */
    public function toApi(): Role
    {
        unset($this->role_id);
        unset($this->service_id);

        if (isset($this->role_options) && !empty($this->role_options))
            foreach ($this->role_options as $role_option)
                $role_option->toApi();

        return $this;
    }
}
