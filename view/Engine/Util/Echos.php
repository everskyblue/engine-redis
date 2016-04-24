<?php

namespace View\Engine\Util;


use BadMethodCallException;
use LogicException;
use OutOfBoundsException;
use RuntimeException;
use View\Content\Data;


/**
 * @license Apache 
 * @author Nike Madrid
 * @copyright Nike Madrid
 */


class Echos
{
    
    /**
     * @param string $c
     * @param string $v
     * @return boolean
     */
    
    protected static function checkVarStructureControl($c, $v)
    {
        preg_match_all('/[foreach|while|if]+\((.*)?\)/i', $c, $m);
        
        $vars = array_pop($m);
        
        foreach($vars as $vars){
            if(preg_match_all('/\$[a-zA-Z_]+/i', $vars, $mv)){
                if(in_array('$'.$v, $mv[0])){
                    return true;
                }
            }
        }
        
        return false;
    }
    
    
    /**
     * @param string $content
     * @throws RuntimeException
     * @return string
     */
    
    public function withErrors($content)
    {
        
        if(preg_match_All('/echo\s*\$*[\'|\"][a-zA-Z0-9]*/', $content, $m)){
            if(!empty(array_shift($m)[0])){
                throw new RuntimeException('error quote, verifica tu codigo');
            }
        }
        return $content;
    }
    
    /**
     * @param string $variable
     * @param array $data
     * @param array $allData
     * @return boolean|string
     */
    
    protected static function muchVariable($variable, array $data, array $allData)
    {
        if(strstr($variable, ',')) {
            foreach (explode(',', $variable) as $tvars){
                $tvars = str_replace(array(' ', ','), '', $tvars);
                if(!empty($tvars) && isset($data[$tvars]) && !isset($allData[$tvars])){
                    return $tvars;
                }
            }
            return false;
        }
        return $variable;
        
    }
    
    /**
     * @param string $content html
     * @return string
     */
    
    public static function content($content, $data = array())
    {
        if(preg_match_all('/\{\!\s(.*)?\!\}/', $content) !== 0){
            
            $dd = preg_replace_callback('/\{\!\s(.*)\s\!\}/', function($m) use($content, $data){
    
                $value = array_shift($m);
                
                if(!empty($data)){
                    
                    if(isset($data[0]) && $data[0] instanceof Data){
                        
                        $idata = array_shift($data);
                        
                        if(preg_match('/\{\!\s\'|\"(.*)\"|\'\s\!\}/', $value) === 0){
                            
                            $getNameVar = str_replace(array(/*'.',*/ '$'), array(/*'->',*/ ''), preg_replace('/\{\!\s(.*)?\s\!\}/', '$1', $value));
                            
                            $str = strstr($getNameVar, '->', true);
                            $allData = $idata->getData();
                            
                            if(( $str !== false )){

                                die($getNameVar);
                                $multi = array_filter(explode('->', $getNameVar));
                                
                                $nameObject = array_shift($multi);

                                foreach ($multi as $pObject) {
                                    $cll = get_class($allData[$nameObject]);
                                    if(strstr($pObject, '(') !== false){
                                        $normalizeMethod = preg_replace('/(.*)?\((.*)?\)/', '$1', $pObject);
                                        
                                        if((!method_exists($allData[$nameObject], $normalizeMethod) && !is_object($allData[$nameObject]->$normalizeMethod))) {
                                            throw new \BadMethodCallException("intentando llamar a un no objeto {$cll}::{$normalizeMethod}");
                                        }
                                    }
                                    
                                    if(strstr($pObject, '(') === false){
                                        $coincidencia = self::muchVariable($nameObject, $data, $allData);
                                        if(isset($allData[$coincidencia]) && !property_exists($allData[$coincidencia], $pObject)){
                                            throw new \LogicException("propieda {$pObject} no definida en {$cll}");
                                        }
                                    }
                                    
                                }
                                
                                
                            }else if($str === false && (!isset($data[$getNameVar]) && !isset($allData[$getNameVar]))){
                                if(!self::checkVarStructureControl($content, $getNameVar)){
                                    if($coincidencia = self::muchVariable($getNameVar, $data, $allData)) {
                                        throw new OutOfBoundsException("la variable o clave [{$coincidencia}] no existe!");
                                    }
                                }
                                
                            }
                        }
                        
                    }
                }
                return str_replace('.', '.', $value);
    
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