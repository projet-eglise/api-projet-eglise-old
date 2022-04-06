<?php
declare(strict_types=1);

namespace App\Controller;

class HomeController extends AppController
{
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

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
    }

    /**
     * Sends a json response with the variables you want to send.
     *
     * @param integer $statusCode
     * @param array $data
     */
    public function index()
    {
        $this->apiResponse(['hello' => 'world']);
    }
}
