<?php
/**
 * @file mail.php
 * @author ouyangjunqiu
 * @version Created by 2016/10/11 09:44
 */

$client = new swoole_client(SWOOLE_SOCK_TCP);
if (!$client->connect('127.0.0.1', 9501, -1))
{
    exit("connect failed. Error: {$client->errCode}\n");
}
$client->send(json_encode(array("exec"=>"test")));
$client->close();