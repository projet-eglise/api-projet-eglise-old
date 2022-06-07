<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Interfaces\ApiRessource;
use App\Model\Table\UsersTable;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Log Entity
 *
 * @property int $log_id
 * @property string $uid
 * @property int|null $user_id
 * @property int|null $error_log_id
 * @property string $ip_address
 * @property string $method
 * @property string $route
 * @property string $params
 * @property int $response_code
 * @property string|null $response
 * @property int $start_timestamp
 * @property int $end_timestamp
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 *
 * @property \App\Model\Entity\ErrorLog $error_log
 * @property \App\Model\Entity\User $user
 */
class Log extends Entity implements ApiRessource
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
        'error_log_id' => true,
        'ip_address' => true,
        'method' => true,
        'route' => true,
        'params' => true,
        'response_code' => true,
        'response' => true,
        'start_timestamp' => true,
        'end_timestamp' => true,
        'created_at' => true,
        'updated_at' => true,
        'error_log' => true,
        'user' => true,
    ];

    private UsersTable $Users;

    public function __construct(array $properties = [], array $options = [])
    {
        parent::__construct($properties, $options);
        $this->Users = TableRegistry::getTableLocator()->get('Users');
    }

    /** 
     * Hydrate the user attribute. 
     * 
     * @return void
     */
    private function hydrateUser(): void
    {
        if (!isset($this->user) && isset($this->user_id))
            $this->user = $this->Users->get($this->user_id);
    }

    protected function setUser(User $user)
    {
        if ($user->user_id !== null) $this->user_id = $user->user_id;
        return $user;
    }

    public function equals(Log $log)
    {
        return
            $log->log_id === $this->log_id
            && $log->uid === $this->uid
            && $log->user_id === $this->user_id
            && $log->error_log_id === $this->error_log_id
            && $log->ip_address === $this->ip_address
            && $log->method === $this->method
            && $log->route === $this->route
            && $log->params === $this->params
            && $log->response_code === $this->response_code
            && $log->response === $this->response
            && $log->start_timestamp === $this->start_timestamp
            && $log->end_timestamp === $this->end_timestamp;
    }

    /**
     * Unset the variables needed for an api return.
     *
     * @return Log
     */
    public function toApi(): Log
    {
        $this->hydrateUser();

        if (isset($this->response) && !($this->response instanceof string) && $this->response !== "")
            $this->response = stream_get_contents($this->response);
        unset($this->log_id);
        unset($this->error_log_id);
        unset($this->user_id);
        unset($this->created_at);
        unset($this->updated_at);
        if (isset($this->user))
            $this->user->toApi();

        return $this;
    }
}
