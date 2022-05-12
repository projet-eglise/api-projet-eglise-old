<?php

/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return static function (RouteBuilder $routes) {
    /*
     * The default class to use for all routes
     *
     * The following route classes are supplied with CakePHP and are appropriate
     * to set as the default:
     *
     * - Route
     * - InflectedRoute
     * - DashedRoute
     *
     * If no call is made to `Router::defaultRouteClass()`, the class used is
     * `Route` (`Cake\Routing\Route\Route`)
     *
     * Note that `Route` does not do any inflections on URLs which will result in
     * inconsistently cased URLs when used with `{plugin}`, `{controller}` and
     * `{action}` markers.
     */
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder) {
        /*
         * Here, we are connecting '/' (base path) to a controller called 'Pages',
         * its action called 'display', and we pass a param to select the view file
         * to use (in this case, templates/Pages/home.php)...
         */
        $builder->post('/login', ['controller' => 'Authentication', 'action' => 'login']);
        $builder->post('/signin', ['controller' => 'Authentication', 'action' => 'signin']);
        $builder
            ->get('/password_request/request/{mail}', ['controller' => 'PasswordRequests', 'action' => 'request'])
            ->setPatterns(['mail' => '(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))']);
        $builder
            ->get('/password_request/check/{token}', ['controller' => 'PasswordRequests', 'action' => 'checkToken'])
            ->setPatterns(['token' => '[a-z0-9]+']);
        $builder->post('/password_request/change_password', ['controller' => 'PasswordRequests', 'action' => 'changePassword']);

        $builder->scope('', function (RouteBuilder $protectedRoutes) {
            $protectedRoutes->applyMiddleware('authentication');

            $protectedRoutes->get('/whoami', ['controller' => 'Authentication', 'action' => 'whoami']);

            $protectedRoutes->get('/churches', ['controller' => 'Churches', 'action' => 'index']);
            $protectedRoutes->get('/churches/forJoin', ['controller' => 'Churches', 'action' => 'getAllForJoin']);
            $protectedRoutes
                ->get('/church/{uid}', ['controller' => 'Churches', 'action' => 'view'])
                ->setPatterns(['uid' => '[a-z0-9]+']);
            $protectedRoutes->post('/church/add', ['controller' => 'Churches', 'action' => 'add']);
        });


        /*
         * Connect catchall routes for all controllers.
         *
         * The `fallbacks` method is a shortcut for
         *
         * ```
         * $builder->connect('/{controller}', ['action' => 'index']);
         * $builder->connect('/{controller}/{action}/*', []);
         * ```
         *
         * You can remove these routes once you've connected the
         * routes you want in your application.
         */
        $builder->fallbacks();
    });

    /*
     * If you need a different set of middleware or none at all,
     * open new scope and define routes there.
     *
     * ```
     * $routes->scope('/api', function (RouteBuilder $builder) {
     *     // No $builder->applyMiddleware() here.
     *
     *     // Parse specified extensions from URLs
     *     // $builder->setExtensions(['json', 'xml']);
     *
     *     // Connect API actions here.
     * });
     * ```
     */
};
