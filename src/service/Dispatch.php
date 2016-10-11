<?php
/**
 * @file Dispatch.php
 * @author ouyangjunqiu
 * @version Created by 2016/10/11 10:42
 */

namespace ou\sop\service;


class Dispatch
{

    private static $maps = array();

    public function register($name,$callback){
        static::$maps[$name] = $callback;
    }

    public function trigger($name,$params){
        $callback = self::$maps[$name];
        call_user_func($callback,$params);
    }

}