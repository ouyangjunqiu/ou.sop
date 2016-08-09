<?php
namespace phpservice\app\croon;
use phpservice\service\Daemon;

/**
 * @file CroonDaemon.php
 * @author ouyangjunqiu
 * @version Created by 16/8/9 15:54
 */
class CroonDaemon extends Daemon
{
    /**
     * Path to PID file
     *
     * @var string
     * @since 1.0.1
     */
    protected $pidFileLocation = '/tmp/service.croon.daemon.pid';


}