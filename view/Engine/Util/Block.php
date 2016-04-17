<?php

namespace View\Engine\Util;


/**
 * @license Apache
 * @author Nike Madrid
 * @copyright Nike Madrid
 */


class Block
{
    
	/**
	 * @static metodo
	 * @param string $b_c block content
	 * @return string|array|bool
	 */
	 
	public static function content($b_c)
	{
		if(preg_match_all('/((\\%\{([block?|a-zA-Z_]*\s(.*)?)\\})*(<[^>]+>|<[^>]+>(.*)<\/[^>]+>|(.*)<\/[^>]+>)*?(\\%\{([endblock?|a-zA-Z]*)?\\})?)/iU', $b_c, $m, PREG_PATTERN_ORDER)){
		
			$html = array_shift($m); // array
			array_shift($m);
			
			$func_redis_first = array_filter(array_shift($m));
			array_shift($m);
			
			$name = array_filter(array_shift($m));
			
			$normal_html = Quote::space(implode("", $html));
			
			$arr = 'array(';
			
    			$key_element = '__INICIALIZE_BLOCK__';
    			foreach($func_redis_first as $index => $fr){
    				$normal_html = str_replace($fr, '\'' . $key_element . Quote::quotationMarks($name[$index]) . '\' => \'', $normal_html);
    			}
    			
    			array_shift($m); 
    			array_shift($m); 
    			array_shift($m);
    			
    			$end_func = array_filter(array_shift($m));
    			
    			$key_end = '__END_BLOCK__';
    			foreach($end_func as $index => $end){
    				$normal_html = str_replace($end, ' ' . /*$key_end. static::pregQuote($name[$index]) .*/ '\',', $normal_html);
    			}
    			$normal_html = substr($normal_html, 0, - 1);
    			
    		$arr .= $normal_html . ');';
    		
			unset($m);
			
			return @eval("return {$arr}");
		} 
		return $b_c;
	}
	
}