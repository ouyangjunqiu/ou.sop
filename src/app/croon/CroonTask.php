<?php
/**
 * @file CroonTask.php
 * @author ouyangjunqiu
 * @version Created by 16/8/9 15:59
 */

namespace phpservice\app\croon;

use phpservice\service\Task;

class CroonTask extends Task
{
    public function run()
    {
        $croon = new \Croon\Croon(array(
            'source' => array(
                'type' => 'pdo',
                'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=croon',
                'username' => 'cps.da-mai.com',
                'password' => 'cps@da-mai.com',
                'options' => array(),
                'table' => 'croon',
                'fields' => array('time', 'command')
            ),
            'log' => array(
                'file' => dirname(dirname(dirname(dirname(__FILE__))))."/data/runtime/croon.log"
            )
        ));
        $croon->run();

    }

}