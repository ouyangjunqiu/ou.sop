<?php
/**
 * @file Bootstrap.php
 * @author ouyangjunqiu
 * @version Created by 2016/10/11 09:55
 */
namespace ou\sop\service;
use ou\sop\service\utils\Logger;
use swoole_server;

class Bootstrap {

    private static $serv = null;

    public function __construct()
    {
        if(self::$serv == null){
            self::$serv = new swoole_server("127.0.0.1", 9501);
            self::$serv->set(array(
                'worker_num' => 8,   //工作进程数量
                'daemonize' => true, //是否作为守护进程
            ));
        }

        return self::$serv;

    }

    public static function boot(){

        $dispatch = new Dispatch();

        $dispatch->register("test",function($data){
            Logger::log("test");
        });


        $dispatch->register("mail",function($data){

        });



        self::$serv->on('receive', function ($serv, $fd, $from_id, $data) use($dispatch) {

            $data = json_decode($data,true);
            $command = $data["exec"];
            if(!empty($command)){
                $dispatch->trigger($command,$data);
            }

        });

        self::$serv->start();

    }




}