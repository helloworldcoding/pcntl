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
	public function __construct($path = '/tmp/weicool.pipe', $cover = false, $mode = 0666)
	{
		if (file_exists($path)) {
			if ($cover) {
				unlink($path);
			} else {
				$this->path = $path;
			}
		}

		if (posix_mkfifo($path,$mode)) {
			$this->path = $path;
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
	public function open($mode = 'r')
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
		return $this->open('r');
	}

	/**
	 * 已写的方式打开管道
	 *
	 * @return resource
	 */
	public function writeOpen()
	{
		return $this->open('w');
	}

	public function readOne($byte = 1024);
	public function readAll();
	public function write($data);
	public function close();
	public function remove();

	public function pipeGetContents();
	public function pipePutContents($data);



}
