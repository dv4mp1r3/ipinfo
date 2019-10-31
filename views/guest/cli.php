<?php

/**
 * @var array $data
 */

use mmvc\models\cli\Table;
use mmvc\models\helpers\ColoredConsole;

$table = new Table();

$table->setHeader(['Header', 'Value']);
foreach ($data['http_data'] as $key => $value)
{
    $table->pushRow([$key, $value['value']]);
}
header("Content-Type: text/plain");
?>
Remote IP: <?= $data['remoteIP']['value']; ?>

Tor: <?= ColoredConsole::paintYesNo(
    $data['isTorUsed']['value'],
    $data['isTorUsed']['value'] === 'Yes',
    ColoredConsole::COLOR_GREEN,
     ColoredConsole::COLOR_RED);
?>

Proxy: <?= ColoredConsole::paintYesNo(
    (string)$data['isProxyUsed']['value'] === true ? 'Yes' : 'No',
    $data['isProxyUsed']['value'] === true,
    ColoredConsole::COLOR_GREEN,
    ColoredConsole::COLOR_RED);

?>

HTTP Headers:
<?= $table->out(); ?>
