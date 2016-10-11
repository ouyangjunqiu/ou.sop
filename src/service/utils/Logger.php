<?php
/**
 * @file Logger.php
 * @author ouyangjunqiu
 * @version Created by 16/8/9 14:11
 */

namespace ou\sop\service\utils;

class Logger
{
    private static $logPath = null;

    public static function init($logPath){
        self::$logPath = $logPath;
    }

    public static function log($msg){

        if(self::$logPath == null){
            self::$logPath = "/tmp";
        }

        if(!is_dir(self::$logPath)){
            mkdir(self::$logPath);
        }

        $f = fopen(self::$logPath."/error.log","a+");
        if(!$f) return;
        fwrite($f,$msg."\t".date("Y-m-d H:i:s")."\n");
        fclose($f);
    }

}