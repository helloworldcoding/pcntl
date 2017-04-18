<?php
/**
 * 管道封装类
 *
 * @author  mingazi@163.com
 * @link    www.helloworldcoding.com
 * @since   2017-04-18
 */
class fifoPipeClass
{
	/**
	 * 管道资源
	 */
	protected $handler;

	/**
	 * 管道路径
	 */
	protected $path;

	/**
	 * 是否阻塞，false为非阻塞，true为阻塞
	 */
	protected $block = false;

	/**
	 * 创建管道
	 */
	public function __construct($path = './weicool.pipe', $cover = false, $mode = 0666)
	{
		if (file_exists($path)) {
			if ($cover) {
				unlink($path);
			} else {
				$this->path = $path;
				return $this;
			}
		}

		if (posix_mkfifo($path,$mode)) {
			$this->path = $path;
			return $this;
		} else {
			$this->throwException('create pipe failed');
		}

	}

	/**
	 * 抛异常方法
	 */
	public function throwException($msg = 'failed')
	{
		throw new \Exception($msg);
	}

	/**
	 * 设置阻塞方式
	 * 
	 * @param   bool   $block false为非阻塞，true为阻塞
	 */
	public function setBlock($block = false)
	{
		$this->$block = $block;
	}

	/**
	 * 指定pipe文件路径
	 */
	public function setPath($path)
	{
		if (!file_exists($path)) {
			$msg = $path.' pipe  does not exists';
			$this->throwException($msg);
		}
		$this->path = $path;
	}

	/**
	 * 获取pipe文件路径
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * 打开一个管道
	 *
	 * @param   string   $mode  打开类型
	 */
	public function pipeOpen($mode = 'r')
	{
		$handler = fopen($this->path, $mode);
		if (!is_resource($handler)) {
			$msg = 'open pipe '.$this->path.' falied';
			$this->throwException($msg);
		}
		// 设置阻塞类型
		stream_set_blocking($handler, $this->block);
		$this->handler = $handler;
		return $this;

	}

	/**
	 * 已读的方式打开管道
	 *
	 * @return resource
	 */
	public function readOpen()
	{
		return $this->pipeOpen('r');
	}

	/**
	 * 已写的方式打开管道
	 *
	 * @return resource
	 */
	public function writeOpen()
	{
		return $this->pipeOpen('w');
	}

	/**
	 * 读取一行，或给定的长度
	 */
	public function readOne($byte = 1024)
	{
		$data = fread($this->handler,$byte);
		return $data;
	}

	/**
	 * 读取所有的内容
	 */
	public function readAll()
	{
		$hd = $this->handler;
		$data = '';
		while (!feof($hd)) {
			$data .= fread($hd,1024);
		}
		return $data;
	}

	/**
	 * 写入数据
	 */
	public function write($data)
	{
		$hd = $this->handler;
		try {
			fwrite($hd,$data);
		} catch(\Exception $e) {
			$this->throwException($e->getMessage());
		}
		return $this;
	}

	/**
	 * 关闭管道
	 */
	public function close()
	{
		return fclose($this->handler);
	}

	/**
	 * 删除管道
	 */
	public function remove()
	{
		return unlink($this->path);
	}

	/**
	 * 一次性获取管道中所有的数据
	 *
	 */
	public function pipeGetContents()
	{
		$data = '';
		$this->readOpen();
		$handler = $this->handler;
		while( !feof($handler)) {
			$data .= fread($handler,1024);
		}
		$this->close();
		return $data;
	}

	/**
	 * 一次性写入数据到管道中
	 *
	 * @param   string   $data   输入数据
	 */
	public function pipePutContents($data)
	{
		return $this->writeOpen()->write($data)->close();
	}
}
