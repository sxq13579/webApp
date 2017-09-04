<?php
require_once('./response.php');
require_once('./file.php');

class Db {
	static private $_instance;
	static private $_connectSource;
	private $_dbConfig = array(
		'host' => 'localhost:3306',
		'user' => 'root',
		'password' => '',
		'database' => 'userinfo',
	);

	private function __construct() {
	}

	static public function getInstance() {
		if(!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function connect() {
		if(!self::$_connectSource) {
			self::$_connectSource = @mysql_connect($this->_dbConfig['host'], $this->_dbConfig['user'], $this->_dbConfig['password']);	

			if(!self::$_connectSource) {
				throw new Exception('mysql connect error ' . mysql_error());
				//die('mysql connect error' . mysql_error());
			}
			
			mysql_select_db($this->_dbConfig['database'], self::$_connectSource);
			mysql_query("set names UTF8", self::$_connectSource);
		}
		return self::$_connectSource;
	}
}
try{
	$connect = Db::getInstance()->connect();
} catch(Exception $e) {
	return Response::show(403, '数据库连接失败');
}

$sql = "select * from users";

// $insert = "INSERT INTO users".
//        "(name, password, email) ".
//        "VALUES ".
//        "('better','19950920', '123456@163.com')";
$result = mysql_query($sql, $connect);
// $result = mysql_query($insert, $connect);
 
$users = array();
while($user = mysql_fetch_assoc($result)) {
	$users[] = $user;
}
// echo mysql_num_rows($result);
// var_dump($users);

if($users) {
	return Response::show(200, 'success', $users);
}