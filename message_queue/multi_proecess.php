<?php

// 获取消息队列key
$key = ftok(__FILE__,'w');
file_put_contents('./msg_queue.key',$key);
echo $key;

// 创建衣蛾消息队列
$queue = msg_get_queue($key);

$child = [];
$num   = 5;
$result = [];

for($i=0;$i<$num;$i++){
	$pid = pcntl_fork();
	if($pid == -1) {
		die('fork failed');
	} else if ($pid > 0) {
		$child[] = $pid;
	} else if ($pid == 0) {
		$sleep = rand(1,4);
		$id = getmypid();
		msg_send($queue,$id,array('name' => $i.'~'.$sleep,'pid'=>$id));
		sleep($sleep);
		exit(0);
	}
}

while(count($child)){
	foreach($child as $k => $pid) {
		$res = pcntl_waitpid($pid,$status,WNOHANG);
		if ($res == -1 || $res > 0 ) {
			unset($child[$k]);
			msg_receive($queue,$pid,$message_type,1024,$data);
			$result[$pid] = $data;
		}
	}
}
msg_remove_queue($queue);
print_r($result);
