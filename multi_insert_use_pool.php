<?php
/**
 * 模拟并发请求，100万次写入数据库
 * 拆分为10000个进程，
 * 使用数据库连接池,size 为500
 *
 */

$total = 1000;
$num   = 500;
$per   = $total/$num;

$poolSize  = 500; // 数据库连接池容量

$sql  = '';
$child = [];

// 共享内存通信
$shm_key = ftok(__FILE__,'t');
$shm_id  = shm_attach($shm_key,1024,0655);
const SHARE_KEY = 1;
$pool = [];

// 加入信号量
$sem_id = ftok(__FILE__,'s');
$signal = sem_get($sem_id);

// 初始化连接数量,开始的连接数为0
shm_put_var($shm_id, SHARE_KEY,0);

$begin  = microtime(true);
echo 'start '.$begin.PHP_EOL;

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
		while(true) {
			// 获得信号量
			sem_acquire($signal);
			$count = shm_get_var($shm_id, SHARE_KEY);
			if ($count >= $poolSize) {
				sem_release($signal);
				continue;
			} else {
				$link  = mysqli_connect('localhost','root','root','yii2advanced');
				if ($link) {
					$count++;
					shm_put_var($shm_id,SHARE_KEY,$count);
					sem_release($signal);

					$start = ($i-1)*$per + 1;
					$end   = $start + $per;
					for($j = $start; $j< $end; $j++){
						$time = microtime(true);
						$sql = 'insert pcntl_test (rank,time) values ('.$j.','.$time.')';
						mysqli_query($link,$sql);
					}
					mysqli_close($link);
					break;
				}
				
			}
			sleep(1);
		}
		sem_acquire($signal);
		$count = shm_get_var($shm_id,SHARE_KEY);
		$count--;
		shm_put_var($shm_id, SHARE_KEY, $count);
		sem_release($signal);
		$id = getmypid();
		$count++;
		echo 'count : '.$count.' child '.$id.' finished '.microtime(true).PHP_EOL;
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

$end = microtime(true);
echo 'end '.$end.PHP_EOL;
echo 'fork '.$num.'process insert '.$total.' recodes takes '.($end-$begin).PHP_EOL;
