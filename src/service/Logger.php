<?php
/**
 * @file Logger.php
 * @author ouyangjunqiu
 * @version Created by 16/8/9 14:11
 */

namespace service;

// Log message levels
define('DLOG_TO_CONSOLE', 1);
define('DLOG_NOTICE', 2);
define('DLOG_WARNING', 4);
define('DLOG_ERROR', 8);
define('DLOG_CRITICAL', 16);
class Logger
{

    public static function log($msg, $level = DLOG_NOTICE){

        if($level == DLOG_ERROR){
            $path = dirname(dirname(__FILE__))."/data/runtime";

            if(!is_dir($path)){
                mkdir($path);
            }

            $f = fopen($path."/error.log","a+");
            if(!$f) return;
            fwrite($f,$msg."\t".date("Y-m-d H:i:s")."\n");
            fclose($f);
        }
    }

}