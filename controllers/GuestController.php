<?php

namespace ipinfo\controllers;

use ipinfo\models\GuestInfo;
use mmvc\core\AccessChecker;
use mmvc\controllers\WebController;

class GuestController extends WebController {

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
        $currentTime = time();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (array_key_exists('fingerPrints', $_COOKIE))
        {
            if (!empty($_SESSION['fingerPrints'])) {
                if (!in_array($_COOKIE['fingerprint'], $_SESSION['fingerPrints']))
                {
                    array_push($_SESSION['fingerPrints'], $_COOKIE['fingerprint']);
                }
            } else {
                $_SESSION['fingerPrints'] = [$_COOKIE['fingerprint']];
            }
        }

        if (!empty($_SESSION['visitCount'])) {
            $_SESSION['visitCount'] ++;
        } else {
            $_SESSION['visitCount'] = 1;
        }
        if (!empty($_SESSION['serverTime'])) {
            $_SESSION['lastVisit'] = $currentTime - $_SESSION['serverTime'];
        }
        $_SESSION['serverTime'] = $currentTime;
        session_commit();
        setcookie('lastVisit', (int)$_SESSION['lastVisit']);
        setcookie('fingerPrints', htmlspecialchars(implode("\n", $_SESSION['fingerPrints'])));
        
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
        
        $data['visitCount']  = $_SESSION['visitCount'];
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
        $result = ['error' => 0, 'using' => false, 'port' => 0];
        $ip = GuestInfo::getRemoteIp();
        try
        {
            $result['using'] = GuestInfo::isProxyPortOpened($ip);
            $result['port'] = GuestInfo::$detectedProxyPort;
        } 
        catch (\Exception $ex) 
        {
            $result['using'] = false;
            $result['error'] = $ex->getMessage();
        }
        
        return json_encode($result);
    }
}
