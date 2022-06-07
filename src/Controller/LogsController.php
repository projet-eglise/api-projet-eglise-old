<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Logs Controller
 *
 * @property \App\Model\Table\LogsTable $Logs
 * @method \App\Model\Entity\Log[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LogsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $logs = $this->Logs->find('all', [/* 'limit' => '100',  */'order' => ['start_timestamp' => 'DESC']])->toArray();

        foreach ($logs as $log)
            $log->toApi();

        $this->apiResponse($logs);
    }
}
