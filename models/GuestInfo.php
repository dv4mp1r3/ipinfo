<?php

namespace ipinfo\models;

use mmvc\models\BaseModel;

class GuestInfo extends BaseModel implements IConvertibleToArray {

    const KEY_REMOTE_ADDR = 'REMOTE_ADDR';

    private array $data;

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

    public function toArray() : array {
        $this->getHttpHeaders();
        return [
            'http_data' => $this->getHttpHeaders(),
            'proxyHeader' => $this->detectedProxyHeader,
            'isProxyUsed' => $this->isProxyUsed(),
        ];
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

}
