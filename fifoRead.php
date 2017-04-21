<?php
$pipe = './my_pipe.pip';
while(1){
	$handler = fopen($pipe,'r');
	if(is_resource($handler)) {
		while(1){
			echo '========================='.PHP_EOL;
			echo fread($handler,1024).PHP_EOL;
			echo '#########################'.PHP_EOL;
			sleep(1);
		}
	}
}
