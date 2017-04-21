<?php

$path = empty($argv[1]) ? './index' : $argv[1];
$keyFile= './msg_queue.key';
$queueKey = file_get_contents($keyFile);
if (empty($queueKey)) {
    die('no key in  file');
}

$msgQueue = msg_get_queue($queueKey);
$pid = getmypid();

// send request data to the server
$request = [
    'pid' => $pid,
    'path' => $path,
];
msg_send($msgQueue,1,$request);


// receive data from the server
while (1) {
    //msg_receive($msgQueue,$pid,$msgType,1024,$response,true,MSG_NOERROR);
    msg_receive($msgQueue,$pid,$msgType,1024,$response);
    if($response) {
        print_r($response);
        break;
    }
}
