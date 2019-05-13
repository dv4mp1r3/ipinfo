<?php

namespace ipinfo\controllers;

use ipinfo\models\GuestInfo;
use mmvc\core\AccessChecker;
use mmvc\controllers\WebController;

class GuestController extends WebController {

    const KEY_FP = 'fingerPrints';

    const KEY_VISITS = 'visitCount';

    public function __construct() {
        $this->rules = [
            'info' => [
            AccessChecker::RULE_GRANTED => AccessChecker::USER_ALL,
            ],
        ];
        parent::__construct();
    }
    
    public function actionInfo()
    {
        $keyServerTime = 'serverTime';
        $keyFingerprint = 'fingerprint';
        $keyLastVisit = 'lastVisit';
        $currentTime = time();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (array_key_exists($keyFingerprint, $_COOKIE))
        {
            if (!empty($_SESSION[self::KEY_FP])) {
                if (!in_array($_COOKIE[$keyFingerprint], $_SESSION[self::KEY_FP]))
                {
                    array_push($_SESSION[self::KEY_FP], $_COOKIE[$keyFingerprint]);
                }
            } else {
                $_SESSION[self::KEY_FP] = [$_COOKIE[$keyFingerprint]];
            }
        }

        if (!empty($_SESSION[self::KEY_VISITS])) {
            $_SESSION[self::KEY_VISITS] ++;
        } else {
            $_SESSION[self::KEY_VISITS] = 1;
        }
        if (!empty($_SESSION[$keyServerTime])) {
            $_SESSION[$keyLastVisit] = $currentTime - $_SESSION[$keyServerTime];
        }
        $_SESSION['serverTime'] = $currentTime;
        session_commit();
        if (array_key_exists($keyLastVisit, $_SESSION))
        {
            setcookie($keyLastVisit, (int)$_SESSION[$keyLastVisit]);
        }

        if (array_key_exists(self::KEY_FP, $_SESSION))
        {
            setcookie(self::KEY_FP, htmlspecialchars(implode("\n", $_SESSION[self::KEY_FP])));
        }

        
        $info = new GuestInfo();
        $ipInfo = $info->getIpInfo();
        $data = $info->buildInfo();
              
        $data['location'] = $ipInfo;
        if (is_array($ipInfo) && isset($ipInfo['latitude']) && isset($ipInfo['longitude']))
        { 
            $timezone = $info->getNearestTimezone($ipInfo['latitude'], $ipInfo['longitude']);
            $data['timezone'] = $timezone;
        }
       
        $this->appendVariable('www_root', $this->getHttpRootPath());
        
        $data[self::KEY_VISITS]  = $_SESSION[self::KEY_VISITS];
        $data['remoteIP']  = $info::getRemoteIp();
        $data['isTorUsed']   = $info->isTorUser();
        $data['isProxyUsed'] = $info->isProxyUsed();
        $data['proxyHeader']  = $info->detectedProxyHeader;
               
        $data = \ipinfo\helpers\VarDumper::getData($data, 'server');
        $this->appendVariable('data', $data);
        $this->render('info');       
    }
    
    public function  actionProxyport()
    {
        $keyUsing = 'using';
        $result = ['error' => 0, $keyUsing => false, 'port' => 0];
        $ip = GuestInfo::getRemoteIp();
        try
        {
            $result[$keyUsing] = GuestInfo::isProxyPortOpened($ip);
            $result['port'] = GuestInfo::$detectedProxyPort;
        } 
        catch (\Exception $ex) 
        {
            $result[$keyUsing] = false;
            $result['error'] = $ex->getMessage();
        }
        
        return json_encode($result);
    }
}
