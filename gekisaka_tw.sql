-- phpMyAdmin SQL Dump
-- version 4.0.10.11
-- http://www.phpmyadmin.net
--
-- ホスト: localhost
-- 生成日時: 2016 年 6 月 03 日 00:44
-- サーバのバージョン: 5.5.42
-- PHP のバージョン: 5.4.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- データベース: `gekisaka_tw`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `media`
--

CREATE TABLE IF NOT EXISTS `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mediaId` bigint(20) NOT NULL,
  `id_str` text COLLATE utf8_bin NOT NULL,
  `indices` text COLLATE utf8_bin NOT NULL,
  `media_url` text COLLATE utf8_bin NOT NULL,
  `media_url_https` text COLLATE utf8_bin NOT NULL,
  `url` text COLLATE utf8_bin NOT NULL,
  `display_url` text COLLATE utf8_bin NOT NULL,
  `expanded_url` text COLLATE utf8_bin NOT NULL,
  `type` text COLLATE utf8_bin NOT NULL,
  `smallW` int(11) NOT NULL,
  `smallH` int(11) NOT NULL,
  `smallResize` text COLLATE utf8_bin NOT NULL,
  `mediumW` int(11) NOT NULL,
  `mediumH` int(11) NOT NULL,
  `mediumSize` text COLLATE utf8_bin NOT NULL,
  `thumbW` int(11) NOT NULL,
  `thumbH` int(11) NOT NULL,
  `thumbSize` text COLLATE utf8_bin NOT NULL,
  `largeW` int(11) NOT NULL,
  `largeH` int(11) NOT NULL,
  `largeSize` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mediaId` (`mediaId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2618 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `place`
--

CREATE TABLE IF NOT EXISTS `place` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `placeId` tinytext COLLATE utf8_bin NOT NULL,
  `url` text COLLATE utf8_bin NOT NULL,
  `place_type` tinytext COLLATE utf8_bin NOT NULL,
  `name` text COLLATE utf8_bin NOT NULL,
  `full_name` text COLLATE utf8_bin NOT NULL,
  `country_code` tinytext COLLATE utf8_bin NOT NULL,
  `country` text COLLATE utf8_bin NOT NULL,
  `contained_within` text COLLATE utf8_bin NOT NULL,
  `bounding_boxType` tinytext COLLATE utf8_bin NOT NULL,
  `bounding_box` polygon NOT NULL,
  `attributes` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `placeId` (`placeId`(32))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `tweet`
--

CREATE TABLE IF NOT EXISTS `tweet` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `tweetId` bigint(20) NOT NULL,
  `id_str` text COLLATE utf8_bin NOT NULL,
  `text` text COLLATE utf8_bin NOT NULL,
  `hashtags` text COLLATE utf8_bin NOT NULL,
  `symbols` text COLLATE utf8_bin NOT NULL,
  `user_mentions` text COLLATE utf8_bin NOT NULL,
  `urls` text COLLATE utf8_bin NOT NULL,
  `media_id` text COLLATE utf8_bin,
  `truncated` tinyint(1) NOT NULL,
  `metadataIso_language_code` tinytext COLLATE utf8_bin NOT NULL,
  `metadataResult_type` tinytext COLLATE utf8_bin NOT NULL,
  `source` text COLLATE utf8_bin NOT NULL,
  `in_reply_to_status_id` bigint(20) DEFAULT NULL,
  `in_reply_to_status_id_str` text COLLATE utf8_bin,
  `in_reply_to_user_id` bigint(20) DEFAULT NULL,
  `in_reply_to_user_id_str` text COLLATE utf8_bin,
  `in_reply_to_screen_name` text COLLATE utf8_bin,
  `userId` bigint(20) NOT NULL,
  `geoType` tinytext COLLATE utf8_bin,
  `geo` geometry DEFAULT NULL,
  `coordinatesType` tinytext COLLATE utf8_bin,
  `coordinates` geometry DEFAULT NULL,
  `placeId` text COLLATE utf8_bin,
  `contributors` text COLLATE utf8_bin,
  `is_quote_status` tinyint(1) DEFAULT NULL,
  `retweet_count` int(11) NOT NULL,
  `favorite_count` int(11) NOT NULL,
  `favorited` tinyint(1) DEFAULT NULL,
  `retweeted` tinyint(1) DEFAULT NULL,
  `possibly_sensitive` tinyint(1) DEFAULT NULL,
  `lang` tinytext COLLATE utf8_bin NOT NULL,
  `whitelist` tinyint(1) NOT NULL,
  `blacklist` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=22997 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) NOT NULL,
  `id_str` text COLLATE utf8_bin NOT NULL,
  `name` text COLLATE utf8_bin NOT NULL,
  `screen_name` text COLLATE utf8_bin NOT NULL,
  `location` text COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `url` text COLLATE utf8_bin,
  `entitiesUrlUrls` text COLLATE utf8_bin,
  `entitiesDescriptionUrls` text COLLATE utf8_bin,
  `protected` tinyint(1) NOT NULL,
  `followers_count` int(11) NOT NULL,
  `friends_count` int(11) NOT NULL,
  `listed_count` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `favourites_count` int(11) NOT NULL,
  `utc_offset` int(11) DEFAULT NULL,
  `time_zone` text COLLATE utf8_bin,
  `geo_enabled` tinyint(1) NOT NULL,
  `verified` tinyint(1) NOT NULL,
  `statuses_count` int(11) NOT NULL,
  `lang` tinytext COLLATE utf8_bin NOT NULL,
  `contributors_enabled` tinyint(1) NOT NULL,
  `is_translator` tinyint(1) NOT NULL,
  `is_translation_enabled` tinyint(1) NOT NULL,
  `profile_background_color` tinytext COLLATE utf8_bin NOT NULL,
  `profile_background_image_url` text COLLATE utf8_bin NOT NULL,
  `profile_background_image_url_https` text COLLATE utf8_bin NOT NULL,
  `profile_background_tile` tinyint(1) NOT NULL,
  `profile_image_url` text COLLATE utf8_bin NOT NULL,
  `profile_image_url_https` text COLLATE utf8_bin NOT NULL,
  `profile_banner_url` text COLLATE utf8_bin,
  `profile_link_color` tinytext COLLATE utf8_bin NOT NULL,
  `profile_sidebar_border_color` tinytext COLLATE utf8_bin NOT NULL,
  `profile_sidebar_fill_color` tinytext COLLATE utf8_bin NOT NULL,
  `profile_text_color` tinytext COLLATE utf8_bin NOT NULL,
  `profile_use_background_image` tinyint(1) NOT NULL,
  `has_extended_profile` tinyint(1) NOT NULL,
  `default_profile` tinyint(1) NOT NULL,
  `default_profile_image` tinyint(1) NOT NULL,
  `following` tinyint(1) DEFAULT NULL,
  `follow_request_sent` tinyint(1) DEFAULT NULL,
  `notifications` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=23015 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
