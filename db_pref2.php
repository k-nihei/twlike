<?php
if (isset($_GET["id"]) && isset($_GET["type"])) {
	$con = new PDO("mysql:host=153.122.32.54;dbname=timagma66036com34490_twitter", "timag_twitter", "xVjn9%68", array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', ));
	$sql = "SELECT " . $_GET["type"] . " FROM tweet WHERE tweetId = " . $_GET["id"];
	$result = $con -> query($sql);
	while ($r = $result -> fetch(PDO::FETCH_ASSOC)) {
		if ($r[$_GET["type"]] == "1") {
			$updateSql = "UPDATE tweet SET " . $_GET["type"] . " = 0 WHERE tweetId = " . $_GET["id"];
			echo "0";
		} else {
			$updateSql = "UPDATE tweet SET " . $_GET["type"] . " = 1 WHERE tweetId = " . $_GET["id"];
			echo "1";
		}
		$con -> query($updateSql);
	}

} else {
	echo "error";
}
?>