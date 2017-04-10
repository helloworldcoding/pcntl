<?php
/**
 * this demo for php fork and pip usage,
 * fork use to create child process and pipe is used to sychoroize
 * the child process and its main process
 */

define("PC",10); // 进程个数
define("TO",4); // 超时
define("TS",4); // 事件跨度，用于模拟任务延迟

// 创建管道
$pipe = "my_pipe".posix_getpid();
if (!posix_mkfifo($pipe,0666)) {
	die("craete pipe {$pipe} error");
}

// 模拟任务并发
for($i = 0; $i < PC; $i++) {
	$pid = pcntl_fork();
	if ($pid == 0) {
		// 子进程过程，写信息到管道
		sleep(rand(1,TS));
		$handler = fopen($pipe,'w');
		fwrite($handler,$i.PHP_EOL);// 当前任务完毕，在管道中写入数据
		fclose($handler);
		exit(0); // 子进程执行完毕
	}
}

// 父进程，读取管道数据
$ph = fopen($pipe,'r');
stream_set_blocking($ph,FALSE);// 将管道设为非堵塞，适用超时机制
$data = ''; //存放管道中的数据
$line = 0; // 行数
$time = time();

while ($line < PC && time() - $time < TO) {
	$temp = fread($ph,1024);
	if(empty($temp)) {
		continue;
	}

	echo "current line : {$temp}".PHP_EOL;
	$line = $line +  count(explode(PHP_EOL,$temp)) -1;
	$data .= $temp;
}

fclose($ph);

// 等待子进程执行完毕，避免出现僵尸进程
$n = 0;
while ($n < PC) {
	$status = -1;
	$pid = pcntl_wait($status, WNOHANG);
	if ($pid > 0) {
		echo "$pid exite".PHP_EOL;
	}
}

