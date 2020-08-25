<?php


namespace ipinfo\models;


class TorDetect
{
    /**
     * Определение использования TOR клиентом
     * @param string $ip
     * @return bool
     */
    public static function isTorUser(string $ip) : bool
    {
        $res = gethostbyname(self::ReverseIPOctets($ip) .
                "." . $_SERVER['SERVER_PORT'] .
                "." . self::ReverseIPOctets($_SERVER['SERVER_ADDR']) .
                ".ip-port.exitlist.torproject.org") == "127.0.0.2";
        return $res !== false;
    }

    private static function ReverseIPOctets($ip)
    {
        $ipOct = explode(".", $ip);
        if (count($ipOct) !== 4) {
            return '';
        }
        return $ipOct[3] . "." . $ipOct[2] . "." . $ipOct[1] . "." . $ipOct[0];
    }
}