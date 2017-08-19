<?php

namespace app\controllers;

use app\models\GuestInfo;
use app\core\AccessChecker;

class GuestController extends BaseController {

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
        $info = new GuestInfo();
        $this->appendVariable('data', $info->buildInfo());
        $this->appendVariable('proxy_is_used', $info->isProxyUsed());
        $this->appendVariable('proxy_header', $info->detectedProxyHeader);
        
        $ipInfo = $info->getIpInfo();
        $this->appendVariable('user_ip_info', $ipInfo);
        if (is_array($ipInfo) && isset($ipInfo['latitude']) && isset($ipInfo['longitude']))
        {
            $this->appendVariable('timezone', 
                $info->getNearestTimezone($ipInfo['latitude'], $ipInfo['longitude']));
        }
        
        $this->appendVariable('is_tor_user', $info->isTorUser());
        $this->appendVariable('www_root', $this->getHttpRootPath());
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
