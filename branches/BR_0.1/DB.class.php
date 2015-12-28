<?php

class DB{
	public static $_instances = array();
	private $main_dbh = null;	//master db handle
	private $main_sth = null;	//master db statement handle
	private $main_dsn = array();//master db dsn
	
	private $query_dbh = null;		//slave db handle
	private $query_sth = null;		//slave db statement handle
	private $query_dsn = array();	//slave db dsn
	
	private $use_main = false;	//use master server for query
	private $fetch_style = PDO::FETCH_ASSOC;
	
	private $charset = 'UTF8';	//charset for db connect
	public $errno = 0;
	public $error = null;
	
	public static function getInstance($module='default',$use_main=false){
		$use_main = $use_main? true:false;
		$key = $module.$use_main;
		if(isset(self::$_instances[$key]))
			return self::$_instances[$key];
		$path = dirname(__FILE__);
		if( is_file($obj_file = $path.'/write/DB_'.$module.'.conf.obj') ){
			$array = unserialize(file_get_contents($obj_file));
		}else if( is_file($file=$path.'/DB/'.$module.'.conf.php') ){
			$array = include($file);
			file_put_contents($obj_file,serialize($array));
		}else if( is_file($file=$path.'/DB/default.conf.php') ){
			$array = include($file);
			file_put_contents($obj_file,serialize($array));			
		}else{
			die("failed init DB::getInstance().");
		}
		$index = rand(1,count($array)-1);
		$arr = $array[$index];
		$db = new self($arr[0],$arr[1],$arr[2]);
		
		$arr = $array[0];
		$db->set_main_dsn($arr[0],$arr[1],$arr[2]);
		$db->set_use_main($use_main);
		
		self::$_instances[$key] = $db;
		return $db;
	}
	//1
	function __construct($dsn,$user='root',$pass=''){
		$this->set_query_dsn($dsn,$user,$pass);
		$this->set_main_dsn($dsn,$user,$pass);
	}
	//2
	function set_query_dsn($dsn,$user,$pass){
		$this->query_dsn = array($dsn,$user,$pass);
	}
	function set_main_dsn($dsn,$user,$pass){
		$this->main_dsn = array($dsn,$user,$pass);
	}
	//3
	function set_charset($charset='utf8'){
		$this->charset = $charset;
	}
	//4
	function set_use_main($type=null){
		if(!is_null($type)){
			$this->use_main = $type? true:false;
		}
	}

	function connect($dsn,$user,$pass){
		try{
			$dbh = new PDO( $dsn, $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION) );
			$dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$dbh->setAttribute(PDO::ATTR_CURSOR, PDO::CURSOR_FWDONLY);
			$dbh->exec("set NAMES '".$this->charset."'");
		}catch(PDOException $e){
			$this->errno = $e->getCode();
			$this->error = $e->getmessage();
			die('DB Connection failed:'.$e->getMessage());
		}
		return $dbh;
	}
	function init(){
		if(null === $this->query_dbh){
			$arr = ($this->use_main)? $this->main_dsn:$this->query_dsn;
			$this->query_dbh = $this->connect($arr[0],$arr[1],$arr[2]);
		}
	}
	function main_init(){
		if(null === $this->main_dbh){
			$this->main_dbh = $this->connect($this->main_dsn[0],$this->main_dsn[1],$this->main_dsn[2]);
		}
	}
	function prepare($sql){
		if(!$this->query_dbh) $this->init();
		$this->query_sth = $this->query_dbh->prepare($sql);
		if(!$this->query_sth){
			throw new Exception("Error prepare SQL:$sql");
		}
		return $this->query_sth;
	}
	public function bind($param,$var){
		$this->query_sth->bindParam($param,$var);
	}
	function execute($sql=null,$binds=null,$foreach=false){
		if(is_array($sql)){
			$binds = $sql;
		}else if($sql){
			$this->prepare($sql);
		}
		if(is_array($binds)){
			if($foreach){
				foreach($binds as $k=>$v){
					$this->bind($k,$v);
				}
				return $this->query_sth->execute();
			}else{
				return $this->query_sth->execute($binds);
			}
		}else{
			return $this->query_sth->execute();
		}
	}
	//get next row from result
	function next($style=PDO::FETCH_ASSOC){
		if(!$this->query_sth)
			return false;
		return $this->query_sth->fetch($style);
	}
	function get_all($sql,$binds=null,$style=PDO::FETCH_ASSOC){
		if(!$this->execute($sql,$binds))
			return false;
		return $this->query_sth->fetchAll($style);
	}
	//获取一条记录
	function get_row($sql,$binds=null,$style=PDO::FETCH_ASSOC){
		if($this->execute($sql,$binds))
			return $this->query_sth->fetch($style);
		return false;
	}
	//获取多条记录
	function get_rows($sql,$binds=null,$field=null,$style=PDO::FETCH_ASSOC){
		if(!$this->execute($sql,$binds))
			return false;
		return $this->query_sth->fetchAll($style);
	}
	//get value of one field
	function get_value($sql,$binds=null){
		if($this->execute($sql,$binds))
			return $this->query_sth->fetchColumn();
		return false;
	}
	//get values of each field
	function get_values($sql,$binds=null){
		if(!$this->execute($sql,$binds))
			return false;
		return $this->query_sth->fetchAll(PDO::FETCH_COLUMN);
	}
	/**
	 * 获取以第一个字段值为key，第二个字段值为值的一维数组
	 * @return array(field1=>field2,...)
	 */
	function mkarray($sql,$binds=null){
		if(!$this->execute($sql,$binds))
			return false;
		return $this->query_sth->fetchAll(PDO::FETCH_UNIQUE |PDO::FETCH_COLUMN );
	}
	function mkarrayes($sql,$binds=null,$field=null,$style=PDO::FETCH_ASSOC){
		if(!$this->execute($sql,$binds))
			return false;
		$row = $this->query_sth->fetch($style);
		if($field && isset($row[$field])){
			$rows = array( $row[$field] => $row );
			$key = true;
		}else{
			$rows = array($row);
			$key = false;
		}
		while($row = $this->query_sth->fetch($style)){
			if($key) $rows[$row[$field]] = $row;
			else $rows[] = $row;
		}
		return $rows;
	}
	function get_object($sql,$binds=null){
		if(!$this->execute($sql,$binds))
			return false;
		return $this->query_sth->fetch(PDO::FETCH_OBJ);		
	}
	function get_objects($sql,$binds=null){
		if(!$this->execute($sql,$binds))
			return false;
		return $this->query_sth->fetchAll(PDO::FETCH_OBJ);		
	}
	function close(){
		$this->query_dbh = null;
		$this->query_sth = null;
	}
	function main_close(){
		$this->main_dbh = null;
		$this->main_sth = null;
	}
	/* main function*/
	function main_exec($sql){
		$this->main_init();
		try{
			return $this->main_dbh->exec($sql);	
		}catch(PDOException $e){
			PFCException::exception_handler($e);
			return -1;
		}
	}
	function main_prepare($sql){
		if(!$this->main_dbh) $this->main_init();
		$this->main_sth = $this->main_dbh->prepare($sql);
		if(!$this->main_sth){
			throw new Exception("Error main_prepare SQL:$sql");
		}
		return $this->main_sth;		
	}
	public function main_bind($param,$var){
		$this->main_sth->bindParam($param,$var);
	}
	function main_execute($sql,$binds,$foreach=false){
		if(is_array($sql)){
			$binds = $sql;
		}else if($sql){
			$this->main_prepare($sql);
		}
		if(is_array($binds)){
			if($foreach){
				foreach($binds as $k=>$v){
					$this->main_bind($k,$v);
				}
				return $this->main_sth->execute();
			}else{
				return $this->main_sth->execute($binds);
			}
		}else{
			return $this->main_sth->execute();
		}
	}
	public function insert_id(){
		if($this->main_dbh)
			return $this->main_dbh->lastInsertID();
		return false;
	}
	function insert($table,$set){
		if(is_array($set)){
			foreach($set as $k => $v){
				$sets[$k] = "`$k`=`$v`";
			}
			$set = implode(',',$set);
		}
		if( $this->main_exec('INSERT INTO '.$table.' SET '.$set) )
			return $this->insert_id();
		return false;
	}
	function update($table,$set,$where){
		if(!$where)
			return -1;
		if(is_array($set)){
			foreach($set as $key => $val){
				$set[$key] = "`$key`='$val'";
			}
			$set = implode(',',$set);
		}
		$sql = 'UPDATE '.$table.' SET '.$set.' WHERE '.$where;
		return $this->main_exec($sql);
	}
	function delete($table,$where){
		$sql = 'DELETE FROM '.$table.' WHERE '.$where;
		return $this->main_exec($sql);
	}
	static public function p($var,$label=null){
		echo '<pre>'.$label; print_r($var); echo '</pre>';
	}
}
?>