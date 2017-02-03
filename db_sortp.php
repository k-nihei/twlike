<?php
//使用可能パラメータ
$sort = array('retweet', 'created_at', 'fav');
$order = array('asc', 'desc');
function formatter($r) {
	global $con;
	$tmp = [];
	$sql = "SELECT name,screen_name,profile_image_url FROM user WHERE userId = " . $r["userId"];
	$user = $con -> query($sql);
	while ($u = $user -> fetch(PDO::FETCH_ASSOC)) {
		$tmp["user"]["screen_name"] = $u["screen_name"];
		$tmp["user"]["profile_image_url"] = $u["profile_image_url"];
		$tmp["user"]["name"] = $u["name"];
	}
	if (!is_null($r["media_id"])) {
		$idArr = unserialize($r["media_id"]);
		$cnt = 0;
		foreach ($idArr as $id) {
			$sql = "SELECT media_url,smallW FROM media WHERE mediaId = " . $id;
			$media = $con -> query($sql);
			while ($m = $media -> fetch(PDO::FETCH_ASSOC)) {
				$tmp["entities"]["media"][$cnt]["media_url"] = $m["media_url"];
				$tmp["entities"]["media"][$cnt]["sizes"]["small"]["w"] = $m["smallW"];
			}
			$cnt++;
		}
	} else {
		$tmp["entities"]["media"] = NULL;
	}
	$tmp["id_str"] = $r["id_str"];
	$tmp["text"] = $r["text"];
	$tmp["created_at"] = $r["created_at"];
	$tmp["favorite_count"] = $r["favorite_count"];
	return $tmp;
}

if (isset($_GET['callback'])) {
	$con = new PDO("mysql:host=153.122.32.54;dbname=timagma66036com34490_twitter", "timag_twitter", "xVjn9%68", array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', ));
	if (isset($_GET['sort']) && isset($_GET['order'])) {
		//パラメータチェック
		$flag = false;
		foreach ($sort as $v) {
			if ($v == $_GET['sort']) {
				$flag = true;
				break;
			}
		}
		if ($flag == false) {
			echo $_GET['callback'] . '({"error":"sort値不正"})';
			exit();
		}
		$flag = false;
		foreach ($order as $v) {
			if ($v == $_GET['order']) {
				$flag = true;
				break;
			}
		}
		if ($flag == false) {
			echo $_GET['callback'] . '({"error":"order値不正"})';
			exit();
		}
		//データ読み込み
		$data = [];
		$sql = "SELECT created_at,id_str,text,media_id,userId,favorite_count FROM tweet WHERE";
		//フィルター
		switch($_GET['filter']) {
			case 0 :
				break;
			case 1 :
				$sql .= " whiteList=1";
				break;
			case 2 :
				$sql .= " blackList=0";
				break;
			default :
				break;
		}
		//ソート順指定
		switch($_GET['sort']) {
			case 'retweet' :
				$sql .= " ORDER BY retweet_count";
				break;
			case 'created_at' :
				$sql .= " ORDER BY created_at";
				break;
			case 'fav' :
				$sql .= " ORDER BY favorite_count";
				break;
			default :
				break;
		}
		switch($_GET['order']) {
			case 'asce' :
				$sql .= " ASC";
				break;
			case 'desc' :
				$sql .= " DESC";
				break;
			default :
				break;
		}
		if (isset($_GET["limit"]) && is_numeric($_GET["limit"])) {
			$sql .= " LIMIT " . (isset($_GET["current"]) ? ($_GET["current"] . ",") : "0,") . $_GET["limit"];
		}
		$result = $con -> query($sql);
		while ($r = $result -> fetch(PDO::FETCH_ASSOC)) {
			$data[] = formatter($r);
		}
		echo $_GET['callback'] . "(" . json_encode($data, JSON_UNESCAPED_UNICODE) . ")";
	} else {
		//データ読み込み
		echo $_GET['callback'] . "(" . file_get_contents($_GET['path']) . ")";
	}
} else {
	echo '{"error":"callback不正"}';
}
?>
