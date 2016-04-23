<?php

namespace View\Compile;

use LengthException;
use DOMDocument;


/**
 * @license Apache
 * @author Nike Madrid
 * @copyright Nike Madrid
 */


trait Cache
{
    /**
     * @var string crear un control xml
     */
     
    protected $xmlTime = 'time.xml';
    
    /**
     * @var array [timeCache => filenameCache] 
     */
     
    protected $timeCache = [];
    
    /**
     * @var array [updateFile => filename]
     */
     
    protected $timeFirstFile = [];
    
    /**
     * @var DOMDocument
     */
     
     protected $dom;
    
    /**
     * @var DOMDocument node object
     */
     
    protected $nodeCache;
    
    /**
     * @var DOMDocument node object
     */
     
    protected $nodeTemplate;
    
    /**
     * @var string nombre del archivo principal
     */
     
    protected $file;
    
    /**
     * @var string nombre de la cache
     */
     
    protected $fileCache;
    
    /**
     * añade clave y valores  guardando el tiempo de actualizacion del archivo
     *
     * @access private
     * @param DOM $caching
     * @param string $porperty
     */
     
    private function iterateObject($object, $property)
    {
     	foreach($object as $in => $cache){
       	 	foreach($cache->attributes as $i => $attr){
   		 	     $this->{$property}[$attr->value] = substr($cache->nodeValue, 0);
       	 	}
       	}
    }
    
    /**
     * crea y actualiza un documentl xml
     *
     * @access private
     * @param string|null $c content xml
     * @param string|null $sche
     */
     
    private function xml($c = null, $sche = null)
    {
        $this->dom = new DOMDocument();
        
        if(file_exists($this->xmlTime)){
        
            $this->dom->load($this->xmlTime);
            	    	   
            $this->nodeCache = $this->dom->getElementsByTagName('root-cache');
            
            $this->nodeTemplate = $this->dom->getElementsByTagName('filename');
        
        }
        
        switch($sche){
        	case 'create':
        	    
                file_put_contents($this->xmlTime, $c);
                
        		break;
        	
        	default:
            	   
            	$this->iterateObject($this->nodeCache, 'timeCache');
                $this->iterateObject($this->nodeTemplate, 'timeFirstFile');
        	     
        		break;
        }
    	   
    }
    
    /**
     * guarda el archivo compilado y control de archivo xml
     *
     * @access protected 
     * @param array $options
     * @param string $origin filename
     * @param string $content html
     */
     
    protected function save(array $options, $origin, $content)
    {
        $this->file = $origin;
        
        if(count($options) < 2){
            throw new LengthException("el array pasado se necesita 2 o mas elementos");
        }
        
        list($path, $filename) = $options;
        
        $this->fileCache = $save = $path . md5($filename) . '.php';
        
        
       if(!file_exists($save)){
       	
            $this->put($save, $content);
            
        }
        
        if(!file_exists($this->xmlTime)){
        	
        	  $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n<!-- no modificar el documento -->\n";
        	  $xml .= "<schema>\n" . $this->stringXml() . "\n</schema>";
        	  $this->xml($xml, 'create');
        	  
        	  
        }else{
        	
        	$this->xml();
        	
        }
        
        $count = 0;
        $timeCache = array_values($this->timeCache);
        
        foreach($this->timeFirstFile as $update => $file){
       		similar_text($origin, $file, $p);
       		
    		if( (in_array($this->file, $this->timeFirstFile) && $file === $this->file) && $this->isUpdateFile($update) ){
    		    // actualiza el documento xml si se a modificado el archivo
    			$_f = $this->nodeTemplate->item($count);
    			$_ca = $this->nodeCache->item($count);
    			
    			$nFileAt = $this->dom->createElement('filename', $this->file);
    			$nFileAt->setAttribute('update', filemtime($this->file));
    			
    			$nCacheAt = $this->dom->createElement('root-cache', $save);
    			$nCacheAt->setAttribute('created', time());
    			
    			$_f->parentNode->replaceChild($nFileAt, $_f);
    			$_ca->parentNode->replaceChild($nCacheAt, $_ca);
    			
    			$this->xml($this->dom->saveXML(), 'create');
    			
    		    $this->put($save, $content);
    		} 
	   			
	   		if((!in_array($origin, $this->timeFirstFile) || !in_array($save,$timeCache))){
	   			// añade nueva informacion al xml
    	  		$timer = $this->dom->getElementsByTagName('info')->item(0);
    	  		
    	  		$ninfo = $this->dom->createElement('info');
    	  		
                $nFileAt = $this->dom->createElement('filename', $this->file);
   				$nFileAt->setAttribute('update', filemtime($this->file));
   				
   				$nCacheAt = $this->dom->createElement('root-cache', $save);
   				$nCacheAt->setAttribute('created', time());
   				$ninfo->appendChild($nCacheAt);
   				$ninfo->appendChild($nFileAt);
   				
    	  		$timer->parentNode->insertBefore($ninfo, $timer);
    	  		
    	  		$this->xml($this->dom->saveXML(), 'create');
    	  		
    	  		break;
	   		}
	   		
	   		$count++;
        }
    	   
        return $save;
    }
    
    /**
     * comprueba si se ah modificado el documento html
     * 
     * @access protected
     * @return bool
     */
     
    protected function isUpdateFile($upt)
    {
		return (filemtime($this->file) > $upt);
    }
    
    /**
     * @access protected
     * @return string
     */
     
    protected function stringXml()
    {
        $xml = '<info>';
     	 $xml .= '<root-cache crated="'.time().'">'.$this->fileCache.'</root-cache>';
     	 $xml .= '<filename update="'. filemtime($this->file) .'">'. $this->file  . '</filename>';
    	$xml .= '</info>';
        	
    	return $xml;
    }
    
    /**
     * @param string $save nombre de la cache
     * @param string $data contenido a añadir
     * @return string ruta de la cache
     */
    
    protected function put($save, $data)
    {
    
        /*$times = time();
        
        $push = '<?php $times = ' . $times . '; /*if($times < '. filemtime($this->file) .'){ return false; }/ ?>'."\n";*/
        
        file_put_contents($save, $data);
        
        return $save;
    }
    
}