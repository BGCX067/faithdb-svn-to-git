<?php

class Http {
	
	/*
	 * 获取HTTP请求参数
	 * @param string $key, 参数名
     * @param $default, 缺省参数值
     * @param $filter, 过滤类型
	 * @return 参数值
	 */
	static public function get($key,$default=null,$filter=null){
		$method = empty($_SERVER['REQUEST_METHOD'])? 'CONSOLE':$_SERVER['REQUEST_METHOD'];
		switch($method){
			case 'GET': $result = isset($_GET[$key])? $_GET[$key]:$default;break;
			case 'POST':$result = isset($_POST[$key])? $_POST[$key]:$default;break;
			case 'CONSOLE':{
				$argv = $GLOBALS['argv'];
				for($i=1;$i< $GLOBALS['argc'];$i++){
					if(strtoupper($argv[$i]) == strtoupper('-'.$key)){
						$result = isset($argv[$i+1])? $argv[$i+1]:$default;
						break;
					}
				}
				break;
			}
			default:$result = $default;break;
		}
		if(!$filter || !$result)
			return $result;
		switch($filter){
			case 'input':$result = trim(htmlspecialchars(strip_tags($result)));break;
			case 'text':$result = trim(strip_tags($result));break;
			case 'number':$result = preg_replace("/[^0-9]/",'',$result);break;
			default:break;
		}
		return $result;
	}
	//获取cookie变量
	static public function getCookie($key,$default=null){
		return isset($_COOKIE[$key])? $_COOKIE[$key]:$default;
	}
	//获取session变量
	static public function getSession($key,$default=null){
		return isset($_SESSION[$key])? $_SESSION[$key]:$default;
	}
	//获取上传文件
	static public function getFile($key){
		return isset($_FILES[$key])? $_FILES[$key]:false;
	}
	
	/**
	 * 使用给出的关联（或下标）数组生成一个经过 URL-encode 的请求字符串
	 * @param array|object $formdata 	-- 数组或包含属性的对象
	 * @param string $numeric_prefix	-- 基础数组使用了数字下标时的数字下标元素的前缀
	 * @return URL-encode QUERY_STRING
	 */
	public function buildQuery($formdata,$numeric_prefix=''){
		return http_build_query($formdata,$numeric_prefix);
	}
	/**
	 * get REQUEST_METHOD
	 * return GET|POST|CONSOLE
	 */
	public function getMethod(){
		return empty($_SERVER['REQUEST_METHOD'])? 'CONSOLE':$_SERVER['REQUEST_METHOD'];
	}
	public function getOS(){
		return isset($_ENV['OS'])? $_ENV['OS']:'unknown';
	}
	public function getLanguage(){
		return iseet($_SERVER['HTTP_ACCEPT_LANGUAGE'])? $_SERVER['HTTP_ACCEPT_LANGUAGE']:'';
	}
	static public function getIP(){
		$ip = getenv('HTTP_CLIENT_IP').','.getenv('HTTP_X_FORWARDED_FOR').','.getenv('REMOTE_ADDR');
		if ( preg_match ("/\d+\.\d+\.\d+\.\d+/", $ip, $matchs) )
			return $matchs[0];
		return 'unknown';
	}
	static public function getRequestUrl(){
		if(isset($_SERVER['HTTP_HOST']) && isset($_SERVER['SCRIPT_NAME'])){
			return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		}
		return false;
	}
	static public function getReferUrl(){
		return isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']:'';
	}
	static public function file2Url($file){
		$root = isset($_SERVER['DOCUMENT_ROOT'])? $_SERVER['DOCUMENT_ROOT']:'';
		$host = isset($_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST']:'';
		if($host && $root && (0===strpos($file,$root)) ){
			return 'http://'.$host.str_replace($root,'',$file);
		}
		return false;
	}
}
?>