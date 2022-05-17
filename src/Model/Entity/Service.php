<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Service Entity
 *
 * @property int $service_id
 * @property string $uid
 * @property string $name
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 */
class Service extends Entity
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
        'created_at' => true,
        'updated_at' => true,
    ];

    /**
     * Unset the variables needed for an api return.
     *
     * @return Service
     */
    public function toApi(): Service
    {
        unset($this->service_id);

        if(isset($this->roles) && !empty($this->roles))
            foreach ($this->roles as $role)
                $role->toApi();

        return $this;
    }
}
