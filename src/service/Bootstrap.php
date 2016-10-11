<?php
/**
 * @file Bootstrap.php
 * @author ouyangjunqiu
 * @version Created by 2016/10/11 09:55
 */
namespace ou\sop\service;
use ou\sop\service\utils\Logger;
use swoole_server;
use PHPMailer;

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
            $mail  = new PHPMailer();

            $mail->CharSet    ="UTF-8";                 //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置为 UTF-8
            $mail->IsSMTP();                            // 设定使用SMTP服务
            $mail->SMTPAuth   = true;                   // 启用 SMTP 验证功能
            $mail->SMTPSecure = "ssl";                  // SMTP 安全协议
            $mail->Host       = "smtp.mxhichina.com";       // SMTP 服务器
            $mail->Port       = 465;                    // SMTP服务器的端口号
            $mail->Username   = "damai@da-mai.com";  // SMTP服务器用户名
            $mail->Password   = "da-mai2016";        // SMTP服务器密码
            $mail->SetFrom('damai@da-mai.com', '系统');    // 设置发件人地址和名称
           // $mail->AddReplyTo("邮件回复人地址","邮件回复人名称");
            // 设置邮件回复人地址和名称
            $mail->Subject    = $data['subject'];                     // 设置邮件标题
            $mail->AltBody    = "邮件由系统自动发出，请勿回复！";

            // 可选项，向下兼容考虑
            $mail->MsgHTML($data['msg']);

            foreach($data["address"] as $name=>$address){
                // 设置邮件内容
                $mail->AddAddress($address, $name);
            }

            //$mail->AddAttachment("images/phpmailer.gif"); // 附件
            if(!$mail->Send()) {
                Logger::log("发送失败：" . $mail->ErrorInfo);
            } else {
                Logger::log("恭喜，邮件发送成功！");
            }


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