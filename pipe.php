<?php
/**
 * this demo for php fork and pip usage,
 * fork use to create child process and pipe is used to sychoroize
 * the child process and its main process
 */

define("PC",4); // 进程个数
define("TO",4); // 超时

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
		sleep(rand(3,TO));
		$handler = fopen($pipe,'w');
		//fwrite($handler,$i.PHP_EOL);// 当前任务完毕，在管道中写入数据
		$id = getmypid();
		fwrite($handler,$id.':'.$i.'-');// 当前任务完毕，在管道中写入数据
		fclose($handler);
		exit(0); // 子进程执行完毕
	}
}

// 父进程，读取管道数据
$ph = fopen($pipe,'r');
//stream_set_blocking($ph,FALSE);// 将管道设为非堵塞，适用超时机制
stream_set_blocking($ph,true);// 将管道设为堵塞
$data = ''; //存放管道中的数据
$line = 0; // 行数
$time = time();

while ($line < PC && time() - $time < TO) {
	echo 'line:'.$line.PHP_EOL;
	$temp = fread($ph,1024);
	if(empty($temp)) {
		continue;
	}

	echo "current line : {$temp}".PHP_EOL;
	$line = $line +  count(explode(':',$temp)) - 1;
	echo 'line '.$line.PHP_EOL;
	$data .= $temp;
}


fclose($ph);

unlink($pipe);
echo 'data '.$data.PHP_EOL;
// 等待子进程执行完毕，避免出现僵尸进程
$n = 0;
while ($n < PC) {
	$status = -1;
	$pid = pcntl_wait($status, WNOHANG);
	if ($pid > 0) {
		echo "$pid exite".PHP_EOL;
	}
}

