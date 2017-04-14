<?php
/**
 * 一个进程，10万次写入数据库
 */

$total = 100000;
$num   = 100;
$per   = $total/$num;

$sql  = '';
$child = '';

$start = microtime(true);
echo 'start '.$start.PHP_EOL;
$link = mysqli_connect('localhost','root','root','yii2advanced');
for($i = 1; $i<= $total; $i++) 
{
	$time = microtime(true);
	$sql = 'insert pcntl_test(rank,time) values ('.$i.','.$time.')';
	mysqli_query($link,$sql);
}
mysqli_close($link);

$end = microtime(true);
echo 'end '.$end.PHP_EOL;
echo 'it takes '.($end - $start).PHP_EOL;
