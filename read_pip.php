<?php


function createPip($name='my_pipe.pip', $path = './', $mode = 0666)
{
	try {
		$fifoPath = $path.$name;
		if( file_exists($fifoPath)) {
			unlink($fifoPath);		
		}
		if(!posix_mkfifo($fifoPath,$mode)) {
			echo 'create pipe error'.PHP_EOL;
			return false;
		}
		return $fifoPath;
	} catch (\Exception $e){
		echo $e->getMessage();
		return false;
	}
}
function writePip($path,$content = '')
{
	$pipe = fopen($path,'w');
	if($pipe){
		fwrite($pipe,$content);
		fclose($pipe);
		return true;
	}
	echo 'write error'.PHP_EOL;
	return false;
}

function readPip($path = '')
{
	$pipe = fopen($path,'r');
	if($pipe) {
		$data = '';
		while (!feof($pipe)){
			$data .= fread($pipe,1024);
		}
		return $data;
	}
	echo 'read error'.PHP_EOL;
	return false;
}

$path = createPip();
var_dump($path);
writePip($path,'haha');
$data = readPip($path);
var_dump($data);
