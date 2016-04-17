<?php

namespace View\Content;


/**
 * @license Apache
 * @author Nike Madrid
 * @copyright Nike Madrid
 */


class TemplateFile
{
	/**
	 * @const string
	 */
	 
	const REDIS_T = '.redis.php';
	
	/**
	 * @const string
	 */
	 
	const PHP_T = '.php';
	
	/**
	 * @var string file
	 */
	 
	protected $file;
	
	/**
	 * @var string
	 */
	
	protected $fileName;
	
	/**
	 * @var array guarda el tipi de template
	 */
	 
	protected $template = [];
	 
	/**
	 * @param string $file
	 */
	 
	public function __construct($file)
	{ 
		$this->file = $this->slash($this->withFile($file));
	}
	
	/**
	 * elimina la extension si tiene
	 *
	 * @access private
	 * @param string $file
	 * @return string
	 */
	 
	private function withFile($file)
	{
		$ext = pathinfo($file);
		if(isset($ext['extension'])){
			$file = substr($file, 0, - strlen($ext['extension']) - 1);
		}
		$this->fileName = $ext['filename'];
		
		return $file;
	}
	
	/**
	 * @access public
	 * @return bool
	 */
	 
	public function exists()
	{
		if(file_exists($this->file . self::REDIS_T)){
			$this->template = ['filename' => $this->file . self::REDIS_T, 'is' => true];
			
			return true;
		}elseif(file_exists($this->file . self::PHP_T)){
			 $this->template = ['filename' => $this->file . self::PHP_T, 'is' => false];
			 
			 return true;
		}
		return false;
	}
	
	/**
	 * @access private
	 * @return string
	 */
	 
	private function slash($f)
	{
		return str_replace('.', '/', $f);
	}
	
	/**
	 * @access public
	 * @return array
	 */
	 
	public function getTemplate()
	{
		return $this->template;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	 
	public function getFile()
	{
		return $this->file;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	
	public function getFileName()
	{
	    return $this->fileName;
	}
}