<?php
class pipe
{
	protected $path = '';
	protected $phandler;

	public function __construct($name='pipe', $path='/tmp', $cover=false, $mode = 0666 )
	{
		$path = rtrim($path,'/');
		$fifoPath = $path.'/'.$name.getmypid();
		if (file_exists($fifoPath)) {
			if ($cover) {
				unlink($fifoPath);
			} else {
				throw  new Exception($fifoPath.' exists already!'.PHP_EOL);
			}
		} else {
			if (posix_mkfifo($fifoPath, $mode)) {
				$this->path= $fifoPath;
			} else {
				throw  new Exception($fifoPath.' create failed!'.PHP_EOL);
			}
		}
	}

	public function getPath()
	{
		return $this->path;
	}

	public function open($mode='r', $blocking=false)
	{
		$ph = fopen($this->path, $mode);
		if(!is_resource($ph)) {
			throw new \Exception('open pipe failed'.PHP_EOL);
		}
		stream_set_blocking($ph,$blocking);
		$this->phandler = $ph;
		return $this;
	}

	public function readOpen($blocking=false)
	{
		$this->open('r');
		return $this;
	}
	public function writeOpen($blocking=false)
	{
		$this->writehandler = fopen($this->path,'w');
		stream_set_blocking($this->writehandler,$blocking);
		return $this;
	}

	public function readOne($byte=1024)
	{
		$line = fread($this->phandler,$byte);
		return $line;
	}

	public function readAll()
	{
		$data = '';
		$ph   = $this->phandler;
		while (!feof($ph)) {
			$data .= fread($ph,1024);
		}
		fclose($ph);
		return $data;
	}

	public function write($data)
	{
		$ph = $this->writehandler;
		fwrite($ph, $data);
		return $this;
	}

	public function writeAll($data)
	{
		$ph = $this->writehandler;
		fwrite($ph, $data);
		fclose($ph);
		return $this;
	}

	public function close()
	{
		fclose($this->writehandler);
		return fclose($this->phandler);
	}

	public function delete()
	{
		return unlink($this->path);
	}

	public function __destruct()
	{
		return unlink($this->path);
	}

}
