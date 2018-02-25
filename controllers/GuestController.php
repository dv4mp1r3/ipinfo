<?php

namespace ipinfo\controllers;

use ipinfo\models\GuestInfo;
use mmvc\core\AccessChecker;
use mmvc\controllers\WebController;

class GuestController extends WebController {
    
    protected $dataTree = [
        'screen' => [
            'caption' => 'Screen' , 
            'icon' => 'fa-desktop',
            'data' => [
                'Screen size' => 'N/A',
                'Window size' => 'N/A',
                'Pixel depth' => 'N/A',
                'Color depth' => 'N/A',
                'availLeft' => 'N/A',
                'availTop' => 'N/A',
                'availWidth' => 'N/A',
                'availHeight' => 'N/A',
            ],
        ],
        'plugins' => [
            'caption' => 'Plugins' , 
            'icon' => 'fa-cube',
        ],
        'language' => [
            'caption' => 'Language' , 
            'icon' => 'fa-cube',
            'data' => [
                'Headers' => '',
                'JavaScript' => '',
                'Flash' => '',
                'Java' => '',
            ],
        ],
        'time' => [
            'caption' => 'Time' , 
            'icon' => 'fa-cube',
        ],
        'dns' => [
            'caption' => 'Dns' , 
            'icon' => 'fa-cube',
        ],
        'navigator' => [
            'caption' => 'Navigator' , 
            'icon' => 'fa-internet-explorer',
        ],
        'scripts' => [
            'caption' => 'Scripts' , 
            'icon' => 'fa-file-code-o',
            'data' => [
                'JavaScript' => 'disabled',
                'WebRTC' => 'disabled',
                'ActiveX' => 'disabled',
                'VBScript' => 'disabled',
                'Java' => 'disabled',
                'WebAssembly' => 'disabled',
            ],
        ],
        'http-data' => [
            'caption' => 'HTTP data' , 
            'icon' => 'fa-server',
        ],
        'location' => [
            'caption' => 'Location' , 
            'icon' => 'fa-globe',
        ]
    ];

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
        $data = $info->buildInfo();


        $result = \ipinfo\helpers\VarDumper::getData($info, 'server');
        \ipinfo\helpers\VarDumper::printData($result); die;

        $this->dataTree['http-data']['data'] = $data['http_data'];
        $ipInfo = $info->getIpInfo();
        $this->dataTree['location']['data'] = $ipInfo;
        if (is_array($ipInfo) && isset($ipInfo['latitude']) && isset($ipInfo['longitude']))
        { 
            $timezone = $info->getNearestTimezone($ipInfo['latitude'], $ipInfo['longitude']);
            $this->dataTree['time']['data'] = ['Zone (geoip)' => $timezone];
        }
       
        $this->appendVariable('is_tor_user', $info->isTorUser());
        $this->appendVariable('www_root', $this->getHttpRootPath());
        $this->appendVariable('proxy_is_used', $info->isProxyUsed());
        $this->appendVariable('proxy_header', $info->detectedProxyHeader);
        
        $this->appendVariable('data', $this->dataTree);
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
