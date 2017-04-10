<?php
/**
 * 父进程通过pcntl_wait等待子进程退出
 * 子进程通过信号kill自己，也可以在父进程中发送kill信号结束子进程
 */

$pid = pcntl_fork();

if ($pid == -1){
	die('fork failed'.PHP_EOL);
} else if ($pid > 0) { // 父进程
	pcntl_wait($status); // 阻塞父进程,直到子进程结束
	echo 'parent'.PHP_EOL;
	exit;
} else {
	// 子进程逻辑
	// 结束子进程前，防止生成僵尸进程
	echo 'child'.PHP_EOL;
	if (function_exists('posix_kill')){
		posix_kill(getmypid(),SIGTERM); // 通过信号kill自己
	} else {
		system('kill -9 '.getmypid()); // 在父进程kill子进程
	}
	exit;
}
