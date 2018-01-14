<?php
/* @var array $data */
/* @var string $proxy_is_used */
/* @var string $proxy_header */
/* @var array $user_ip_info */
/* @var array $is_tor_user */
/* @var string $www_root */
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Добро пожаловать, снова">
        <meta name="author" content="dv4mp1r3">
        <link href="<?= $www_root ?>/assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?= $www_root ?>/assets/css/1-col-portfolio.css" rel="stylesheet">
        <link href="<?= $www_root ?>/assets/css/guest.css" rel="stylesheet">
        <link href="<?= $www_root ?>/assets/css/font-awesome.min.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <div class="container">
            <?php foreach ($data as $widgetName => $options): ?>
                <div class="row">
                    <div class="widget widget-gray"> 
                        <div class="widget-head">
                            <h4 class="heading">
                                <i class="fa <?= $options['icon'] ?>"></i>  
                                <?= $options['caption'] ?>
                            </h4>
                        </div>                    
                        <div class="widget-body" id="widget-<?=$widgetName?>"> 
                            <?php if (!empty($options['data'])): ?>
                                <?php foreach ($options['data'] as $key => $value): ?>
                                    <p class="name"><?= $key ?></p>
                                    <p class="value"><?= $value ?></p>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="widget-footer"> 
                            <a widgetName="widget-<?=$widgetName?>"               
                               id="toggle" 
                               class="toggle-button fa fa-chevron-up fa-1x" 
                               data-toggle="tooltip" 
                               data-placement="right" 
                               title="" 
                               data-original-title="Show/Hide">
                                <i></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>    
            <div class="row">
                <iframe id="iframe" sandbox="allow-same-origin allow-scripts" style="display: none"></iframe>
            </div>
        </div> 
        <script src="<?= $www_root ?>/assets/js/jquery.js"></script>
        <script src="<?= $www_root ?>/assets/js/bootstrap.min.js"></script>
        <script src="<?= $www_root ?>/assets/js/ads.js"></script>
        <script src="<?= $www_root ?>/assets/js/guest.js"></script>
    </body>
</html>