# phpservice
service基础框架

service基础框架包括Daemon和Task.

Daemon 以服务的形式运行.

Task 运行的任务或者事项.

案例:
1. 创建一个Task,每隔1s往文件中写入"Hello!",文件名:src/app/simple/SimpleTask.php
<?php
namespace phpservice\app\simple;
use phpservice\service\Task;

class SimpleTask extends Task {
   public function run(){
        file_put_contents("/tmp/simple.task.txt","Hello!");
        sleep(1);
   }
}

2. 创建一个可执行程序,如:bin/SimpleService

#!/usr/bin/env php
<?php
require dirname(__DIR__) . '/vendor/autoload.php';

$task = new phpservice\app\simple\SimpleTask();

$daemon = new phpservice\service\Daemon($task);

$daemon->start();

3. 执行,赋予bin/SimpleService可执行权限,命令行运行

bin/SimpleService

4. 如何结束

kill -9 进程号
