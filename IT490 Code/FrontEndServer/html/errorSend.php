<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

function send($errorString){

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();


$channel->queue_declare('hello', false, false, false, false);

$msg = new AMQPMessage($errorString);
$channel->basic_publish($msg, '', 'hello');

echo "Sent: " . $errorString;

$channel->close();
$connection->close();

}


$value = "test";

send($value);
?>