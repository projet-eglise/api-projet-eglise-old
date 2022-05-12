<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\UsersTable;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
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
            $newPasswordRequest = $existingRequest[0];
        } else {
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
        }

        // TODO Send an email

        $this->apiResponse();
    }

    public function checkToken()
    {
        $passwordRequest = $this->PasswordRequests->findByToken($this->request->getParam('token'))->toArray();

        if (count($passwordRequest) < 1) {
            throw new NotFoundException('Cette demande a expiré');
        }

        $this->apiResponse();
    }

    public function changePassword()
    {
        $passwordRequest = $this->PasswordRequests->findByToken($this->request->getData('token'))->toArray();
        $password = $this->request->getData('password');

        if (count($passwordRequest) < 1) {
            throw new NotFoundException('Cette demande a expiré');
        }
        $passwordRequest = $passwordRequest[0];

        if (is_null($password)) {
            throw new BadRequestException('Mot de passe non renseignée.');
        }

        if (!preg_match('^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&\^])[A-Za-z\d@$!%*#?&\^]{8,}$^', $password)) {
            throw new BadRequestException('Mot de passe non conforme.');
        }

        $user = $this->Users->get($passwordRequest->user_id);
        $user->password = $this->Authentication->hashPassword($password);

        if (!empty($user->getErrors())) {
            throw new BadRequestException('Une erreur est survenue.');
        }

        if (!$this->Users->save($user)) {
            throw new BadRequestException('Une erreur est survenue.');
        }

        $this->PasswordRequests->delete($passwordRequest);

        $this->apiResponse();
    }
}
