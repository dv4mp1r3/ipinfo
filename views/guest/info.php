<?php
/* @var array $data */
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
        <link href="<?= $www_root ?>/assets/css/guest.css" rel="stylesheet">
    </head>
    <body>
        <?php ipinfo\helpers\VarDumper::printData($data, 'backend_data'); ?> 
        <script src="<?= $www_root ?>/assets/js/jquery.js"></script>
        <script src="<?= $www_root ?>/assets/js/ads.js"></script>
        <script src="<?= $www_root ?>/assets/js/guest.js"></script>
    </body>
</html>