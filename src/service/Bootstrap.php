<?php
/**
 * @file Bootstrap.php
 * @author ouyangjunqiu
 * @version Created by 2016/10/11 09:55
 */
namespace ou\sop\service;

class Bootstrap {

    private static $serv = null;

    public static function boot(){
        if(self::$serv == null){
            $serv = new swoole_server("127.0.0.1", 9501);
            $serv->set(array(
                'worker_num' => 8,   //工作进程数量
                'daemonize' => true, //是否作为守护进程
            ));

            $serv->on('receive', function ($serv, $fd, $from_id, $data) {
                $serv->send($fd, 'Swoole: '.$data);
                $serv->close($fd);
            });
        }
        return self::$serv;
    }




}