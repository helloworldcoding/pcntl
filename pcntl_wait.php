<?php

	$i = 0;
	$n = 3;
	while($i < $n) {
		$pid = pcntl_fork();

		// 父进程和子进程都会执行以下代码
		if ($pid == -1) { // 创建子进程错误，返回-1
			die('could not fork');
		} else if ($pid) {
			// 父进程会得到子进程号，所以这里是父进程执行的逻辑
			pcntl_wait($status,WNOHANG); // 父进程必须等待一个子进程退出后，再创建下一个子进程。
			//pcntl_waitpid(0,$status,WNOHANG); // 父进程必须等待一个子进程退出后，再创建下一个子进程。
			$cid = $pid; // 为子进程的ID
			$pid = posix_getpid(); // pid 与mypid一样，是当前进程Id
			$myid = getmypid();
			$ppid = posix_getppid(); // 进程的父级ID
			$time = microtime(true);
			echo "I am parent cid:$cid   myid:$myid pid:$pid ppid:$ppid i:$i $time \n";
		} else {
			// 子进程得到的$pid 为0，所以这里是子进程的逻辑
			$cid = $pid;
			$pid = posix_getpid();
			$ppid = posix_getppid();
			$myid = getmypid();
			$time = microtime(true);
			//echo "I am chile i=$i myid=$myid --pid=$pid --ppid=$ppid\n";
			echo "I am child cid:$cid   myid:$myid pid:$pid ppid:$ppid i:$i  $time \n";
			//exit;
			sleep(2);
			//
		}
		$i++;
	}
