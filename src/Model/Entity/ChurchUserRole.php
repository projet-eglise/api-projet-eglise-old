<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChurchUserRole Entity
 *
 * @property int $church_user_role_id
 * @property int $church_user_id
 * @property int $role_id
 * @property int $role_option_id
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 */
class ChurchUserRole extends Entity
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
        'church_user_id' => true,
        'role_id' => true,
        'role_option_id' => true,
        'created_at' => true,
        'updated_at' => true,
    ];
}
