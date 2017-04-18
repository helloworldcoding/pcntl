<?php
require_once './pipeClass.php';

class pipMultiCurl
{
	protected $url;
	protected $pipeName;
	protected $child=[];
	protected $result=[]; // 计算的结果

	public function __construct($pipeName='multi-read', $url='')
	{
		$this->pipeName = $pipeName;
		$this->url  = $url;
	}

	public function setUrl($url)
	{
		$this->url = $url;
	}

	public function getTitle()
	{
		$url  = $this->url;
		foreach($url as $k => $item) {
			$pid = pcntl_fork();
			if ($pid ==  0) {
				$pipe = new pipe($this->pipeName,'./');
				$id = getmypid();
				$pipe->writeOpen();
				$pipe->write($k.' pid:'.$id.PHP_EOL);
				$pipe->close();
				exit(0);
			} else if ($pid > 0) {
				$this->child[] = $pid;
			}
		}
		return $this->getResult();
	}

	public function getResult()
	{
		$child = $this->child;
		$pipe = new pipe($this->pipeName,'./');
		$pipe->open();
		echo 'get all begin'.PHP_EOL;
		while(count($child)) {
			foreach($child as $k => $pid){
				$res = pcntl_waitpid($pid,$status,WNOHANG);
				if ( -1 == $res || $res > 0 ) {
					unset($child[$k]);
				}
			}
			$data = $pipe->readOne();
			if ($data) {
				$this->result[] = $data;
			}
		}
		$pipe->close();
		echo 'get all end'.PHP_EOL;
		$pipe->delete();
		return $this->result;
	}

}

$obj = new pipMultiCurl();
$obj->setUrl([1,2,3]);
$res = $obj->getTitle();
print_r($res);


