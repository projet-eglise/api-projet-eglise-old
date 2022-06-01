<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Log Entity
 *
 * @property int $log_id
 * @property string $uid
 * @property int|null $user_id
 * @property string $ip_address
 * @property string $method
 * @property string $route
 * @property string $params
 * @property int $response_code
 * @property string|null $response
 * @property string|null $file
 * @property string|null $trace
 * @property int $start_timestamp
 * @property int $end_timestamp
 * @property bool $viewed
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 */
class Log extends Entity
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
        'user_id' => true,
        'ip_address' => true,
        'method' => true,
        'route' => true,
        'params' => true,
        'response_code' => true,
        'response' => true,
        'file' => true,
        'trace' => true,
        'start_timestamp' => true,
        'end_timestamp' => true,
        'viewed' => true,
        'created_at' => true,
        'updated_at' => true,
    ];
}
