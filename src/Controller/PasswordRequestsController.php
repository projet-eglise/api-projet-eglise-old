<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\UsersTable;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\TableRegistry;

/**
 * PasswordRequests Controller
 *
 * @property \App\Model\Table\PasswordRequestsTable $PasswordRequests
 * @method \App\Model\Entity\PasswordRequest[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PasswordRequestsController extends AppController
{
    private UsersTable $Users;

    public function initialize(): void
    {
        parent::initialize();
        $this->Users = TableRegistry::getTableLocator()->get('Users');
    }

    public function request()
    {
        $user = $this->Users->findByEmail($this->request->getParam('mail'))->toArray();

        if (count($user) < 1) {
            throw new BadRequestException('Ce mail n\'est pas renseigné dans notre base');
        }

        $user = $user[0];

        $uid = uniqid();
        $existingRequest = $this->PasswordRequests->find('all', [
            'conditions' => ['expiration > ' => time(), 'user_id' => $user->user_id] 
        ])->toArray();

        if (count($existingRequest) > 0) {
            $newPasswordRequest = $this->PasswordRequests->newEntity([
                'uid' => $uid,
                'token' => sha1(Configure::read('Security.salt') . $uid . time()),
                'expiration' => time() + 3600,
                'user_id' => $user->user_id,
            ]);
    
            if (!empty($newPasswordRequest->getErrors())) {
                throw new BadRequestException('Impossible de génerer la demande');
            }
    
            if (!$this->PasswordRequests->save($newPasswordRequest)) {
                throw new BadRequestException('Impossible de sauvegarder la demande');
            }
        } else {
            $newPasswordRequest = $existingRequest[0];
        }

        // TODO Send an email

        $this->apiResponse();
    }
}
