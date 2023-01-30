<?php
include ('vendor/autoload.php');

use prodigyview\media\Video;
use prodigyview\util\FileManager;
use PhpAmqpLib\Connection\AMQPStreamConnection;

//Запустить сервер RabbitMQ
$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('video_queue', 	//$queue - Либо устанавливает очередь, либо создает ее, если она не существует
						false,			//$passive - Не изменяйте состояние серверов
						true,			//$durable - Данные сохранятся, если произойдет сбой или перезапуск
						false,			//$exclusive - Будет использоваться только одно соединение, и оно будет удалено при закрытии
						false			//$auto_delete - Очередь удаляется, когда потребитель больше не подписывается
						);

/**
 * Определить функцию обратного вызова
 */
$callback = function($msg) {
	//Преобразовать данные в массив
	$data = json_decode($msg->body, true);

	//Определить, установлены ли wget и ffmpeg *Linux
	exec("man wget", $wget_exist);
	exec("man ffmpeg", $ffmpeg_exist);

	if ($wget_exist) {
		//Использовать wget для загрузки видео. *Linux
		exec("wget -O video.mp4 {$data['video_url']}");
	} else {
		//Используйте FileManager в качестве резервной копии
		FileManager::copyFileFromUrl($data['video_url'], getcwd() . '/', 'video.mp4');
	}

	if ($ffmpeg_exist) {
		//Запустите конвертацию формата с помощью ffmpeg *Linux
		Video::convertVideoFile('video.mp4', 'video.' . $data['convert_to']);
	} else {
		echo "Sorry No Conversion Software Exist On Server\n";
	}

	echo "Finished Processing\n";
};

//Передать обратный вызов
$channel->basic_consume('video_queue', '', false, false, false, false, $callback);

//Прослушать обратные вызовы
while (count($channel->callbacks)) {
	$channel->wait();
}