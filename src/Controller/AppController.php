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

use App\Model\Table\UsersTable;
use Cake\Controller\Controller;
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
    private UsersTable $Users;

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

        $this->Users = TableRegistry::getTableLocator()->get('Users');


        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
    }

    /**
     * Sends a json response with the variables you want to send.
     *
     * @param array $data
     */
    protected function apiResponse(array $data = [])
    {
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

        $response = $statusCode === 200 ?
            $this->okResponseBuilder($response, $data) :
            $this->errorResponseBuilder($response, $data);

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

    /**
     * Returns the id of the connected user.
     *
     * @return integer
     */
    protected function getUserId(): int
    {
        return $this->Users->findByUid($this->Authentication->getTokenContent($this->request->getSession()->read('token'))['user']['uid'])->toArray()[0]->user_id;
    }
}
