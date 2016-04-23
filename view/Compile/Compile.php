<?php

namespace View\Compile;


/**
 * @license Apache
 * @author Nike Madrid
 * @copyright Nike Madrid
 */


class Compile
{
    /**
     * @var multitype:null|string
     */
    
    protected $root;
    
    /**
     * @param string $path tags
     */
    
    public function __construct($path)
    {
        $this->root = $path;
    }
    
    public function comments($tpl)
    {
        return preg_replace('/\#\{(.*)?\s\}/', '<?php /* $1 */ ?>', $tpl);
    }
    
    /**
     * @param string $tpl file compile redis
     * @return mixed
     */
    
    public function extendsContent($tpl)
    {
        return preg_replace('/\%\{(extends()?)\s(.*?)\}/', '<?php $_view_->extendsTemplate($3); ?>', $tpl);
    }
    
    /**
     * @param string $tpl file compile redis
     * @return mixed
     */
    
    public function block($tpl)
    {
        $block = preg_replace('/\%\{(block()?)\s(.*?)\}/', '<?php $_view_->startBlock($3); ?>', $tpl);
    
        return preg_replace('/\%\{(endblock()?)\}/', '<?php $_view_->endBlock(); $_view_->renderer(); ?>', $block);
    }
    
    /**
     * @param string $tpl file compile redis
     * @return mixed
     */
    
    public function push($tpl)
    {
        return preg_replace('/\%\{(yield()?)\s(.*?)\}/', '<?php $_view_->outlet($3); ?>', $tpl);
    }
    
    /**
     * @param string $tpl file compile fucntion php
     * @return mixed
     */
    
    public function funcPHP($tpl)
    {
        if(preg_match('/\%\{([foreach|for|while|if|else]*)\s(.*)?\}/i', $tpl)){
            $control = preg_replace('/\%\{((foreach|for|while|if|elseif()?)\s(.*)?)?\}/', '<?php $2($4): ?>', $tpl);
            $control = preg_replace('/\%\{((else()?)?)?\}/', '<?php $1: ?>', $control);
            $tpl = preg_replace('/\%\{((endforeach|endfor|endwhile|endif|break|continue()?)*)\}/', '<?php $1; ?>', $control);
        }
        if(preg_match('/\%\{([include|include_once|require|require_once]*)\s(.*)?\}/i', $tpl)){
            $tpl = preg_replace('/\%\{((include|include_once|require|require_once()?)\s(.*)?)\}/', '<?php $2 "'.$this->root.'".$4; ?>', $tpl);
        }
    
        return $tpl;
    }
    
    /**
     * @param string $tpl file compile redis function
     * @return mixed
     */
    
    public function funcAssets($tpl)
    {
        $js = preg_replace('/\{\!\s(script_tag()?)\s\((.*?)\)\s\!\}/', '<?php echo $_view_->tagsJs($1); ?>' , $tpl);
    
        return preg_replace('/\{\!\s(link_tag_css()?)\s\((.*?)\)\s\!\}/', '<?php echo $_view_->tagsCss($1); ?>' , $js);
    }
}

?>