<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ErrorLog Entity
 *
 * @property int $error_log_id
 * @property string $uid
 * @property int|null $error_id
 * @property string $file
 * @property int $line
 * @property bool $viewed
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 *
 * @property \App\Model\Entity\Trace[] $traces
 * @property \App\Model\Entity\Error $error
 */
class ErrorLog extends Entity
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
        'error_id' => true,
        'file' => true,
        'line' => true,
        'viewed' => true,
        'created_at' => true,
        'updated_at' => true,
        'traces' => true,
        'error' => true,
    ];

    public function equals(ErrorLog $errorLog)
    {
        return
            $errorLog->error_log_id === $this->error_log_id
            && $errorLog->uid === $this->uid
            && $errorLog->error_id === $this->error_id
            && $errorLog->file === $this->file
            && $errorLog->line === $this->line
            && $errorLog->viewed === $this->viewed;
    }
}
