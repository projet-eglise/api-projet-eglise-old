<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Trace Entity
 *
 * @property int $trace_id
 * @property string $uid
 * @property int|null $error_log_id
 * @property string $file
 * @property int $line
 * @property string $class
 * @property string $type
 * @property string $function
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 */
class Trace extends Entity
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
        'error_log_id' => true,
        'file' => true,
        'line' => true,
        'class' => true,
        'type' => true,
        'function' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    public function equals(Trace $trace)
    {
        return
            $trace->trace_id === $this->trace_id
            && $trace->uid === $this->uid
            && $trace->error_log_id === $this->error_log_id
            && $trace->file === $this->file
            && $trace->line === $this->line
            && $trace->class === $this->class
            && $trace->type === $this->type
            && $trace->function === $this->function;
    }
}
