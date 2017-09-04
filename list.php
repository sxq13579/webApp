<?php
http://app.com/list.php?page-=1&pagesize=12
require_once('./response.php');
require_once('./file.php');

$file = new File();
$data = $file->cacheData('index_cron_cahce');
if($data) {
	return Response::show(200, '首页数据获取成功', $data);
}else{
	return Response::show(400, '首页数据获取失败', $data);
}
exit;
require_once('./db.php');
require_once('./file.php');
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$pageSize = isset($_GET['pagesize']) ? $_GET['pagesize'] : 6;
if(!is_numeric($page) || !is_numeric($pageSize)) {
	return Response::show(401, '数据不合法');
}

$offset = ($page - 1) * $pageSize;

$sql = "select * from users where order by id desc limit ". $offset ." , ".$pageSize;
$cache = new File();
$users = array();
if(!$users = $cache->cacheData('index_mk_cache' . $page .'-' . $pageSize)) {
	echo 1;exit;
	try {
		$connect = Db::getInstance()->connect();
	} catch(Exception $e) {
		// $e->getMessage();
		return Response::show(403, '数据库链接失败');
	}
	$result = mysql_query($sql, $connect); 
	
	while($user = mysql_fetch_assoc($result)) {
		$users[] = $user;
	}

	if($users) {
		$cache->cacheData('index_mk_cache' . $page .'-' . $pageSize, $users, 1200);
	}
}

if($users) {
	return Response::show(200, '首页数据获取成功', $users);
} else {
	return Response::show(400, '首页数据获取失败', $users);
}
