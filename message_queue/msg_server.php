<?php

$keyFile = './msg_key';
$queueKey = ftok(__FILE__,'a');
file_put_contents($keyFile,$queueKey);
$queue    = msg_get_queue($queueKey);
$child = [];

while(1) {
	msg_receive($queue,1,$msgtype,1024,$message,true,MSG_IPC_NOWAIT);
	if (!empty($message['key'])) {
		$clientKey = $message['key'];
		$content   = empty($message['content']) ? '****' :$message['content'];
		$pid = pcntl_fork();
		if ($pid == 0) {
			$content = 'data from server '.$content;
			$clientQueue = msg_get_queue($clientKey);
			msg_send($clientQueue,1,$content);
            exit(0);
		} else if ($pid) {
			$child[] = $pid;
		}else {
			echo 'fork error '.PHP_EOL;
		}
	}
}

while(count($child)) {
	foreach($child as $k => $pid) {
		$res = pcntl_waitpid($pid,$status,WNOHANG);
		if($res == -1 || $res) {
			unset($child[$k]);
		}
	}
}

