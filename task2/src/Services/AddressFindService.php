<?php

namespace AddressFinder\Services;

use AddressFinder\Itegrations\AddressDataProvider\AddressDataProviderInterface;
use AddressFinder\Repository\UserRequestRepository;
use Psr\Http\Client\ClientInterface;

class AddressFindService
{
    public function __construct (
        private AddressDataProviderInterface $dataProvider,
        private UserRequestRepository $userRequestRepository,
    ) { }

    public function getData(string $address, string $userId): array
    {
        $data = $this->dataProvider->get($address);

        if (!$this->userRequestRepository->get($address, $userId)) {
            $this->userRequestRepository->save($address, $userId);
        }

        return $data;
    }

}