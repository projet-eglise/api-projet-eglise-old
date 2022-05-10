<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

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
}
