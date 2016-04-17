<?php

namespace View\Engine\Util;


/**
 * @license Apache
 * @author Nike Madrid
 * @copyright Nike Madrid
 */


class Echos
{
    /**
     * @param string $content html
     * @return string
     */
    
    public static function content($content)
    {
        if(preg_match_all('/\{\!\s(.*)?\!\}/', $content) !== 0){
    
            $dd = preg_replace_callback('/\{\!\s(.*)\s\!\}/', function($m){
    
                $value = array_shift($m);
    
                return str_replace('.', '->', $value);
    
            }, $content);
    
            return self::replace('/\{\!\s(.*)?\s\!\}/', '<?php echo $1; ?>', $dd);
        }
    
        return $content;
    }
    
    /**
     * @param string $pattern
     * @param string $replace
     * @param string $str
     * @return string
     */
    
    private static function replace($pattern, $replace, $str)
    {
        return preg_replace($pattern, $replace, $str);
    }
}