<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Error Entity
 *
 * @property int $error_id
 * @property string $uid
 * @property int $code
 * @property string $error
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 */
class Error extends Entity
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
        'code' => true,
        'error' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    public function equals(Error $error)
    {
        return
            $error->error_id === $this->error_id
            && $error->uid === $this->uid
            && $error->code === $this->code
            && $error->error === $this->error;
    }
}
