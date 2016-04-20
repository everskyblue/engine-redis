<?php

namespace View;


use Exception;
use View\Content\Data;
use View\Content\TemplateFile;


/**
 * @license Apache
 * @author Nike Madrid
 * @copyright Nike Madrid
 */


class View implements IRedis
{
    /**
     * @var string
     */
     
    protected $pathView;
    
    /**
     * @var array
     */
     
    protected $optionsDefault = [
    
    	'compile' => '/',
    	'cache' => true
    
    ];
    
    /**
     * @var array
     */
     
    protected $options = [];
    
    /**
     * @var string
     */
     
     protected $templateFile;
     
     /**
      * @var array
      */
      
     protected $assign = [];
	
	/**
	 * 
	 * @param unknown $path
	 * @param array $options
	 */
     
	 public function __construct($path, array $options)
     {
    	$this->setPath($path);
    	$this->options($options);
     }
	
     /**
      * @see \View\IRedis::setPath()
      */	
      
	 public function setPath($path)
	 {
		$this->pathView = $path;
	 }
	
	/**
	 * @see \View\IRedis::options()
	 */
	 
	public function options(array $options)
	{
		if(!isset($options['compile'])){
			throw new Exception("path compile no fount");
		}
		$this->options = array_merge($this->optionsDefault, $options);
	}
	
	/**
	 * @see \View\IRedis::render()
	 */
	
	public function render($tpl, $vars = array())
	{
		if(!($tpl instanceof TemplateFile))
			$tpl = new TemplateFile($this->getVP() . $tpl);
		 
		if(!$tpl->exists())
			throw new Exception("file no exists or no fount {$tpl->getFile()}");
		
		if((!empty($vars) && !($vars instanceof Data)))
			$vars = new Data($vars);
			
		$this->templateFile = $tpl;
		
		if(!empty($vars) || is_object($vars)){
			$this->assign($vars);
		}
		
		return $this;
	}
	
	/**
	 * @see \View\IRedis::assign()
	 */
	 
	public function assign($vars)
	{
		if(!($vars instanceof Data)){
			$vars = new Data($vars);
		}
		$this->assign[] = $vars;
		
		return $this;
	}
	
	/**
	 * @return array
	 */
	 
	protected function defaultAssign()
	{
		return [
			'version' => self::REDIS_VERSION,
			'charset' => self::REDIS_CHARSET,
			'time' => microtime()
		];
	}
	
	/**
	 * @return string
	 */
	
	public function getVP()
	{
		$getSlash = substr(strrchr($this->pathView, '/'), 1);
		if($getSlash == false){
			return $this->pathView;
		}
		return $this->pathView . '/';
	}
	
	/**
	 * @param string $name method
	 * @param array $args
	 * @throws Exception
	 */
	
	public function __call($name, $args)
	{
		$this->assign = $this->assign + $this->defaultAssign();
		
		$engine = new Engine\Engine($this);
		
		if(!method_exists($engine, $name)){
			throw new Exception("metodo {$name} no definido");
		}
		
		$engine->$name($args);
		
		echo $engine . PHP_EOL;
	}
	
	/**
	 * @return string
	 */
	
	public function getTemplateFile()
	{
		return $this->templateFile;
	}
	
	/**
	 * @return array
	 */
	
	public function getAssign()
	{
		return $this->assign;
	}
	
	/**
	 * @return array
	 */
	
	public function getOptions()
	{
	    return $this->options;
	}
}