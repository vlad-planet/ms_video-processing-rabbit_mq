# ms_video-processing-rabbit_mq
 microservices

## Сервис для массовой загрузки и обработки видео, с использованием RabbitMQ + Безопасность данных

Данный сервис помогает массово обрабатывать мультимедиа, и значительно сэкономить много времени и ресурсов.

Требования к Запуску Теста:<br>
- Composer<br>
- ^PHP7<br>
- PHP Sockets Extensions Installed<br>

1. Убедитесь, что RabbitMQ установлен и запущен локально
2. Перейдите в корневую директорию
3. Запустите composer install для установки необходимых пакетов
4. Откройте две вкладки в вашей консоли
5. На одной вкладке запустите php server.php
6. На другой вкладке запустите php client.php

____________________________________________________________________________________________________

Подключение к RabbitMQ
```php
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$ channel->queue_declare('video_queue', false, false, false);
```

Отправка очереди
```php
$msg = new AMQPMessage(json_encode($data));
$channel->basic_publish($msg, '', 'video_queue');
```

Прослушка сообщения
```php
$ channel->queue_declare('video_queue', false, false, false);
$ channel->basic_consume('video_queue', '', false, true, false, $ callback);
```
