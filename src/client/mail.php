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
$client->send(json_encode(array("exec"=>"mail","subject"=>"测试邮件","msg"=>"这是系统测试邮件发出，请勿理会。","address"=>array("oshine"=>"oshine.ouyang@da-mai.com"))));
$client->close();