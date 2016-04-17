<?php

namespace View;


/**
 * @license Apache
 * @author Nike Madrid
 * @copyright Nike Madrid
 */


interface IRedis
{

    /**
     * @var string
     */
    
	const REDIS_VERSION = 1.0;
	
	/**
	 * @var string
	 */
	
	const REDIS_CHARSET = 'utf-8';
	
	/**
	 * @param string $path
	 */
	
	public function setPath($path);
	
	/**
	 * @param array $options
	 */
	
	public function options(array $options);
	
	/**
	 * @param string  $tpl
	 * @param array|Data $vars
	 */
	
	public function render($tpl, $vars = array());
	
	/**
	 * @param array|Data $vars
	 */
	
	public function assign($vars);
}