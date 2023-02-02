<?php
$apnsPort = 2195;
$apnsHost = 'gateway.sandbox.push.apple.com';
$apnsCert = 'push_dev.pem';


$apnsHost = 'gateway.push.apple.com';
$apnsCert = 'push_knuff.pem';


$payload['aps'] = array(
		'alert' => 'Essai de push',
		'badge' => 1,
		'sound' => 'default'
		);
$payload = json_encode($payload);

$streamContext = stream_context_create();
stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);

$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
var_dump($apns);

echo $errorString;

$deviceToken = '89f77e4b139fb4888f7e8daeeaf69a10240316f2f3fd68d5c39ee0989ed935f5';
$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
$res = fwrite($apns, $apnsMessage);
print $res;
socket_close($apns);
fclose($apns);
