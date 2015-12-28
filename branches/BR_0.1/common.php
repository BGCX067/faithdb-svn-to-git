<?php
//is Running in console environment
define('CONSOLE',!isset($_SERVER['REQUEST_METHOD']));

//PFC root path
define('PFC_ROOT',dirname(__FILE__));

//server root path
define('SERVER_ROOT',dirname(PFC_ROOT));

//path of web module 
define('WEB_MODULE_ROOT',SERVER_ROOT.'/WebModules');

//set include path
set_include_path('.'.PATH_SEPARATOR.PFC_ROOT);

function __autoload($class){
	__loadclass($class);
}
function __loadclass($class){
	if(strncmp($class,'Q',1)){
		require_once(PFC_ROOT.'/'.strtr($class,'_','/').'.class.php');
	}else{
		require_once(WEB_MODULE_ROOT.'/'.strtr($class,'_','/').'.class.php');
	}
}
//local config:need rename(local.dist.php,local.php)
include PFC_ROOT.'/local.php';
require_once(PFC_ROOT.'/PFCException.class.php');

?>