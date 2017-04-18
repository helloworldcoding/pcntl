<?php
class pipe
{
	protected $path = '';
	protected $phandler;
	protected $writehandler;

	public function __construct($name='pipe', $path='/tmp', $cover=false, $mode = 0666 )
	{
		$path = rtrim($path,'/');
		$fifoPath = $path.'/'.$name.'.pipe';
		if (file_exists($fifoPath)) {
			if ($cover) {
				unlink($fifoPath);
			} else {
				$this->path= $fifoPath;
				return $fifoPath;
				//throw  new Exception($fifoPath.' exists already!'.PHP_EOL);
			}
		}

		if (posix_mkfifo($fifoPath, $mode)) {
			$this->path= $fifoPath;
		} else {
			throw  new Exception($fifoPath.' create failed!'.PHP_EOL);
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
		$this->open('r', $blocking);
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
		if(is_resource($this->writehandler)){
			fclose($this->writehandler);
		}
		if(is_resource($this->phandler)){
			fclose($this->phandler);
		}
		return true;
	}

	public function delete()
	{
		return unlink($this->path);
	}

	public function __destruct()
	{
		$this->close();
		//return unlink($this->path);
	}

}
