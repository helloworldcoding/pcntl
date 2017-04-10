<?php
// pcntl_signal_dispatch() 调用每个等待信号通过pcntl_signal安装的处理器

echo '安装信号处理器..'.PHP_EOL;
pcntl_signal(SIGHUP, function($signo){
	echo "信号处理器被调用\n";
});

echo "为自己生成SIGHUP信号..\n";
posix_kill(posix_getpid(), SIGHUP);
//posix_kill(17336, SIGHUP); // 给指定的进程发送信号

echo "分发...\n";
pcntl_signal_dispatch();
echo "完成\n";
