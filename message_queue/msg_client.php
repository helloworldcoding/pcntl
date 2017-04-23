<?php

$queueKey = ftok(__FILE__,'a');
$serverKey = file_get_contents('./msg_key');
$serverQueue = msg_get_queue($serverKey); // 获取服务器端队列

$queue = msg_get_queue($queueKey);
echo 'client is running,communication to the server '.PHP_EOL;
while(1) {
    // receive msg from the client queue;
    msg_receive($queue,1,$msgType,1024,$message,true,MSG_IPC_NOWAIT);
    if ($message) {
        echo 'the server says: '.$message.PHP_EOL;
    } else {
       // echo 'the server says nothing.'.PHP_EOL;
    }

    // sending data to the server;
    //
    echo 'say to the server: ';
    $content = trim(fgets(STDIN));
    $data = ['key'=>$queueKey,'content'=>$content];
    msg_send($serverQueue,1,$data);
    sleep(1);
}
