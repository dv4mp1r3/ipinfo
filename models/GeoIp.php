<?php

declare(strict_types=1);

namespace ipinfo\models;

use GeoIp2\Database\Reader;
use DateTimeZone;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;

class GeoIp implements IConvertibleToArray
{
    const KEY_REMOTE_ADDR = 'REMOTE_ADDR';

    protected string $ip;

    protected Reader $city;

    protected Reader $asn;

    public function __construct(string $ip, string $geoipCityPath = '', string $geoipAsnPath = '')
    {
        $this->ip = $ip;
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
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function toArray() : array
    {
        $city = $this->city->city($this->ip);
        $asn =  $this->asn->asn($this->ip);

        $record = [
            'city' => $city->city->name,
            'latitude' => $city->location->latitude,
            'longitude' => $city->location->longitude,
            'provider' => $asn->autonomousSystemOrganization,
            'timezone' => $this->getNearestTimezone($city->location->latitude, $city->location->longitude),
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

    /**
     * Определение ближайшей зоны по координатам
     * @param integer $latitude
     * @param integer $longitude
     * @param string $countryCode
     * @return string
     */
    public function getNearestTimezone($latitude, $longitude, $countryCode = '') {
        $timezone_ids = ($countryCode) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $countryCode) : DateTimeZone::listIdentifiers();

        if ($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

            $time_zone = '';
            $tz_distance = 0;
            if (count($timezone_ids) == 1) {
                $time_zone = $timezone_ids[0];
            } else {

                foreach ($timezone_ids as $timezone_id) {
                    $timezone = new DateTimeZone($timezone_id);
                    $location = $timezone->getLocation();
                    $tz_lat = $location['latitude'];
                    $tz_long = $location['longitude'];

                    $theta = $longitude - $tz_long;
                    $distance = (sin(deg2rad($latitude)) * sin(deg2rad($tz_lat))) + (cos(deg2rad($latitude)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
                    $distance = acos($distance);
                    $distance = abs(rad2deg($distance));

                    if (!$time_zone || $tz_distance > $distance) {
                        $time_zone = $timezone_id;
                        $tz_distance = $distance;
                    }
                }
            }
            return $time_zone;
        }
        return 'unknown';
    }
}