<?php

namespace AddressFinder\Itegrations\AddressDataProvider;

interface AddressDataProviderInterface
{
    public function get(string $address): array;
}