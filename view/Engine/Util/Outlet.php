<?php

namespace View\Engine\Util;


use LogicException;
use RuntimeException;


/**
 * @license Apache
 * @author Nike Madrid
 * @copyright Nike Madrid
 */


class Outlet
{
    /**
     * @var array guarda la claves de la salida
     */
    
	protected static $keepKey = [];
	
	/**
	 * @var string relative view
	 */
	
	protected $relativeFileHTML;
	
	/**
	 * @param string $content html content
	 */
	
	public function __construct($content)
	{
	    $this->relativeFileHTML = $content;
	}
	
	/**
	 * cambia las salidas por los bloques de codigo
	 * 
	 * @static metodo
	 * @param string $r_file relative html
	 * @param string $e_file extends file
	 * @throws RuntimeException
	 * @throws LogicException
	 * @return mixed|unknown
	 */
	
	public static function changeBlockInYield($r_file, $e_file)
	{
		if( preg_match_all('/\%\{(yield?)\s*(.*)?\}/i', $e_file, $m) ){
		    
    		$block = Block::content($r_file);
    		
    		if($block === false){
    			throw new RuntimeException('hubo un error al compilar los block en '. __METHOD__);
    		}
    		
			$name = array_pop($m);
			foreach($name as $index => $contentName){
				$name[$index] = $n = Quote::quotationMarks($contentName);
				if(!array_key_exists('__INICIALIZE_BLOCK__'.$n, $block)){
					throw new \OutOfBoundsException("hay una salida con el valor de [{$n}] sin especificar el bloque de la salida [{$n}]");
				}
				array_push(self::$keepKey, '__INICIALIZE_BLOCK__'.$n);
			}
			
			if(count(self::$keepKey) !== count($block)){
				throw new LogicException("hay salidas de bloque sin especificar");
			}
			$outlet = str_replace(array_shift($m), $block, $e_file);
			
			return Echos::content($outlet);
		}
		
		return new static(Echos::content($r_file));
	}
	
	/**
	 * manda un throw en caso de ejecutar bloques sin extender un archivo
	 * 
	 * @access public
	 * @throws LogicException
	 * @return string
	 */
	
	public function error()
	{
	    if(preg_match_all('/\%\{(block?)\s*(.*)?\}/i', $this->relativeFileHTML) > 0){
	       
            throw new LogicException("intentado ejecutar lineas de bloque, sin haber extendido un archivo para las salidas de bloque de codigo");
	        
	    }
	    return $this->relativeFileHTML;
	}
	
	/**
	 * @access public
	 * @return array
	 */
	
	public function keyBlock()
	{
	    return self::$keepKey;
	}
}