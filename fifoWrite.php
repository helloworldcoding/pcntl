<?php
$pipe = './my_pipe.pip';
while(1){
	$handler = fopen($pipe,'w');
	if(is_resource($handler)) {
		$count = 0;
		while(1){
			$count++;
			$data = ' '.$count.'-';
			$res  = fwrite($handler,$data);
		}
	}
}
