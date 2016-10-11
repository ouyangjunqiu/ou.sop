# ou.sop
swoole面向服务编程案例

>Swoole : PHP的异步、并行、高性能网络通信引擎，使用纯C语言编写，提供了PHP语言的异步多线程服务器，异步TCP/UDP网络客户端，异步MySQL，异步Redis，数据库连接池，AsyncTask，消息队列，毫秒定时器，异步文件读写，异步DNS查询。


####案例:
1. 邮件服务
	
	
		$client = new swoole_client(SWOOLE_SOCK_TCP);
		
		if (!$client->connect('127.0.0.1', 9501, -1))
		{
		
		    exit("connect failed. Error: {$client->errCode}\n");
		}
		
		$msg = array("exec"=>"mail","subject"=>"测试邮件","msg"=>"这是系统测试邮件发出，请勿理会。","address"=>array("oshine"=>"oshine.ouyang@da-mai.com"));
		$msg = json_encode($msg);
		$client->send($msg);
		
		$client->close();
	
	
2. 数据分析服务
	
