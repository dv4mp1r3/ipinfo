<?php

declare(strict_types=1);

namespace ipinfo\models;

use GeoIp2\Database\Reader;

class GeoIp
{
    const KEY_REMOTE_ADDR = 'REMOTE_ADDR';

    protected string $ip;

    protected Reader $city;

    protected Reader $asn;

    public function __construct(string $geoipCityPath = '', string $geoipAsnPath = '')
    {
        $this->initReaders($geoipCityPath, $geoipAsnPath);
    }

    protected function initReaders(string $geoipCityPath, string $geoipAsnPath)
    {
        $this->city = new Reader($geoipCityPath === '' ? GEOIP_CITY_PATH : $geoipCityPath);
        $this->asn = new Reader($geoipAsnPath === '' ? GEOIP_ASN_PATH : $geoipAsnPath);
    }

    /**
     * @param string $ip
     * @return array
     * @throws \GeoIp2\Exception\AddressNotFoundException
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public function toArray(string $ip) : array
    {
        $city = $this->city->city($ip);
        $asn =  $this->asn->asn($ip);

        $record = [
            'city' => $city->city->name,
            'latitude' => $city->location->latitude,
            'longitude' => $city->location->longitude,
            'provider' => $asn->autonomousSystemOrganization,
        ];
        return $record;
    }

    public static function getRemoteIp() : string
    {
        $localIP = ['127.0.0.1', '::1'];
        return empty($_SERVER[self::KEY_REMOTE_ADDR]) && !in_array($_SERVER[self::KEY_REMOTE_ADDR], $localIP)
            ? $_SERVER[self::KEY_REMOTE_ADDR]
            : '';
    }
}