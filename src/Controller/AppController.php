<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use App\Model\Entity\Log;
use App\Model\Entity\User;
use App\Model\Table\LogsTable;
use App\Model\Table\UsersTable;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Connection;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    private LogsTable $Logs;
    private UsersTable $Users;

    protected User $connectedUser;
    private static Log $log;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Authentication');

        $this->Logs = TableRegistry::getTableLocator()->get('Logs');
        $this->Users = TableRegistry::getTableLocator()->get('Users');

        $this->log = $this->Logs->newEntity([
            'uid' => uniqid(),
            'start_timestamp' => microtime(true) * 10000,
        ]);
    }

    /**
     * beforeFilter callback.
     *
     * @param \Cake\Event\EventInterface $event Event.
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event)
    {
        /** @var Connection */
        $connection = ConnectionManager::get('default');
        $connection->begin();

        $token = $this->request->getSession()->read('token');
        if (isset($token)) {
            $this->connectedUser = $this->Users->findByUid($this->Authentication->getTokenContent($token)['user']['uid'])->first();
            $this->log->user_id = $this->connectedUser->user_id;
        } else {
            $this->request->getSession()->delete('user');
        }
    }

    /**
     * Sends a json response with the variables you want to send.
     *
     * @param array $data
     */
    protected function apiResponse(array $data = [])
    {
        /** @var Connection */
        $connection = ConnectionManager::get('default');
        $connection->commit();
        $this->sendResponse(200, $data);
    }

    /**
     * Sends an error response with the variables you want to send.
     *
     * @param integer $statusCode
     * @param array $data
     */
    protected function errorResponse(int $code, array $data = [])
    {
        /** @var Connection */
        $connection = ConnectionManager::get('default');
        $connection->rollback();
        $this->sendResponse($code, $data);
    }

    /**
     * Sends a json response with the variables you want to send.
     *
     * @param integer $statusCode
     * @param array $data
     */
    private function sendResponse(int $statusCode, array $data = [])
    {
        $response['code'] = $statusCode;
        $response['message'] = (new Response(['status' => $statusCode]))->getReasonPhrase();

        $params = $this->getRequest()->getData() ?? [];

        if (isset($params['password']))
            unset($params['password']);

        $this->log->params = json_encode($params);
        $this->log->route = $this->getRequest()->getUri()->__toString();
        $this->log->method = $this->getRequest()->getMethod();
        $this->log->ip_address = $this->getRequest()->clientIp();
        $this->log->end_timestamp = microtime(true) * 10000;
        $this->log->response_code = $statusCode;

        if ($statusCode === 200) {
            $response = $this->okResponseBuilder($response, $data);
            $this->log->response = json_encode($data);
        } else {
            $this->log->error_log = $this->Logs->ErrorLogs->newEntity([
                'uid' => uniqid(),
                'file' => str_replace(ROOT . DS, '', $data['file']),
                'line' => $data['line'],
                'viewed' => false,
                'traces' => [],
                'error' => [
                    'uid' => uniqid(),
                    'code' => $statusCode,
                    'error' => 'Bla bla bla bla',
                ]
            ]);

            foreach ($data['trace'] as $trace) {
                $trace['file'] = str_replace(ROOT . DS, '', $trace['file']);
                $this->log->error_log->traces[] = $this->Logs->ErrorLogs->Traces->newEntity(array_merge([
                    'uid' => uniqid(),
                ], $trace));
            }

            $response = $this->errorResponseBuilder($response, ['error' => $data['error']]);
            $this->log->response = json_encode($data);
        }

        $this->Logs->save($this->log);

        $this->set($response);

        $this->viewBuilder()
            ->setClassName('Json')
            ->setOption('serialize', array_keys($response));
    }

    /**
     * Data processing when the answer is an error.
     *
     * @param array $response
     * @param array $data
     * @return array
     */
    private function errorResponseBuilder(array $response, array $data = []): array
    {
        return array_merge($response, $data);
    }

    /**
     * Data processing when the answer is ok.
     *
     * @param array $response
     * @param array $data
     * @return array
     */
    private function okResponseBuilder(array $response, array $data = []): array
    {
        $response['data'] = $data;
        return $response;
    }
}
