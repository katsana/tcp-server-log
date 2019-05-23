<?php

require 'vendor/autoload.php';

$eventLoop = React\EventLoop\Factory::create();
$writer = new React\Stream\WritableResourceStream(STDOUT, $eventLoop);
$server = new React\Socket\TcpServer(50001, $eventLoop);

$server->on('connection', function (React\Socket\ConnectionInterface $connection) use ($writer) {
    $writer->write('Connection from '.$connection->getRemoteAddress().PHP_EOL);

    $connection->on('data', function ($chunk) use ($connection, $writer) {
        $writer->write($connection->getRemoteAddress()." send :`{$chunk}`".PHP_EOL);
    });

    $connection->on('close', function ($chunk) use ($connection, $writer) {
        $writer->write($connection->getRemoteAddress().' closed connection'.PHP_EOL);
    });
});

$server->on('error', function (Exception $e) use ($writer) {
    $writer->write('Error'.$e->getMessage().PHP_EOL);
});

$eventLoop->run();
