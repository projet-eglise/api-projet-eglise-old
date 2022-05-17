<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RoleOption Entity
 *
 * @property int $role_option_id
 * @property string $uid
 * @property string $name
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 *
 * @property \App\Model\Entity\Role $role
 */
class RoleOption extends Entity
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
        'role_id' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    /**
     * Unset the variables needed for an api return.
     *
     * @return Role
     */
    public function toApi(): RoleOption
    {
        unset($this->role_id);
        unset($this->role_option_id);

        return $this;
    }
}
