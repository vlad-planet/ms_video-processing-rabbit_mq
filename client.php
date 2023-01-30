<?php

include ('vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

//Создайте очередь
$channel->queue_declare('video_queue', 	//$queue - Либо устанавливает очередь, либо создает ее, если она не существуетt
						false,			//$passive - Не изменяйте состояние серверов
						true,			//$durable - Данные сохранятся, если произойдет сбой или перезапуск
						false,			//$exclusive - Только одно соединение будет использовать очередь и удалено при закрытии
						false			//$auto_delete - Queue is deleted when consumer is no longer subscribes
						);

$data = array(
	'video_url' => 'https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_20mb.mp4',
	'convert_to' => 'mov'
);

//Создать сообщение, настройте доставку так, чтобы она была постоянной при сбоях и перезапусках
$msg = new AMQPMessage(json_encode($data), array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
$channel->basic_publish($msg, '', 'video_queue');

echo "Sent Video To Server!'\n";

$channel->close();
$connection->close();
