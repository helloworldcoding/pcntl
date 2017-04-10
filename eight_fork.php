<?php

// 最大子进程数量
$maxChild = 4;

// 当前的子进程数量
$curChild = 0;

// 当子进程退出时，会触发该函数，当前子进程-1
function sig_handler($sig)
{
	global $curChild;
	switch ($sig) {
		case SIGCHLD:
			echo 'SIGCHLD',PHP_EOL;
			$curChild--;
			break;
	}
}

declare(ticks = 1);

//注册子进程退出时调用的函数，SIGHLD:在一个进程终止或者退出时，将SIGHLD信号发送给其父进程

pcntl_signal(SIGCHLD,"sig_handler");

$i = 2;
while ($i) {
	$curChild++;
	$pid = pcntl_fork();

	if ($pid) {
		// 父进程运行代码，达到上限时，父进程等待任一子进程退出后while循环继续
		if ($curChild >= $maxChild) {
			pcntl_wait($status);
		}
	} else {
		$s = rand(1,3);
		sleep($s);
		$pid = posix_getpid();
		$ppid = posix_getppid();
		echo 'pid '.$pid.' ppid '.$ppid.' child sleep '.$s.' seconds quit',PHP_EOL;
		// 子进程需要exit，防止子进程也进入循环
		exit();
	}
	//$i--;
}
