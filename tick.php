<?php
declare(ticks=1);

// a function called on each tick event
//
function tick_handler()
{
	echo "tick_handler() called".PHP_EOL;
}

register_tick_function('tick_handler');

$a = 1;

if ($a >0) {
	$a += 2;
	print($a);
}