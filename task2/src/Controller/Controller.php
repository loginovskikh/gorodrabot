<?php

namespace AddressFinder\Controller;

use AddressFinder\Services\AddressFindService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class Controller
{
    public function __construct(
        private Twig $view,
        private AddressFindService $addressFindService
    )
    {}

    public function showForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->view->render($response, 'index.twig');
    }

    public function search(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();
        $userId = $request->getAttribute('userId');

        if (!$userId) {
            throw new \DomainException('Не удалось определить пользователя');
        }

        if (!isset($data['address'])) {
            throw new \DomainException('Не указан адрес');
        }

        $address = trim($data['address']);
        if (!$address) {
            throw new \DomainException('Заполните адрес');
        }

        $responseData = $this->addressFindService->getData($address, $userId);
        $responseData = (string)json_encode($responseData);
        $response->getBody()->write($responseData);

        return $response->withHeader('Content-Type', 'application/json');
    }
}