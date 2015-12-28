<?php
/*
 page module
*/
if(!defined('TPL_PATH'))
define('TPL_PATH','tpl');
	
class Page{
	
	function __construct(){

	}
	/**
	 * Name of the module
	 * @return string
	 */
    public function getName__(){
     	return strtok(get_class($this),'_');
	}
    /**
     * Prepare a Smarty object
     *
     * @return Smarty
     */

    function getEngine($module=null){
    	require_once(PFC_ROOT.'/Smarty/Smarty.class.php');
    	$smarty = new Smarty();
    	$smarty->compile_check = true;
    	$smarty->template_dir = TPL_PATH;
    	$smarty->compile_dir = TPL_CACHE_PATH;
    	$smarty->caching = 0;
    	return $smarty;
    }
    /**
     * Render according to template
     *
     * @param string $template
     * @param array $parameters
     * @return string
     */
	public function render($template, $parameters=array()) {
		$engine = $this->getEngine();
		$template = $template;
		foreach($parameters as $name => $value){
			$engine->assign($name,$value);
		}
		return $engine->fetch($template);
	}
	/**
	* Display according to a template
	* @param string $template
	* @param array $parameters
	*/
	public function display($template, $parameters = array()) {
		$engine = $this->getEngine();
		foreach ($parameters as $varname => $value) {
			$engine->assign($varname, $value);
		}
		$engine->display($template);
	}
	static public function p($var,$label=''){
    	echo '<pre>'.$label;print_r($var);echo '</pre>';
    }
}
?>