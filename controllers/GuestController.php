<?php

namespace ipinfo\controllers;

use ipinfo\models\GuestInfo;
use mmvc\core\AccessChecker;
use mmvc\controllers\WebController;

class GuestController extends WebController {

    const KEY_FP = 'fingerPrints';

    const KEY_VISITS = 'visitCount';

    const VIEW_NAME_WEB = 'web';
    const VIEW_NAME_HTML = 'html';
    const VIEW_NAME_CLI = 'cli';

    public function __construct() {
        $this->rules = [
            'info' => [
            AccessChecker::RULE_GRANTED => AccessChecker::USER_ALL,
            ],
        ];
        parent::__construct();
    }

    /**
     * Подготовка данных для передачи во вьюху
     * @return array
     */
    protected function collectData()
    {
        $this->updateSessionData();
        $info = new GuestInfo();
        $ipInfo = $info->getIpInfo();
        $data = $info->buildInfo();

        $data['location'] = $ipInfo;
        if (is_array($ipInfo) && isset($ipInfo['latitude']) && isset($ipInfo['longitude']))
        {
            $timezone = $info->getNearestTimezone($ipInfo['latitude'], $ipInfo['longitude']);
            $data['timezone'] = $timezone;
        }
        $data[self::KEY_VISITS]  = $_SESSION[self::KEY_VISITS];
        $data['remoteIP']  = $info::getRemoteIp();
        $data['isTorUsed']   = $info->isTorUser();
        $data['isProxyUsed'] = $info->isProxyUsed();
        $data['proxyHeader']  = $info->detectedProxyHeader;

        $data = \ipinfo\helpers\VarDumper::getData($data, 'server');
        return $data;
    }

    /**
     * Обновление данных о сессии, либо создание при первом посещении
     */
    protected function updateSessionData()
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
    }

    /**
     * @param string $view
     */
    protected function renderByViewName($view)
    {
        $data = $this->collectData();
        $this->appendVariable('data', $data);
        if ($view === GuestController::VIEW_NAME_WEB)
        {
            $this->appendVariable('www_root', $this->getHttpRootPath());
        }
        $this->render($view);
    }

    /**
     * html-страница со стилями и js (современные браузеры)
     */
    public function actionWeb()
    {
        $this->renderByViewName(GuestController::VIEW_NAME_WEB);
    }

    /**
     * только html-страница (устаревшие браузеры)
     */
    public function actionHtml()
    {
        $this->renderByViewName(GuestController::VIEW_NAME_HTML);
    }

    /**
     * форматирование для отображения в терминалах
     */
    public function actionCli()
    {
        $this->renderByViewName(GuestController::VIEW_NAME_CLI);
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
