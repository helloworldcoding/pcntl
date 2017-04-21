<?php
$pid = pcntl_fork();
if($pid == 0) {
	echo getmypid();
	sleep(1);
	exit(0);
} else if ($pid > 0) {
	$count = 0;
	while($count < 10){
		sleep(1);
		$count ++;
	}
	exit(0);
}
