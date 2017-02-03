<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<?php
$m = [];
$con = new PDO("mysql:host=153.122.32.54;dbname=timagma66036com34490_twitter", "timag_twitter", "xVjn9%68", array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', ));
$sql = "SELECT t.tweetId,t.created_at,t.text,t.userId,t.geo,u.name,u.screen_name,t.hashtags,t.media_id,t.whitelist,t.blacklist,t.id_str,u.id_str AS uid_str,t.urls FROM tweet AS t,user AS u WHERE t.userId = u.userId ORDER BY t.created_at DESC";
$data = $con -> query($sql);
while ($d = $data -> fetch(PDO::FETCH_ASSOC)) {
	$tmp = [];
	$tmp["user"]["screen_name"] = $d["screen_name"];
	$tmp["id_str"] = $d["id_str"];
	$tmp["created_at"] = $d["created_at"];
	$tmp["text"] = $d["text"];
	$tmp["user"]["id_str"] = $d["uid_str"];
	$tmp["geo"] = $d["geo"];
	$tmp["user"]["name"] = $d["name"];
	$tmp["entities"]["hashtags"] = unserialize($d["hashtags"]);
	$tmp["entities"]["urls"] = unserialize($d["urls"]);
	$tmp["whitelist"] = $d["whitelist"];
	$tmp["blacklist"] = $d["blacklist"];
	if (!is_null($d["media_id"])) {
		$mediaSql = "SELECT media_url FROM media WHERE mediaId = " . unserialize($d["media_id"])[0];
		$media = $con -> query($mediaSql);
		while ($me = $media -> fetch(PDO::FETCH_ASSOC)) {
			$tmp["entities"]["media"][0]["media_url"] = $me["media_url"];
		}
	} else {
		$tmp["entities"]["media"] = [];
	}
	$m[] = $tmp;
}
echo "<xmp>";
//var_dump($m);
echo "</xmp>";
if (isset($_GET["p"])) {
	if (0 < $_GET["p"] && $_GET["p"] <= ceil((count($m) - 1) / 10)) {
		$p = $_GET["p"];
	} else {
		echo "ページ指定不正";
		exit();
	}
} else {
	$p = 1;
}

$d = array_slice($m, ($p - 1) * 10, 10, true);
?>
<a href="get_tag.php" target="_blank">データ取得</a>・<a href="db_view.html" target="_blank">表示確認</a>
<br>
使用方法・Whitelistに設定された環境の場合、Whitelistを○にすると表示される。Blacklistに設定された環境の場合、Blacklistを○にすると表示されない。(現設定はBlacklist)
<table border="1">
	<tbody>
		<tr>
			<th>Tweet ID</th>
			<th>created_at</th>
			<th>text</th>
			<th>User ID</th>
			<th>Geo</th>
			<th>User Name</th>
			<th>User Screen Name</th>
			<th>Hash Tags</th>
			<th>images</th>
			<th>White List</th>
			<th>Black List</th>
		</tr>
		<?php foreach($d as $k => $v){
		?>





		<tr>
			<td><a href="https://twitter.com/<?php echo $v["user"]["screen_name"]; ?>/status/<?php echo $v["id_str"]; ?>" target="_blank"><?php echo $v["id_str"]; ?></a></td>
			<td><?php echo $v["created_at"]; ?></td>
			<td><?php echo $v["text"]; ?></td>
			<td><?php echo $v["user"]["id_str"]; ?></td>
			<td><?php echo $v["geo"]; ?></td>
			<td><?php echo $v["user"]["name"]; ?></td>
			<td><?php echo $v["user"]["screen_name"]; ?></td>
			<?php
			if ($v["entities"]["hashtags"]) {
				echo "<td>";
				$tmp = [];
				foreach ($v["entities"]["hashtags"] as $hashtag) {
					$tmp[] = $hashtag["text"];
				}
				echo implode(",", $tmp);
				echo "</td>";
			} else {
				echo "<td></td>";
			}
			if ($v["entities"]["media"]) {
				echo "<td>";
				foreach ($v["entities"]["media"] as $media) {
					echo '<a href="' . $media["media_url"] . '" target="_blank"><img src="' . $media["media_url"] . '"></a>';
				}
				echo "</td>";
			} elseif (isset($v["entities"]["urls"][0]["expanded_url"]) && strpos($v["entities"]["urls"][0]["expanded_url"], "instagram.com/p") !== false) {
				$tmp = explode("/", $v["entities"]["urls"][0]["expanded_url"]);
				echo '<td class="instagram"><a href="' . $v["entities"]["urls"][0]["expanded_url"] . '" target="_blank"><img src="insta_img.php?url=' . $v["entities"]["urls"][0]["expanded_url"] . 'media"></a></td>';
			} else {
				echo "<td></td>";
			}
			?>
			<td class="whitelist" id="<?php echo $v["id_str"]; ?>"><?php echo $v["whitelist"] ? "○" : "×"; ?></td>
			<td class="blacklist" id="<?php echo $v["id_str"]; ?>"><?php echo $v["blacklist"] ? "○" : "×"; ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<ul class="pagenation">
	<?php
	for ($i = 1; $i <= ceil((count($m) - 1) / 10); $i++) {
		if ($i == $p) {
			echo '<b><li>' . $i . '</li></b>';
		} else {
			echo '<a href="' . explode('?', $_SERVER["REQUEST_URI"])[0] . '?p=' . $i . '"><li>' . $i . '</li></a>';
		}
	}
	?>
</ul>
<style>
  img {
    width: 200px;
  }
  .whitelist, .blacklist {
    cursor: pointer;
  }
  tr:nth-of-type(2n) td {
    background-color: #ccc;
  }
  ul.pagenation li {
    float: left;
    margin: 10px;
    padding: 10px;
  }
</style>
<script>
	$(function() {
		$('.whitelist, .blacklist').on('click', function() {
			var cell = this;
			$.get('db_pref2.php', {
				'id' : $(this).attr('id'),
				'type' : $(this).attr('class'),
			}, function(data) {
				if (data == "1") {
					$(cell).text("○");
				} else if (data != "error") {
					$(cell).text("×");
				}
				console.log(data);
			})
		});
	});
</script>
