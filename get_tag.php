<?php
$con = new PDO("mysql:host=153.122.32.54;dbname=timagma66036com34490_twitter", "timag_twitter", "xVjn9%68", array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', ));
if (!$con) {
	exit("接続エラー");
}
date_default_timezone_set('asia/tokyo');
define('SINCE', 'since.txt');
if (is_file(SINCE)) {
	$since = file_get_contents(SINCE);
}

$bearer_token = 'AAAAAAAAAAAAAAAAAAAAAOkTzAAAAAAA5JgazbrKZ2B%2BV4HGJ%2BQKIUM2fjk%3DjKAJlFMVQXvmGRWLb3N8u3Mgv3Zuwdsnvx7YbAv1Sf4hPHlLAv';
$header = array('Authorization: Bearer ' . $bearer_token, );
$q = ['q' => 'QBオンライン OR QBONLINE exclude:retweets', 'result_type' => 'recent', 'count' => 100, ];
if (isset($since) && $since != "") {
	$q['since_id'] = $since;
}

$url = 'https://api.twitter.com/1.1/search/tweets.json?';
$curl = curl_init($url . http_build_query($q));
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
curl_setopt($curl, CURLOPT_TIMEOUT, 5);

$res = curl_exec($curl);
$res = json_decode($res, true);

if ($res != false && $res["statuses"] != null) {
	echo $res["statuses"][0]["id_str"];
	$since_res = file_put_contents(SINCE, $res["statuses"][0]["id_str"]);
	if ($since_res !== false) {
		foreach ($res["statuses"] as &$value) {
			if (!isset($value["whitelist"])) {
				$value["whitelist"] = false;
			}
			if (!isset($value["blacklist"])) {
				$value["blacklist"] = false;
			}
		}
		$tweetSqlColumn = ["created_at", "tweetId", "id_str", "text", "hashtags", "symbols", "user_mentions", "urls", "media_id", "truncated", "metadataIso_language_code", "metadataResult_type", "source", "in_reply_to_status_id", "in_reply_to_status_id_str", "in_reply_to_user_id", "in_reply_to_user_id_str", "in_reply_to_screen_name", "userId", "geoType", "geo", "coordinatesType", "coordinates", "placeId", "contributors", "is_quote_status", "retweet_count", "favorite_count", "favorited", "retweeted", "possibly_sensitive", "lang", "whitelist", "blacklist"];
		foreach ($res["statuses"] as $v) {
			$tweetSql = "INSERT INTO tweet(" . implode(",", $tweetSqlColumn) . ") VALUES (";
			$tweetSqlValue = [];
			$tweetSqlValue[] = $con -> quote(date("Y-m-d H:i:s", strtotime($v["created_at"])));
			$tweetSqlValue[] = $v["id"];
			$tweetSqlValue[] = $con -> quote($v["id_str"]);
			$tweetSqlValue[] = $con -> quote($v["text"]);
			$tweetSqlValue[] = $con -> quote(serialize($v["entities"]["hashtags"]));
			$tweetSqlValue[] = $con -> quote(serialize($v["entities"]["symbols"]));
			$tweetSqlValue[] = $con -> quote(serialize($v["entities"]["user_mentions"]));
			$tweetSqlValue[] = $con -> quote(serialize($v["entities"]["urls"]));
			if (isset($v["entities"]["media"])) {
				$tmp = [];
				foreach ($v["entities"]["media"] as $m) {
					$tmp[] = $m["id"];
				}
				$tweetSqlValue[] = $con -> quote(serialize($tmp));
			} else {
				$tweetSqlValue[] = "NULL";
			}
			$tweetSqlValue[] = $v["truncated"] ? 1 : 0;
			$tweetSqlValue[] = $con -> quote($v["metadata"]["iso_language_code"]);
			$tweetSqlValue[] = $con -> quote($v["metadata"]["result_type"]);
			$tweetSqlValue[] = $con -> quote($v["source"]);
			$tweetSqlValue[] = is_null($v["in_reply_to_status_id"]) ? "NULL" : $v["in_reply_to_status_id"];
			$tweetSqlValue[] = is_null($v["in_reply_to_status_id_str"]) ? "NULL" : $con -> quote($v["in_reply_to_status_id_str"]);
			$tweetSqlValue[] = is_null($v["in_reply_to_user_id"]) ? "NULL" : $v["in_reply_to_user_id"];
			$tweetSqlValue[] = is_null($v["in_reply_to_user_id_str"]) ? "NULL" : $con -> quote($v["in_reply_to_user_id_str"]);
			$tweetSqlValue[] = is_null($v["in_reply_to_screen_name"]) ? "NULL" : $con -> quote($v["in_reply_to_screen_name"]);
			$tweetSqlValue[] = $v["user"]["id"];
			//geoType
			$tweetSqlValue[] = "NULL";
			//geo
			$tweetSqlValue[] = "NULL";
			$tweetSqlValue[] = is_null($v["coordinates"]["type"]) ? "NULL" : $v["coordinates"];
			$tweetSqlValue[] = is_null($v["coordinates"]) ? "NULL" : ("'POINT(" . $v["coordinates"]["coordinates"][0] . " " . $v["coordinates"]["coordinates"][1] . ")'");
			$tweetSqlValue[] = is_null($v["place"]["id"]) ? "NULL" : $con -> quote($v["place"]["id"]);
			$tweetSqlValue[] = is_null($v["contributors"]) ? "NULL" : serialize($v["contributors"]);
			$tweetSqlValue[] = $v["is_quote_status"] ? 1 : 0;
			$tweetSqlValue[] = $v["retweet_count"];
			$tweetSqlValue[] = $v["favorite_count"];
			$tweetSqlValue[] = $v["favorited"] ? 1 : 0;
			$tweetSqlValue[] = $v["retweeted"] ? 1 : 0;
			$tweetSqlValue[] = isset($v["possibly_sensitive"]) ? ($v["possibly_sensitive"] ? 1 : 0) : "NULL";
			$tweetSqlValue[] = $con -> quote($v["lang"]);
			$tweetSqlValue[] = $v["whitelist"] ? 1 : 0;
			$tweetSqlValue[] = $v["blacklist"] ? 1 : 0;
			$tweetSql .= implode(",", $tweetSqlValue);
			$tweetSql .= ")";
			$con -> query($tweetSql);

			if (isset($v["entities"]["media"])) {
				$mediaSqlColumn = ["mediaId", "id_str", "indices", "media_url", "media_url_https", "url", "display_url", "expanded_url", "type", "smallW", "smallH", "smallResize", "mediumW", "mediumH", "mediumSize", "thumbW", "thumbH", "thumbSize", "largeW", "largeH", "largeSize"];
				foreach ($v["entities"]["media"] as $m) {
					$mediaSql = "INSERT INTO media(" . implode(",", $mediaSqlColumn) . ") VALUES (";
					$mediaSqlValue = [];
					$mediaSqlValue[] = $m["id"];
					$mediaSqlValue[] = $con -> quote($m["id_str"]);
					$mediaSqlValue[] = $con -> quote(serialize($m["indices"]));
					$mediaSqlValue[] = $con -> quote($m["media_url"]);
					$mediaSqlValue[] = $con -> quote($m["media_url_https"]);
					$mediaSqlValue[] = $con -> quote($m["url"]);
					$mediaSqlValue[] = $con -> quote($m["display_url"]);
					$mediaSqlValue[] = $con -> quote($m["expanded_url"]);
					$mediaSqlValue[] = $con -> quote($m["type"]);
					$mediaSqlValue[] = $con -> quote($m["sizes"]["small"]["w"]);
					$mediaSqlValue[] = $con -> quote($m["sizes"]["small"]["h"]);
					$mediaSqlValue[] = $con -> quote($m["sizes"]["small"]["resize"]);
					$mediaSqlValue[] = $con -> quote($m["sizes"]["medium"]["w"]);
					$mediaSqlValue[] = $con -> quote($m["sizes"]["medium"]["h"]);
					$mediaSqlValue[] = $con -> quote($m["sizes"]["medium"]["resize"]);
					$mediaSqlValue[] = $con -> quote($m["sizes"]["thumb"]["w"]);
					$mediaSqlValue[] = $con -> quote($m["sizes"]["thumb"]["h"]);
					$mediaSqlValue[] = $con -> quote($m["sizes"]["thumb"]["resize"]);
					$mediaSqlValue[] = $con -> quote($m["sizes"]["large"]["w"]);
					$mediaSqlValue[] = $con -> quote($m["sizes"]["large"]["h"]);
					$mediaSqlValue[] = $con -> quote($m["sizes"]["large"]["resize"]);
					$mediaSql .= implode(",", $mediaSqlValue);
					$mediaSql .= ")";
					$con -> query($mediaSql);
				}
			}

			if (isset($v["place"])) {
				$placeSqlColumn = ["placeId", "url", "place_type", "name", "full_name", "country_code", "country", "contained_within", "bounding_boxType", "bounding_box", "attributes"];
				$placeSql = "INSERT INTO place(" . implode(",", $placeSqlColumn) . ") VALUES (";
				$placeSqlValue = [];
				$placeSqlValue[] = $con -> quote($v["place"]["id"]);
				$placeSqlValue[] = $con -> quote($v["place"]["url"]);
				$placeSqlValue[] = $con -> quote($v["place"]["place_type"]);
				$placeSqlValue[] = $con -> quote($v["place"]["name"]);
				$placeSqlValue[] = $con -> quote($v["place"]["full_name"]);
				$placeSqlValue[] = $con -> quote($v["place"]["country_code"]);
				$placeSqlValue[] = $con -> quote($v["place"]["country"]);
				$placeSqlValue[] = $con -> quote(serialize($v["place"]["contained_within"]));
				$placeSqlValue[] = $con -> quote($v["place"]["bounding_box"]["type"]);
				$tmp = "'POLYGON((";
				$tmp .= $v["place"]["bounding_box"]["coordinates"][0][0][0] . " " . $v["place"]["bounding_box"]["coordinates"][0][0][1] . ",";
				$tmp .= $v["place"]["bounding_box"]["coordinates"][0][1][0] . " " . $v["place"]["bounding_box"]["coordinates"][0][1][1] . ",";
				$tmp .= $v["place"]["bounding_box"]["coordinates"][0][2][0] . " " . $v["place"]["bounding_box"]["coordinates"][0][2][1] . ",";
				$tmp .= $v["place"]["bounding_box"]["coordinates"][0][3][0] . " " . $v["place"]["bounding_box"]["coordinates"][0][3][1] . ",";
				$tmp .= "))'";
				$placeSqlValue[] = $tmp;
				$placeSqlValue[] = $con -> quote(serialize($v["place"]["attributes"]));
				$placeSql .= implode(",", $placeSqlValue);
				$placeSql .= ")";
				$con -> query($placeSql);
			}
			$userSqlColumn = ["userId", "id_str", "name", "screen_name", "location", "description", "url", "entitiesUrlUrls", "entitiesDescriptionUrls", "protected", "followers_count", "friends_count", "listed_count", "created_at", "favourites_count", "utc_offset", "time_zone", "geo_enabled", "verified", "statuses_count", "lang", "contributors_enabled", "is_translator", "is_translation_enabled", "profile_background_color", "profile_background_image_url", "profile_background_image_url_https", "profile_background_tile", "profile_image_url", "profile_image_url_https", "profile_banner_url", "profile_link_color", "profile_sidebar_border_color", "profile_sidebar_fill_color", "profile_text_color", "profile_use_background_image", "has_extended_profile", "default_profile", "default_profile_image", "following", "follow_request_sent", "notifications"];
			$userSql = "INSERT INTO user(" . implode(",", $userSqlColumn) . ") VALUES (";
			$userSqlValue = [];
			$userSqlValue[] = $v["user"]["id"];
			$userSqlValue[] = $con -> quote($v["user"]["id_str"]);
			$userSqlValue[] = $con -> quote($v["user"]["name"]);
			$userSqlValue[] = $con -> quote($v["user"]["screen_name"]);
			$userSqlValue[] = $con -> quote($v["user"]["location"]);
			$userSqlValue[] = $con -> quote($v["user"]["description"]);
			$userSqlValue[] = is_null($v["user"]["url"]) ? "NULL" : $con -> quote($v["user"]["url"]);
			$userSqlValue[] = isset($v["user"]["entities"]["url"]["urls"]) ? $con -> quote(serialize($v["user"]["entities"]["url"]["urls"])) : "NULL";
			$userSqlValue[] = isset($v["user"]["entities"]["description"]["urls"]) ? $con -> quote(serialize($v["user"]["entities"]["description"]["urls"])) : "NULL";
			$userSqlValue[] = $v["user"]["protected"] ? 1 : 0;
			$userSqlValue[] = $v["user"]["followers_count"];
			$userSqlValue[] = $v["user"]["friends_count"];
			$userSqlValue[] = $v["user"]["listed_count"];
			$userSqlValue[] = $con -> quote(date("Y-m-d H:i:s", strtotime($v["user"]["created_at"])));
			$userSqlValue[] = $v["user"]["favourites_count"];
			$userSqlValue[] = is_null($v["user"]["utc_offset"]) ? "NULL" : $v["user"]["utc_offset"];
			$userSqlValue[] = is_null($v["user"]["time_zone"]) ? "NULL" : $con -> quote($v["user"]["time_zone"]);
			$userSqlValue[] = $v["user"]["geo_enabled"] ? 1 : 0;
			$userSqlValue[] = $v["user"]["verified"] ? 1 : 0;
			$userSqlValue[] = $v["user"]["statuses_count"];
			$userSqlValue[] = $con -> quote($v["user"]["lang"]);
			$userSqlValue[] = $v["user"]["contributors_enabled"] ? 1 : 0;
			$userSqlValue[] = $v["user"]["is_translator"] ? 1 : 0;
			$userSqlValue[] = $v["user"]["is_translation_enabled"] ? 1 : 0;
			$userSqlValue[] = $con -> quote($v["user"]["profile_background_color"]);
			$userSqlValue[] = $con -> quote($v["user"]["profile_background_image_url"]);
			$userSqlValue[] = $con -> quote($v["user"]["profile_background_image_url_https"]);
			$userSqlValue[] = $v["user"]["profile_background_tile"] ? 1 : 0;
			$userSqlValue[] = $con -> quote($v["user"]["profile_image_url"]);
			$userSqlValue[] = $con -> quote($v["user"]["profile_image_url_https"]);
			$userSqlValue[] = isset($v["user"]["profile_banner_url"]) ? $con -> quote($v["user"]["profile_banner_url"]) : "NULL";
			$userSqlValue[] = $con -> quote($v["user"]["profile_link_color"]);
			$userSqlValue[] = $con -> quote($v["user"]["profile_sidebar_border_color"]);
			$userSqlValue[] = $con -> quote($v["user"]["profile_sidebar_fill_color"]);
			$userSqlValue[] = $con -> quote($v["user"]["profile_text_color"]);
			$userSqlValue[] = $v["user"]["profile_use_background_image"] ? 1 : 0;
			$userSqlValue[] = $v["user"]["has_extended_profile"] ? 1 : 0;
			$userSqlValue[] = $v["user"]["default_profile"] ? 1 : 0;
			$userSqlValue[] = $v["user"]["default_profile_image"] ? 1 : 0;
			$userSqlValue[] = is_null($v["user"]["following"]) ? "NULL" : ($v["user"]["following"] ? 1 : 0);
			$userSqlValue[] = is_null($v["user"]["follow_request_sent"]) ? "NULL" : ($v["user"]["follow_request_sent"] ? 1 : 0);
			$userSqlValue[] = is_null($v["user"]["notifications"]) ? "NULL" : ($v["user"]["notifications"] ? 1 : 0);
			$userSql .= implode(",", $userSqlValue);
			$userSql .= ")";
			$con -> query($userSql);
		}
		//echo "取得件数 : " . count($res["statuses"]);
	} else {
		//echo "since書き込みエラー";
	}
} else {
	if (is_array($res -> statuses)) {
		//echo "取得件数 : " . count($res["statuses"]);
	} else {
		//echo "取得エラー";
	}
}
?>