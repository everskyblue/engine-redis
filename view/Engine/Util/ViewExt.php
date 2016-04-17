<?php

namespace View\Engine\Util;

use Exception;
use View\Content\TemplateFile;


/**
 * @license Apache
 * @author Nike Madrid
 * @copyright Nike Madrid
 */


class ViewExt
{
	/**
	 * @const string
	 */
	 
	const PHP_OPEN_TAG = '<?php';
	
	/**
	 * @const string
	 */
	 
	const PHP_CLOSE_TAG = '?>';
	
	/**
	 * @var string directory from the view
	 */
	 
	protected $dir;
	
	/**
	 * @var string file content
	 */
	 
	protected $content;
	
	/**
	 * @var array save name function and quote tag function yes exists
	 */
	 
	protected $func = [];
	
	/**
	 * @var string file extends from content file
	 */
	 
	protected $extendsFile;
	
	/**
	 * @param string $dir
	 * @param string $file
	 */
	 
	public function __construct($dir, $file)
	{
		$this->dir = $dir;
		$this->content = file_exists($file) ? $this->contentHTML($file) : $file;
	}
	
	/**
	 * @access public 
	 * @param string $c html content
	 */
	 
	public function setContent($c)
	{
		$this->content = $c;
	}
	
	/**
	 * @access public
	 * @return int simulator boolean
	 * @throws Exception
	 */
	 
	public function pregExtends() 
	{
		if(preg_match('/\%\{(extends?)\s(.*)?\}/i', $this->content, $m)){
           	 # nombre del file extends engine
           	 $func = array_shift($m); 
           	 $nameFile = str_replace(
           	 	'.', 
           	 	'/', 
           	 	preg_replace('/\'|\"(.*)?\"|\'/', '$1', $file = array_pop($m))
           	 );
           	 
           	 $dirAndNf = $this->dir . $nameFile;
           	 
           	 if(file_exists($dirAndNf . TemplateFile::REDIS_T)) {
           	 	$this->extendsFile = $dirAndNf . TemplateFile::REDIS_T;
           	 }elseif(file_exists($dirAndNf . TemplateFile::PHP_T)){
           	 	$this->extendsFile = $dirAndNf . TemplateFile::PHP_T;
           	 	$this->func['quote.tag'] = [
           	 		true,
           	 		self::PHP_OPEN_TAG, 
           	 		self::PHP_CLOSE_TAG
           	 	];
           	 }else{
           	 	throw new Exception("archivo a extender {$nameFile} no encontrado");
           	 }
           	 
           	 $this->func[array_shift($m)] = $this->extendsFile;
           	 
           	 $this->setContent(str_replace($func, '', $this->content));
           	 
           	 return 1;
           }
           return 0;
	}
	
	/**
	 * @access public 
	 * @return string
	 */
	 
	public function getExtendsFile()
	{
		return $this->extendsFile;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	 
	public function getFunc()
	{
		return $this->func;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	 
	public function getContent()
	{
		return $this->content;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	
	public function getDir()
	{
		return $this->dir;
	}
	
	/**
	 * @access public
	 * @return string content file
	 */
	 
	public function contentHTML($f)
	{
		return file_get_contents($f);
	}
}