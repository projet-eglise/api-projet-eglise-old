<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Church Entity
 *
 * @property int $church_id
 * @property string $name
 * @property string $uid
 * @property int $pastor_id
 * @property int $main_administrator_id
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 */
class Church extends Entity
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
        'created_at' => true,
        'updated_at' => true,
    ];
}
