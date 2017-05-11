<?php
require_once __DIR__ . '/vendor/autoload.php';
//re_once 'home\/ashish\/Desktop\/rabbitmq_test\/vendor\/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

function sendMessage($string){

	$ipAddr = "192.168.0.106";

	$connection = new AMQPStreamConnection($ipAddr, 5672, 'ashish', 'password');
	$channel = $connection->channel();

	$channel->queue_declare('DMZtoR1', false, false, false, false);


	$msg = new AMQPMessage($string);
	$channel->basic_publish($msg, '', 'DMZtoR1');


	echo " Sent: " . $string . "\n";

	$channel->close();
	$connection->close();
}

?>
