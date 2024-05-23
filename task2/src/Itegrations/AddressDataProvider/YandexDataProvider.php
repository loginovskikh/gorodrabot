<?php

namespace AddressFinder\Itegrations\AddressDataProvider;

use Psr\Http\Client\ClientInterface;

class YandexDataProvider implements AddressDataProviderInterface
{

    public function __construct(
        private ClientInterface $client,
        private string $apiHost,
        private string $apiKey,
    ){}
    public function get(string $address): array
    {
        $address = trim($address);
        if (!$address) {
            return [];
        }

        $requestData = [
            'query' => [
                'apikey' => $this->apiKey,
                'geocode' => $address,
                'lang' => 'ru_RU',
                'format' => 'json',
                'results' => 5,
            ],
        ];

        $response = $this->client->get($this->apiHost, $requestData);

        if ($response->getStatusCode() !== 200) {
            throw new \DomainException('Ошибка при обращении к сервису геокодирования');
        }

        $data = json_decode($response->getBody(), true);
        $this->validateAddressRequest($data);

        $result = [
            'street' => $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['Thoroughfare']['ThoroughfareName'],
            'house' => $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['Thoroughfare']['Premise']['PremiseNumber'],
        ];

        $district = $this->getDistrict($data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos']);
        $metro = $this->getMetro($data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos']);

        $result['district'] = $district;
        $result['metro'] = $metro;

        return $result;
    }

    private function validateAddressRequest(array $responseData)
    {
        if (!isset($responseData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'])) {
            throw new \DomainException('Не удалось определить адрес');
        }

        if (!isset($responseData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['AdministrativeAreaName'])) {
            throw new \DomainException('Не удалось определить город');
        }

        if (($responseData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['AdministrativeAreaName'] !== 'Москва')) {
            throw new \DomainException('Указанный адрес не относится к Москве');
        }
        if (!isset($responseData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['Thoroughfare']['ThoroughfareName'])) {
            throw new \DomainException('Не удалось определить улицу');
        }
        if (!isset($responseData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['Thoroughfare']['Premise']['PremiseNumber'])) {
            throw new \DomainException('Не удалось определить дом');
        }
    }

    private function getDistrict(string $pos): array
    {
        $districts = [];

        $requestData = [
            'query' => [
                'apikey' => $this->apiKey,
                'geocode' => $pos,
                'lang' => 'ru_RU',
                'format' => 'json',
                'results' => 5,
                'kind' => 'district'
            ],
        ];

        $response = $this->client->get($this->apiHost, $requestData);

        if ($response->getStatusCode() !== 200) {
            throw new \DomainException('Ошибка при обращении к сервису геокодирования');
        }

        $data = json_decode($response->getBody(), true);
        if ($data && is_array($data)) {
            if (isset($data['response']['GeoObjectCollection']['featureMember'])) {
                foreach ($data['response']['GeoObjectCollection']['featureMember'] as $geoObject) {
                    if (isset($geoObject['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['DependentLocality']['DependentLocalityName'])){
                        $districts[] = $geoObject['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['DependentLocality']['DependentLocalityName'];
                    }
                }
            }
        }

        return array_unique($districts);
    }

    private function getMetro(string $pos): array
    {
        $metro = [];

        $requestData = [
            'query' => [
                'apikey' => $this->apiKey,
                'geocode' => $pos,
                'lang' => 'ru_RU',
                'format' => 'json',
                'results' => 5,
                'kind' => 'metro'
            ],
        ];

        $response = $this->client->get($this->apiHost, $requestData);

        if ($response->getStatusCode() !== 200) {
            throw new \DomainException('Ошибка при обращении к сервису геокодирования');
        }

        $data = json_decode($response->getBody(), true);
        if ($data && is_array($data)) {
            if (isset($data['response']['GeoObjectCollection']['featureMember'])) {
                foreach ($data['response']['GeoObjectCollection']['featureMember'] as $geoObject) {
                    if (isset($geoObject['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['Thoroughfare'])){
                        $metro[] = $geoObject['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['Thoroughfare']['ThoroughfareName']
                            . ', ' . $geoObject['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['Thoroughfare']['Premise']['PremiseName'];
                    }
                }
            }
        }

        return array_unique($metro);
    }
}