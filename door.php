<?php
$mode = ($_GET['mode']);
$ip = $_SERVER['REMOTE_ADDR'];
$var = explode(".", $ip);
require('../mac/routeros_api.class.php');
$API = new RouterosAPI();
if ($API->connect("192.168.$var[2].1", USER, PASS)) {
$API->write('/system/identity/print');
$name = $API->read(true);
$location = $name[0]['name'];
$API->write('/ip/arp/print', false);
$API->write("?address=$ip");
$mac = $API->read(true);
$adress = $mac[0]['mac-address'];
} else {
$location = 'none';
$adress = $ip;
}
$API->disconnect();
date_default_timezone_set('Europe/Moscow');
$log = date('Y-m-d H:i:s') . " $mode $location $adress";
file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
fopen("https://api.telegram.org/botTOKEN/sendMessage?chat_id=CHAT_ID&text=DOOR%20$mode%0a$location%20$adress", 'r');
if ($location == 'location') {
if ($mode == 'press') {
if (count($_COOKIE) > 0) {
header('Location: http://door.test');
exit();
} else {
header('Location: ../door');
exit();
}
}
}
header('Location: http://test.door');
exit();
?>
