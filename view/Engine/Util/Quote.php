<?php

namespace View\Engine\Util;


/**
 * @license Apache
 * @author Nike Madrid
 * @copyright Nike Madrid
 */


class Quote
{
    /**
     * eliminar la comillas
     * 
     * @param string $quote
     * @return string
     */
    
    public static function quotationMarks($quote)
    {
        return preg_replace('/\'|\"(.*)?\"|\'/', '$1', $quote);
    }
    

    /**
     * eliminar los espacios de los bloques
     *
     * @param string $c content html
     * @return string
     */
    
    public static function space($c)
    {
        $search = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s');
        $replace = array('>','<','\\1');
        $getReplace = preg_replace($search, $replace, $c);
        $trim = str_replace("> <", "><", $getReplace);
        
        return $trim;
    }
}

?>