<?php

namespace View\Compile;


/**
 * @license Apache
 * @author Nike Madrid
 * @copyright Nike Madrid
 */


trait Cache
{
    protected $save;
    
    protected $origin;
    
    protected function save(array $options, $origin, $content)
    {
        $this->origin = $origin;
        
        if(count($options) < 2){
            throw new \Exception("el array pasado se necesita 2 o mas elementos");
        }
        
        list($path, $filename) = $options;
        
        $this->save = $save = $path . md5($filename) . '.php';
        
        
       // if(!file_exists($save)){
            $this->put($save, $content);
        //}
        
        return $save;
    }
    
    protected function put($save, $data)
    {

        $times = time();
        
        $push = '<?php $times = ' . $times . '; if(!($time !== '. filemtime($this->origin) .')){ return; } ?>'."\n";
        
        file_put_contents($save, $push . $data);
        
        return $save;
    }
    
}

?>