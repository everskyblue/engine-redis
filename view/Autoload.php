<?php

function redisload($class)
{
	$prefix = 'View\\';
	
	$path = dirname(__DIR__) . '/view/';
	
	$lenght = strlen($prefix);
	
	if(strpos($class , '\\') !== false){
		$class = substr($class, $lenght);
	}
	 
	$class = $path . str_replace('\\', '/', $class) . '.php';
	
	if(file_exists($class)){
		include $class;
	}
}

spl_autoload_register('redisload');