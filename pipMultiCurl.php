<?php
require_once './pipeClass.php';

class pipMultiCurl
{
	protected $url;
	protected $pipe;
	protected $child=[];

	public function __construct($pipeName='multi-read', $url='')
	{
		$this->pipe = new pipe($pipeName,'./');
		$this->url  = $url;
	}

	public function setUrl($url)
	{
		$this->url = $url;
	}

	public function getTitle()
	{
		$pipe = $this->pipe;
		$url  = $this->url;
		foreach($url as $k => $item) {
			$pid = pcntl_fork();
			if ($pid ==  0) {
				$id = getmypid();
				$pipe->writeOpen();
				$pipe->write('1pid:'.$id.PHP_EOL);
				$pipe->close();
				$pipe->writeOpen();
				$pipe->write('2pid:'.$id.PHP_EOL);
				$pipe->close();
				echo $id.' finished'.PHP_EOL;
				exit(0);
			} else if ($pid > 0) {
				$this->child[] = $pid;
			}
		}
		$this->getResult();
	}

	public function getResult()
	{
		$child = $this->child;
		$pipe  = $this->pipe;
		while(count($child)) {
			//echo 'count '.count($child).PHP_EOL;
			foreach($child as $k => $pid){
				$res = pcntl_waitpid($pid,$status,WNOHANG);
				var_dump($res);
				if ( -1 == $res || $res > 0 ) {
					unset($child[$k]);
				}
			}
		}
		echo 'get all begin'.PHP_EOL;
		$data = $pipe->open()->readAll();
		var_dump($data);
		echo 'get all end'.PHP_EOL;
		$pipe->delete();
	}
}

$obj = new pipMultiCurl();
$obj->setUrl([1]);
$obj->getTitle();

