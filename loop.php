<?php
while(true) {
	echo 'this pid = '.posix_getpid().PHP_EOL;
	sleep(2);
}
