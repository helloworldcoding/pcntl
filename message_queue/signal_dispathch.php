
<?php

// 获取消息队列key
$key = ftok(__FILE__,'w');

// 创建衣蛾消息队列
$queue = msg_get_queue($key);

$child = [];
$num   = 5;
$result = [];
$count = 0;
function hanlder($signal)
{
	global $count;
	if ($signal == SIGCHLD) {
		$count++;
		echo 'child return'.PHP_EOL;
	} else {
		echo $signal.' signal code'.PHP_EOL;
	}
}
pcntl_signal(SIGCHLD,'hanlder');


for($i=0;$i<$num;$i++){
	$pid = pcntl_fork();
	if($pid == -1) {
		die('fork failed');
	} else if ($pid > 0) {
		$child[] = $pid;
	} else if ($pid == 0) {
		$sleep = rand(1,4);
		//msg_send($queue,1,array('name' => $i.'~'.$sleep));
		sleep($sleep);
		exit(0);
	}
}
//msg_receive($queue,1,$message_type,1024,$data);
//var_dump($data);
//while(1){ }

/*
while(count($child)){
	foreach($child as $k => $pid) {
		$res = pcntl_waitpid($pid,$status,WNOHANG);
		if ($res == -1 || $res > 0 ) {
			unset($child[$k]);
			msg_receive($queue,2,$message_type,1024,$data);
			$result[] = $data;
		}
	}
}
 */
//msg_remove_queue($queue);
//print_r($result);
while(1 && $count <$num){
	$result = pcntl_signal_dispatch();
	sleep(1);
}
