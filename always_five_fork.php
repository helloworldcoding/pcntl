<?php

$num = 1; // 进程数量
$maxLife = 1; // 进程的最大存活时间
$minLife = 1; // 进程最小的存活时间
$child = [];  //子进程容器

function genarate($min = 3, $max = 30)
{
	$pid = pcntl_fork();
	if($pid == -1) {
		return 0;
	} else if ($pid == 0) {
		$life = rand($min, $max);
		sleep($life); // 模拟进程生命
		$id = getmypid();
		echo 'child '.$id. ' gone'.PHP_EOL;
		exit(0);
	} else if ($pid > 0) {
		return $pid;
	}
	return 0;
}


while(true) {
	if (count($child) < $num) {
		$pid = genarate($minLife, $maxLife);	
		echo 'child '.$pid.' born '.PHP_EOL;
		if($pid) {
			$child[] = $pid;
		}
	} else if ( count($child) > $num ) {
		$pid = array_pop($child);
		posix_kill($pid,SIGINT);
		echo 'child '.$pid.' killed'.PHP_EOL;
	}
	foreach($child as $k => $pid) {
		$res = pcntl_waitpid($pid,$status,WNOHANG);
		if($res == -1 || $res > 0 ) {
			unset($child[$k]);
		}
	}
}

