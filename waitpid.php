<?php
// 使用一个waitpid函数，等待全部子进程退出，防止僵尸进程的例子

$childs = [];

$num = 3;

for($i = 0; $i < $num; $i++) {
	$pid = pcntl_fork();
	$id = posix_getpid();
	$ppid = posix_getppid();
	if ($pid == -1) {
		die('failed to fork');
	}
	if ($pid) { // 主进程
		//pcntl_wait($status);
		echo "parent pid={$id} ppid={$ppid} ",PHP_EOL;
		$childs[] = $pid;
	} else { // 子进程
		sleep($i+1);
		echo "child pid={$id} ppid={$ppid} sleep $i+1",PHP_EOL;

		// 子进程exit，不然就会再次进入循环，情况变得复杂
		exit();
	}
}

// 这个while循环，不能去掉,不然就产生僵尸进程了。
while(count($childs)){
	forEach($childs as $k => $pid) {
		// 加了参数WNOHANG，父进程就不会等待子进程结束了。
		$res = pcntl_waitpid($pid, $status, WNOHANG);
		// -1 代表error,大于0代表子进程已退出，返回的是pid,非阻塞时0代表没有取到退出子进程
		if ( -1 == $res || $res > 0) {
			unset($childs[$k]);
		}
		//sleep(1);
	}
}
