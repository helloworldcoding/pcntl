<?php
/**
 * 模拟并发请求，10万次写入数据库
 * 拆分为10个进程，每个进程处理一万条插入
 */

$total = 100000;
$num   = 100;
$per   = $total/$num;

$sql  = '';
$child = '';

echo 'start '.microtime(true).PHP_EOL;
for($i = 1; $i<= $num; $i++) 
{
	$pid = pcntl_fork();
	if($pid == -1) {
		die('fork error');
	}
	if($pid > 0) {
		//$id = pcntl_wait($status,WNOHANG);
		$child[] = $pid;
	} else if ($pid == 0) {
		$link  = mysqli_connect('localhost','root','root','yii2advanced');
		$start = ($i-1)*$per + 1;
		$end   = $start + $per;
		for($j = $start; $j< $end; $j++){
			$time = microtime(true);
			$sql = 'insert pcntl_test (rank,time) values ('.$j.','.$time.')';
			mysqli_query($link,$sql);
		}
		mysqli_close($link);
		$id = getmypid();
		echo 'child '.$id.' finished '.microtime(true).PHP_EOL;
		exit(0);
	}
}

while(count($child)){
	foreach($child as $k => $pid) {
		$res = pcntl_waitpid($pid, $status, WNOHANG);
		if ( -1 == $res || $res > 0) {
			unset($child[$k]);
		}
	}
}
echo 'end '.microtime(true).PHP_EOL;
