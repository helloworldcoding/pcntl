<?php
require_once './fifoPipeClass.php';

class pipeMultiProcess
{
	protected $process = []; // 子进程
	protected $child   = []; // 子进程pid数组
	protected $result  = []; // 计算的结果

	public function __construct($process = [])
	{
		$this->process  = $process;
	}

	/**
	 * 设置子进程
	 */
	public function setProcess($process)
	{
		$this->process = $process;
	}

	/**
	 * fork 子进程
	 */
	public function forkProcess()
	{
		$process  = $this->process;
		foreach($process as $k => $item) {
			$pid = pcntl_fork();
			if ($pid ==  0) {
				$pipe = new fifoPipeClass();
				$id = getmypid();
				$pipe->writeOpen();
				$pipe->write($k.' pid:'.$id.PHP_EOL);
				$pipe->close();
				exit(0);
			} else if ($pid > 0) {
				$this->child[] = $pid;
			}
		}
		return $this;
	}

	/**
	 * 等待子进程结束
	 */
	public function waiteProcess()
	{
		$child = $this->child;
		$pipe  = new fifoPipeClass();
		$pipe->readOpen();
		//echo 'get all begin'.PHP_EOL;
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
		//echo 'get all end'.PHP_EOL;
		$pipe->remove();
		return $this;
	}

	/**
	 * 获取返回结果
	 */
	public function getResult()
	{
		return $this->result;
	}

}

$obj = new pipeMultiProcess();
$arr = range(10,100);
$obj->setProcess($arr);
//$obj->setProcess(['name'=>1,'age'=>2,'sex'=>3,'height'=>'12323','hah'=>'ahsdfasfa']);
$res = $obj->forkProcess()->waiteProcess()->getResult();
print_r($res);


