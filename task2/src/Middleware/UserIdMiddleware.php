<?php

namespace AddressFinder\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class UserIdMiddleware
{
    public function __invoke(ServerRequestInterface $request, RequestHandler $handler): ResponseInterface
    {
        $sessionId = session_id();

        $request = $request->withAttribute('userId', $sessionId);

        return $handler->handle($request);
    }
}