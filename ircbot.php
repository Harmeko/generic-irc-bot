<?php

$server = "irc.real.url.org";
$port = 6667;
$nickname = 'HRMKBOT';
$ident = "hrmkbot";
$gecos = "bot from harmeko";
$channel = "#channel";

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);;
$error = socket_connect($socket, $server, $port);

if ($socket === false) {
    $errorCode = socket_last_error();
    $errorString = socket_strerror($errorCode);
    die("Error $errorCode: $errorString");
}

socket_write($socket, "NICK $nickname\r\n");
socket_write($socket, "USER $ident * 8 : :$gecos\r\n");

while (is_resource($socket)) {
    $data = trim(socket_read($socket, 1024, PHP_NORMAL_READ));
    echo $data . "\n";
    $d = explode(' ', $data);
    $d = array_pad($d, 10, '');

    // PING handler
    if ($d[0] === 'PING') {
        socket_write($socket, 'PONG ' . $d[1] . "\r\n");
    }

    // Join chan once you are connected to the server
    if ($d[1] === '376' || $d[1] === '422') {
        socket_write($socket, 'JOIN ' . $channel . "\r\n");
    }

    // Generic answer to the chan if asked in private, do not use for real
    // You can get the username from $d
    if ($d[1] === "PRIVMSG") {
        socket_write($socket, "hello\r\n");

    }


}