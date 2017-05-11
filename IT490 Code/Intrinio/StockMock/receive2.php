<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once 'retrieveCompInfo.php';
require_once 'send2.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('192.168.0.154', 5672, 'ashish', 'password');
$channel = $connection->channel();


$channel->queue_declare('R1toDMZ', false, false, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C'. "\n";

$callback = function($msg) {
  echo $msg->body, "\n";
  $x = getInfo($msg->body);
 
  //echo "return from getInfo(): " .$x;
  sendMessage($x);
};

$channel->basic_consume('R1toDMZ', '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();


?>
