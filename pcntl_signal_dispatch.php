<?php

// 定义一个信号处理器
function signalHandler($signo)
{
	echo 'signal code '.$signo.PHP_EOL;	
}

pcntl_signal(SIGINT, 'signalHandler');

// php < 5.3，没有pcntl_signal_dispatch函数
if (!function_exists('pcntl_signal_dispatch')){
	declare(ticks=1);
} 


$i = 0;
while ($i<3) {
	$s = sleep(2);
	echo 'left '.$s.' seconds'.PHP_EOL;

	// do sth;
	for ($i=0;$i<5;$i++){
		echo $i.PHP_EOL;
		sleep(1);
	}
	
	if (function_exists('pcntl_signal_dispatch')){
		pcntl_signal_dispatch();
	} 

	$i++;
}

