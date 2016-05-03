<?php

namespace View\Engine;

use View\View;
use View\Compile\Compile;
use View\Engine\Util\Outlet;
use View\Engine\Util\ViewExt;
use View\Compile\Cache;
use View\Content\TemplateFile;
use View\Content\Data;


/**
 * @license MIT
 * @author Nike Madrid
 * @copyright Nike Madrid
 */


class Engine
{
    use Cache;
    
    /**
     * @var string
     */
    
    protected $echos;

    /**
     * @var View
     */
    
	protected $view;

	/**
	 * @var TemplateFile
	 */
	
	protected $templateFile;
	
	/**
	 * @var string|null nombre del arhivo extendido
	 */
	
	protected $extendsFile;

	/**
	 * @var Compile
	 */
	
	protected $compile;

	/**
	 * @var string
	 */
	
	protected $scope;

	/**
	 * @param View $view
	 */
	
	public function __construct(View $view)
	{
		$this->view = $view;
		$this->compile = new Compile($view->getVP());
		$this->templateFile = $view->getTemplateFile();
	}
	
	public function create($args = null)
	{
		$arr_t = $this->templateFile->getTemplate();
		$vars = $this->view->getAssign();
		
		ob_start();
		
		if(isset($vars[0]) && $vars[0] instanceof Data){
		    extract(array_shift($vars)->getData()); // add data
		}
		
		extract($vars);
		
		if($arr_t['is']){

		    $template = $this->extending(new ViewExt(
		        $this->view->getVP(),
		        $arr_t['filename']
		    ));
		    
		    if($this->getOptions('cache')) {
    			include $this->save([$this->getPathCompile(), $this->templateFile->getFileName(), $this->extendsFile], $arr_t['filename'], $template);
		
		    }else{
		        
			    eval("?> $template <?php ");
		        
		    }
		}else{
		    include $arr_t['filename'];
		}
		
		$content = ob_get_contents();

		ob_end_clean();
		
		$this->echos = $content;
	}
	
	/**
	 * @param ViewExt $extends
	 * @throws \LogicException
	 * @return string
	 */
	
	public function extending(ViewExt $extends)
	{
		$v_e = "";
		if($extends->pregExtends()){
			$file = $extends->contentHTML($this->extendsFile = $extends->getExtendsFile());
			$v_e .= $this->compiler($file);
		}
		
		$outlet = Outlet::changeBlockInYield(
			$this->compiler($extends->getContent()), $v_e, $this->view->getAssign()
		);
		
		return is_object($outlet) ? $outlet->error() : $outlet;
	}
	
	/**
	 * @param string $content
	 * @return string
	 */
	
	public function compiler($content)
	{
		$comments = $this->compile->comments($content);
		
		return $this->compile->funcPHP(
			$this->compile->funcAssets($comments)
		);
	}
	
	/**
	 * @param string $key
	 * @return multitype:string|array|null
	 */
	
	public function getOptions($key = '')
	{
	    if(empty($key)) {
	       return $this->view->getOptions();
	    }
	    return isset($this->view->getOptions()[$key]) ?  $this->view->getOptions()[$key] : null;
	}
	
	/**
	 * @return string
	 */
	
	public function getPathCompile()
	{
	    $getSlash = substr(strrchr($this->getOptions('compile'), '/'), 1);
	    if($getSlash == false){
	        return $this->getOptions('compile');
	    }
	    return $this->getOptions('compile') . '/';
	}
	
	/**
	 * @return string
	 */
	
	public function __toString()
	{
	    return $this->echos;
	}
}