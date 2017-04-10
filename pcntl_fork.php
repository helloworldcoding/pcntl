<?php
	
	$i = 0;
	while ($i < 3) {
		$pid = pcntl_fork();
		switch($pid) {
			case -1 :
				echo 'counld not fork';
				break;
			case 0 :
				//sleep(3);
				echo 'this is child'."$i\n";
				break;
			default:
				echo 'this parent'."$i\n";
		}
		$i++;
	}
