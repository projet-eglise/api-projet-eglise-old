<?php

namespace App\Middleware;

use App\Controller\Component\AuthenticationComponent;
use Cake\Http\Exception\UnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Exception\BadRequestException;

class AuthenticationMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authorizationHeader = $request->getHeader('AUTHORIZATION')[0] ?? '';
        if ($authorizationHeader === '') {
            throw new UnauthorizedException('No authorization header.');
        }

        if (strpos($authorizationHeader, 'Bearer ') === false) {
            throw new BadRequestException('Use Bearer token.');
        }

        $Authentication = new AuthenticationComponent(new ComponentRegistry());

        $token = str_replace('Bearer ', '', $authorizationHeader);
        if (!$Authentication->checkJwt($token)) {
            throw new UnauthorizedException('Use the token you were given.');
        }

        $request->getSession()->write('token', $token);

        return $handler->handle($request);
    }
}
