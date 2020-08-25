<?php

namespace ipinfo\models;

use DateTimeZone;
use mmvc\models\BaseModel;

class GuestInfo extends BaseModel {

    const KEY_REMOTE_ADDR = 'REMOTE_ADDR';

    private $data;

    /**
     * Список возможных заголовков, которые могут передавать прокси-сервера
     * @var array 
     */
    private $proxy_headers = [
        'HTTP_VIA',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_FORWARDED',
        'HTTP_CLIENT_IP',
        'HTTP_FORWARDED_FOR_IP',
        'VIA',
        'X_FORWARDED_FOR',
        'FORWARDED_FOR',
        'X_FORWARDED',
        'FORWARDED',
        'CLIENT_IP',
        'FORWARDED_FOR_IP',
        'HTTP_PROXY_CONNECTION'
    ];
    
    private $except_headers = [
        'HTTP_X_SERVER_ADDR',
        'HTTP_X_REAL_IP', 
        'HTTP_UPGRADE_INSECURE_REQUESTS',
    ];

    /**
     * Найденный заголовок о прокси
     * @var string 
     */
    public $detectedProxyHeader;

    /**
     * список портов, которые чаще всего используются прокси-серверами
     * @var array 
     */
    static $proxyPorts = [8080, 80, 81, 1080, 6588, 8000, 3128, 553, 554, 4480];

    /**
     * открытый у клиента порт из списка $proxyPorts
     * @var integer 
     */
    static $detectedProxyPort = 0;

    /**
     * Время на ожидание подключения к порту в секундах
     */
    const FSOCKOPEN_TIMEOUT_SEC = 5;

    public function __construct() {
        parent::__construct();
        $this->data = array();
    }

    public function buildInfo() {
        $this->getHttpHeaders();
        return $this->data;
    }

    public function getHttpHeaders() {
        $httpData = array();
        foreach ($_SERVER as $key => $v)
        {
            if (strpos(strtoupper($key), 'HTTP_') === 0 && !in_array($key, $this->except_headers))
            {
                $httpData[htmlspecialchars($key)] = htmlspecialchars ($v);
            }
        }


        $this->data['http_data'] = $httpData;
        return $this->data['http_data'];
    }

    /**
     * Получение IP адреса клиента
     * @return string строка с IP или пустая строка
     */
    public static function getRemoteIp() {
        $localIP = ['127.0.0.1', '::1'];
        return empty($_SERVER[self::KEY_REMOTE_ADDR]) && !in_array($_SERVER[self::KEY_REMOTE_ADDR], $localIP)
            ? $_SERVER[self::KEY_REMOTE_ADDR]
            : '';
    }

    /**
     * Получение информации об IP клиента через php_geoip
     * @return array
     * @throws \Exception выбрасывается если нельзя определить IP клиента
     * или если php-geoip не загружен
     */
    public function getIpInfo() {
        $ip = GeoIp::getRemoteIp();
        if ($ip === null || $ip === '') {
            throw new \Exception('can not to get client ip');
        }

        return (new GeoIp())->toArray($ip);
    }

    /**
     * Определение использования клиентов прокси по передаваемым заголовкам
     * Не работает для анонимных прокси
     * @return boolean
     */
    public function isProxyUsed() {
        foreach ($this->proxy_headers as $header) {
            if (isset($_SERVER[$header])) {
                $this->detectedProxyHeader = $header;
                return true;
            }
        }
        return false;
    }

    /**
     * Определение открытого порта на IP адресе клиента,
     * специфичного для прокси-серверов.
     * Найденный порт записывается в GuestInfo::detectedProxyPort
     * @param string $ip адрес клиента (при передаче null берется из
     * $_SERVER['REMOTE_ADDR'])
     * @return boolean
     * @throws \Exception выбрасывается при невозможности получить адрес клиента
     */
    public static function isProxyPortOpened($ip = null) {
        if ($ip === null) {
            if (!isset($_SERVER[self::KEY_REMOTE_ADDR])) {
                throw new \Exception('ip is empty or not set');
            }
            else {
                $ip = $_SERVER[self::KEY_REMOTE_ADDR];
            }
        }

        foreach (self::$proxyPorts as $port) {
            if (@fsockopen($ip, $port, $errno, $errstr, self::FSOCKOPEN_TIMEOUT_SEC)) {
                self::$detectedProxyPort = $port;
                return true;
            }
        }

        return false;
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
