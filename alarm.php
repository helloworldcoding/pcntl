<?php
function signalHandler($signo)
{
	echo 'signal code '.$signo.PHP_EOL;
	pcntl_alarm(1);
}

pcntl_signal(SIGALRM,'signalHandler');
pcntl_alarm(1);
//pcntl_signal_dispatch();
//sleep(100);
for(;;){
	pcntl_signal_dispatch(); // 要放在循环里，为了检测是否有新的信号等待dispatching
}
