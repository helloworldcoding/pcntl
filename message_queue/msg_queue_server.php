<?php

$type = 1; // 服务器端从消息队列中获取的消息类型
$defaultPath = './index'; // 默认的请求文件路径

$queueKey = ftok(__FILE__,'a');
file_put_contents('./msg_queue.key',$queueKey);
$msgQueue = msg_get_queue($queueKey);
echo 'listening ....'."\n";

while (true) {
    msg_receive($msgQueue,$type,$msg_type,1024,$message);
    if ($message) {
        response($message, $msgQueue);
    }
    sleep(1);
}

function response($message, $msgQueue)
{
    if (empty($message) || empty($message['pid'])) {
        return false;
    }
    $pid = $message['pid'];
    $path = empty($message['path']) ? $defaultPath : $message['path'];
    $content = '';
    if ( file_exists($path) ) {
        //$content = file_get_contents($path);
        $fh = fopen($path,'r');
        while (!feof($fh)) {
            $content = fread($fh,1024);
            if ($content) {
                msg_send($msgQueue,$pid,$content);
            }
        }
    }

}
