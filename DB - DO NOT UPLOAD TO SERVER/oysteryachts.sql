-- phpMyAdmin SQL Dump
-- version 4.2.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Feb 25, 2016 at 12:10 PM
-- Server version: 5.5.38
-- PHP Version: 5.6.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `oysteryachts`
--

-- --------------------------------------------------------

--
-- Table structure for table `exp_actions`
--

CREATE TABLE `exp_actions` (
`action_id` int(4) unsigned NOT NULL,
  `class` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `method` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `csrf_exempt` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_actions`
--

INSERT INTO `exp_actions` (`action_id`, `class`, `method`, `csrf_exempt`) VALUES
(1, 'Channel', 'submit_entry', 0),
(2, 'Channel', 'filemanager_endpoint', 0),
(3, 'Channel', 'smiley_pop', 0),
(4, 'Channel', 'combo_loader', 0),
(5, 'Comment', 'insert_new_comment', 0),
(6, 'Comment_mcp', 'delete_comment_notification', 0),
(7, 'Comment', 'comment_subscribe', 0),
(8, 'Comment', 'edit_comment', 0),
(9, 'Member', 'registration_form', 0),
(10, 'Member', 'register_member', 0),
(11, 'Member', 'activate_member', 0),
(12, 'Member', 'member_login', 0),
(13, 'Member', 'member_logout', 0),
(14, 'Member', 'send_reset_token', 0),
(15, 'Member', 'process_reset_password', 0),
(16, 'Member', 'send_member_email', 0),
(17, 'Member', 'update_un_pw', 0),
(18, 'Member', 'member_search', 0),
(19, 'Member', 'member_delete', 0),
(20, 'Rte', 'get_js', 0),
(21, 'Search', 'do_search', 1),
(22, 'Channel_images', 'channel_images_router', 1),
(23, 'Channel_images', 'locked_image_url', 0),
(24, 'Channel_images', 'simple_image_url', 0),
(25, 'Editor', 'actionGeneralRouter', 0),
(26, 'Editor', 'actionFileUpload', 1);

-- --------------------------------------------------------

--
-- Table structure for table `exp_captcha`
--

CREATE TABLE `exp_captcha` (
`captcha_id` bigint(13) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `word` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_categories`
--

CREATE TABLE `exp_categories` (
`cat_id` int(10) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_id` int(6) unsigned NOT NULL,
  `parent_id` int(4) unsigned NOT NULL,
  `cat_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `cat_url_title` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  `cat_description` text COLLATE utf8_unicode_ci,
  `cat_image` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat_order` int(4) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_category_fields`
--

CREATE TABLE `exp_category_fields` (
`field_id` int(6) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_id` int(4) unsigned NOT NULL,
  `field_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `field_label` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `field_type` varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  `field_list_items` text COLLATE utf8_unicode_ci NOT NULL,
  `field_maxl` smallint(3) NOT NULL DEFAULT '128',
  `field_ta_rows` tinyint(2) NOT NULL DEFAULT '8',
  `field_default_fmt` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  `field_show_fmt` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `field_text_direction` char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ltr',
  `field_required` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `field_order` int(3) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_category_field_data`
--

CREATE TABLE `exp_category_field_data` (
  `cat_id` int(4) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_id` int(4) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_category_groups`
--

CREATE TABLE `exp_category_groups` (
`group_id` int(6) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a',
  `exclude_group` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `field_html_formatting` char(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'all',
  `can_edit_categories` text COLLATE utf8_unicode_ci,
  `can_delete_categories` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_category_posts`
--

CREATE TABLE `exp_category_posts` (
  `entry_id` int(10) unsigned NOT NULL,
  `cat_id` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_channels`
--

CREATE TABLE `exp_channels` (
`channel_id` int(6) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `channel_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `channel_title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `channel_url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `channel_description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `channel_lang` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `total_entries` mediumint(8) NOT NULL DEFAULT '0',
  `total_comments` mediumint(8) NOT NULL DEFAULT '0',
  `last_entry_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_comment_date` int(10) unsigned NOT NULL DEFAULT '0',
  `cat_group` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status_group` int(4) unsigned DEFAULT NULL,
  `deft_status` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'open',
  `field_group` int(4) unsigned DEFAULT NULL,
  `search_excerpt` int(4) unsigned DEFAULT NULL,
  `deft_category` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deft_comments` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `channel_require_membership` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `channel_max_chars` int(5) unsigned DEFAULT NULL,
  `channel_html_formatting` char(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'all',
  `extra_publish_controls` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `channel_allow_img_urls` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `channel_auto_link_urls` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `channel_notify` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `channel_notify_emails` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment_url` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment_system_enabled` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `comment_require_membership` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `comment_moderate` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `comment_max_chars` int(5) unsigned DEFAULT '5000',
  `comment_timelock` int(5) unsigned NOT NULL DEFAULT '0',
  `comment_require_email` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `comment_text_formatting` char(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'xhtml',
  `comment_html_formatting` char(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'safe',
  `comment_allow_img_urls` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `comment_auto_link_urls` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `comment_notify` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `comment_notify_authors` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `comment_notify_emails` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment_expiration` int(4) unsigned NOT NULL DEFAULT '0',
  `search_results_url` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rss_url` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `enable_versioning` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `max_revisions` smallint(4) unsigned NOT NULL DEFAULT '10',
  `default_entry_title` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_field_label` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Title',
  `url_title_prefix` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `live_look_template` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_channels`
--

INSERT INTO `exp_channels` (`channel_id`, `site_id`, `channel_name`, `channel_title`, `channel_url`, `channel_description`, `channel_lang`, `total_entries`, `total_comments`, `last_entry_date`, `last_comment_date`, `cat_group`, `status_group`, `deft_status`, `field_group`, `search_excerpt`, `deft_category`, `deft_comments`, `channel_require_membership`, `channel_max_chars`, `channel_html_formatting`, `extra_publish_controls`, `channel_allow_img_urls`, `channel_auto_link_urls`, `channel_notify`, `channel_notify_emails`, `comment_url`, `comment_system_enabled`, `comment_require_membership`, `comment_moderate`, `comment_max_chars`, `comment_timelock`, `comment_require_email`, `comment_text_formatting`, `comment_html_formatting`, `comment_allow_img_urls`, `comment_auto_link_urls`, `comment_notify`, `comment_notify_authors`, `comment_notify_emails`, `comment_expiration`, `search_results_url`, `rss_url`, `enable_versioning`, `max_revisions`, `default_entry_title`, `title_field_label`, `url_title_prefix`, `live_look_template`) VALUES
(1, 1, 'brokerage_yacht', 'Brokerage Yacht', 'http://oyster.local/index.php', NULL, 'en', 0, 0, 0, 0, '', 1, 'open', 1, NULL, NULL, 'y', 'y', NULL, 'all', 'n', 'y', 'n', 'n', NULL, NULL, 'y', 'n', 'n', 5000, 0, 'y', 'xhtml', 'safe', 'n', 'y', 'n', 'n', NULL, 0, NULL, NULL, 'n', 10, '', 'Title', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `exp_channel_data`
--

CREATE TABLE `exp_channel_data` (
  `entry_id` int(10) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `channel_id` int(4) unsigned NOT NULL,
  `field_id_1` text COLLATE utf8_unicode_ci,
  `field_ft_1` tinytext COLLATE utf8_unicode_ci,
  `field_id_2` text COLLATE utf8_unicode_ci,
  `field_ft_2` tinytext COLLATE utf8_unicode_ci,
  `field_id_3` text COLLATE utf8_unicode_ci,
  `field_ft_3` tinytext COLLATE utf8_unicode_ci,
  `field_id_4` text COLLATE utf8_unicode_ci,
  `field_ft_4` tinytext COLLATE utf8_unicode_ci,
  `field_id_5` text COLLATE utf8_unicode_ci,
  `field_ft_5` tinytext COLLATE utf8_unicode_ci,
  `field_id_6` text COLLATE utf8_unicode_ci,
  `field_ft_6` tinytext COLLATE utf8_unicode_ci,
  `field_id_7` text COLLATE utf8_unicode_ci,
  `field_ft_7` tinytext COLLATE utf8_unicode_ci,
  `field_id_8` text COLLATE utf8_unicode_ci,
  `field_ft_8` tinytext COLLATE utf8_unicode_ci,
  `field_id_9` text COLLATE utf8_unicode_ci,
  `field_ft_9` tinytext COLLATE utf8_unicode_ci,
  `field_id_10` text COLLATE utf8_unicode_ci,
  `field_ft_10` tinytext COLLATE utf8_unicode_ci,
  `field_id_11` text COLLATE utf8_unicode_ci,
  `field_ft_11` tinytext COLLATE utf8_unicode_ci,
  `field_id_12` text COLLATE utf8_unicode_ci,
  `field_ft_12` tinytext COLLATE utf8_unicode_ci,
  `field_id_13` text COLLATE utf8_unicode_ci,
  `field_ft_13` tinytext COLLATE utf8_unicode_ci,
  `field_id_14` text COLLATE utf8_unicode_ci,
  `field_ft_14` tinytext COLLATE utf8_unicode_ci,
  `field_id_15` text COLLATE utf8_unicode_ci,
  `field_ft_15` tinytext COLLATE utf8_unicode_ci,
  `field_id_16` text COLLATE utf8_unicode_ci,
  `field_ft_16` tinytext COLLATE utf8_unicode_ci,
  `field_id_17` text COLLATE utf8_unicode_ci,
  `field_ft_17` tinytext COLLATE utf8_unicode_ci,
  `field_id_18` text COLLATE utf8_unicode_ci,
  `field_ft_18` tinytext COLLATE utf8_unicode_ci,
  `field_id_19` text COLLATE utf8_unicode_ci,
  `field_ft_19` tinytext COLLATE utf8_unicode_ci,
  `field_id_20` text COLLATE utf8_unicode_ci,
  `field_ft_20` tinytext COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_channel_data`
--

INSERT INTO `exp_channel_data` (`entry_id`, `site_id`, `channel_id`, `field_id_1`, `field_ft_1`, `field_id_2`, `field_ft_2`, `field_id_3`, `field_ft_3`, `field_id_4`, `field_ft_4`, `field_id_5`, `field_ft_5`, `field_id_6`, `field_ft_6`, `field_id_7`, `field_ft_7`, `field_id_8`, `field_ft_8`, `field_id_9`, `field_ft_9`, `field_id_10`, `field_ft_10`, `field_id_11`, `field_ft_11`, `field_id_12`, `field_ft_12`, `field_id_13`, `field_ft_13`, `field_id_14`, `field_ft_14`, `field_id_15`, `field_ft_15`, `field_id_16`, `field_ft_16`, `field_id_17`, `field_ft_17`, `field_id_18`, `field_ft_18`, `field_id_19`, `field_ft_19`, `field_id_20`, `field_ft_20`) VALUES
(1, 1, 1, 'Oyster 885', 'br', '<p>An exclusive listing with Oyster Brokerage and a rare opportunity for the discerning Superyacht enthusiast to own the second in class Oyster 100. Penelope has recently completed her flag, class and 3 year docking surveys, which she passed with flying colours. This, along with many other maintenance projects mean that she is truly ''ready to sail away''. She is currently lying in Palma and intends to stay all winter. Penelope has enjoyed many successes since her launch in 2013 including: Finalist in the Boat International World Superyacht Awards 2013, first place in the 2014 Oyster regatta in Antigua and flourishing charter seasons in the Caribbean and Mediterranean – all forming an attractive offering in the pre-owned Superyacht arena.</p>\n\n<p>Penelope is designed with the specification, features and classification of very much larger yachts. Her accommodation layout offers three sumptuous staterooms aft and two crew cabins forward. With panoramic views from the raised saloon which leads forward and down to a further lounge and separate dining area. Forward of the main living arrangement is the crew mess, galley and two crew cabins.</p>\n\n\n\n\n\n\n\n<p>Penelope couldn’t be in a finer condition since her launch in 2012. The Southampton yard visit in early 2014 ensured the warranty list was ticked off, systems thoroughly serviced and allowed some owner modifications. Along with the highest possible Lloyds certification the Oyster 100 is designed and built to ''go places'' and when compared to other like sized sailing yachts, the amount of light and space available will certainly ensure that she is one not to be missed.</p>', 'xhtml', '<p>For further details please contact Jamie Collins - <a href="mailto:jamie.collins@oysteryachts.com">jamie.collins@oysteryachts.com</a> <br>For general information about the history of this Oyster model, please go to the Oyster Fleet Overview</p>', 'xhtml', 'Oyster Palma', 'br', '2012', 'br', 'Cutter', 'br', ' Crown Cut Cherry', 'br', 'Eight berths in four cabins', 'br', 'Available', 'br', '6000000', 'br', 'Large', 'br', 'Reduced Price', 'br', ' ', 'xhtml', '', 'xhtml', 'ChannelImages', 'xhtml', '', 'xhtml', 'ChannelImages', 'xhtml', '£', 'br', 'inc VAT', 'br', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exp_channel_entries_autosave`
--

CREATE TABLE `exp_channel_entries_autosave` (
`entry_id` int(10) unsigned NOT NULL,
  `original_entry_id` int(10) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `channel_id` int(4) unsigned NOT NULL,
  `author_id` int(10) unsigned NOT NULL DEFAULT '0',
  `forum_topic_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `url_title` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `versioning_enabled` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `view_count_one` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count_two` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count_three` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count_four` int(10) unsigned NOT NULL DEFAULT '0',
  `allow_comments` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `sticky` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `entry_date` int(10) NOT NULL,
  `year` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `month` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `day` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `expiration_date` int(10) NOT NULL DEFAULT '0',
  `comment_expiration_date` int(10) NOT NULL DEFAULT '0',
  `edit_date` bigint(14) DEFAULT NULL,
  `recent_comment_date` int(10) DEFAULT NULL,
  `comment_total` int(4) unsigned NOT NULL DEFAULT '0',
  `entry_data` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_channel_entries_autosave`
--

INSERT INTO `exp_channel_entries_autosave` (`entry_id`, `original_entry_id`, `site_id`, `channel_id`, `author_id`, `forum_topic_id`, `ip_address`, `title`, `url_title`, `status`, `versioning_enabled`, `view_count_one`, `view_count_two`, `view_count_three`, `view_count_four`, `allow_comments`, `sticky`, `entry_date`, `year`, `month`, `day`, `expiration_date`, `comment_expiration_date`, `edit_date`, `recent_comment_date`, `comment_total`, `entry_data`) VALUES
(1, 0, 1, 1, 1, NULL, '0', 'Penelope', 'penelope', 'open', 'n', 0, 0, 0, 0, 'y', 'n', 0, '0', '0', '0', 0, 0, 1456328979, NULL, 0, '{"title":"Penelope","url_title":"penelope","field_id_1":"Oyster 885","field_id_2":"<p>An exclusive listing with Oyster Brokerage and a rare opportunity for the discerning Superyacht enthusiast to own the second in class Oyster 100. Penelope has recently completed her flag, class and 3 year docking surveys, which she passed with flying colours. This, along with many other maintenance projects mean that she is truly ''ready to sail away''. She is currently lying in Palma and intends to stay all winter. Penelope has enjoyed many successes since her launch in 2013 including: Finalist in the Boat International World Superyacht Awards 2013, first place in the 2014 Oyster regatta in Antigua and flourishing charter seasons in the Caribbean and Mediterranean \\u2013 all forming an attractive offering in the pre-owned Superyacht arena.<\\/p><p>Penelope is designed with the specification, features and classification of very much larger yachts. Her accommodation layout offers three sumptuous staterooms aft and two crew cabins forward. With panoramic views from the raised saloon which leads forward and down to a further lounge and separate dining area. Forward of the main living arrangement is the crew mess, galley and two crew cabins.<\\/p><p>Penelope couldn\\u2019t be in a finer condition since her launch in 2012. The Southampton yard visit in early 2014 ensured the warranty list was ticked off, systems thoroughly serviced and allowed some owner modifications. Along with the highest possible Lloyds certification the Oyster 100 is designed and built to ''go places'' and when compared to other like sized sailing yachts, the amount of light and space available will certainly ensure that she is one not to be missed.<\\/p>","field_id_3":"<p>For further details please contact Jamie Collins - <a href=\\"mailto:jamie.collins@oysteryachts.com\\">jamie.collins@oysteryachts.com<\\/a> <br>For general information about the history of this Oyster model, please go to the Oyster Fleet Overview<\\/p>","field_id_4":"Oyster Palma","field_id_5":"2012","field_id_6":"Cutter","field_id_7":" Crown Cut Cherry","field_id_8":"Eight berths in four cabins","field_id_9":"Available","field_id_10":"\\u00a36,000,000 inc VAT","field_id_11":"Small","field_id_12":"Reduced Price","locationtype":"local","field_id_14":{"key":"1456328633"},"field_id_15":{"key":"1456328633"},"field_id_16":{"key":"1456328633"},"entry_date":"2\\/24\\/2016 4:43 PM","status":"open","author_id":"1"}');

-- --------------------------------------------------------

--
-- Table structure for table `exp_channel_fields`
--

CREATE TABLE `exp_channel_fields` (
`field_id` int(6) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_id` int(4) unsigned NOT NULL,
  `field_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `field_label` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `field_instructions` text COLLATE utf8_unicode_ci,
  `field_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  `field_list_items` text COLLATE utf8_unicode_ci NOT NULL,
  `field_pre_populate` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `field_pre_channel_id` int(6) unsigned DEFAULT NULL,
  `field_pre_field_id` int(6) unsigned DEFAULT NULL,
  `field_ta_rows` tinyint(2) DEFAULT '8',
  `field_maxl` smallint(3) DEFAULT NULL,
  `field_required` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `field_text_direction` char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ltr',
  `field_search` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `field_is_hidden` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `field_fmt` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'xhtml',
  `field_show_fmt` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `field_order` int(3) unsigned NOT NULL,
  `field_content_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'any',
  `field_settings` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_channel_fields`
--

INSERT INTO `exp_channel_fields` (`field_id`, `site_id`, `group_id`, `field_name`, `field_label`, `field_instructions`, `field_type`, `field_list_items`, `field_pre_populate`, `field_pre_channel_id`, `field_pre_field_id`, `field_ta_rows`, `field_maxl`, `field_required`, `field_text_direction`, `field_search`, `field_is_hidden`, `field_fmt`, `field_show_fmt`, `field_order`, `field_content_type`, `field_settings`) VALUES
(1, 1, 1, 'brokerage_model', 'Model', '', 'text', '', 'n', NULL, NULL, 8, 256, 'n', 'ltr', 'n', 'n', 'br', 'n', 1, 'all', 'YTozOntzOjEwOiJmaWVsZF9tYXhsIjtzOjM6IjI1NiI7czoxODoiZmllbGRfY29udGVudF90eXBlIjtzOjM6ImFsbCI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtiOjA7fQ=='),
(2, 1, 1, 'brokerage_about', 'About', '', 'editor', '', 'n', NULL, NULL, 8, NULL, 'n', 'ltr', 'n', 'n', 'xhtml', 'y', 2, 'any', 'YToyOntzOjY6ImVkaXRvciI7YToxOntzOjY6ImNvbmZpZyI7czoxOiIyIjt9czoxMDoiZmllbGRfd2lkZSI7YjowO30='),
(3, 1, 1, 'brokerage_further_information', 'Further information', '', 'editor', '', 'n', NULL, NULL, 8, NULL, 'n', 'ltr', 'n', 'n', 'xhtml', 'y', 3, 'any', 'YToyOntzOjY6ImVkaXRvciI7YToxOntzOjY6ImNvbmZpZyI7czoxOiIyIjt9czoxMDoiZmllbGRfd2lkZSI7YjowO30='),
(4, 1, 1, 'brokerage_location', 'Location', '', 'text', '', 'n', NULL, NULL, 8, 256, 'n', 'ltr', 'n', 'n', 'br', 'n', 4, 'all', 'YTozOntzOjEwOiJmaWVsZF9tYXhsIjtzOjM6IjI1NiI7czoxODoiZmllbGRfY29udGVudF90eXBlIjtzOjM6ImFsbCI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtiOjA7fQ=='),
(5, 1, 1, 'brokerage_year_built', 'Year built', '', 'text', '', 'n', NULL, NULL, 8, 256, 'n', 'ltr', 'n', 'n', 'br', 'n', 5, 'all', 'YTozOntzOjEwOiJmaWVsZF9tYXhsIjtzOjM6IjI1NiI7czoxODoiZmllbGRfY29udGVudF90eXBlIjtzOjM6ImFsbCI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtiOjA7fQ=='),
(6, 1, 1, 'brokerage_rig', 'Rig', '', 'text', '', 'n', NULL, NULL, 8, 256, 'n', 'ltr', 'n', 'n', 'br', 'n', 6, 'all', 'YTozOntzOjEwOiJmaWVsZF9tYXhsIjtzOjM6IjI1NiI7czoxODoiZmllbGRfY29udGVudF90eXBlIjtzOjM6ImFsbCI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtiOjA7fQ=='),
(7, 1, 1, 'brokerage_joinery', 'Joinery', '', 'text', '', 'n', NULL, NULL, 8, 256, 'n', 'ltr', 'n', 'n', 'br', 'n', 7, 'all', 'YTozOntzOjEwOiJmaWVsZF9tYXhsIjtzOjM6IjI1NiI7czoxODoiZmllbGRfY29udGVudF90eXBlIjtzOjM6ImFsbCI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtiOjA7fQ=='),
(8, 1, 1, 'brokerage_cabins', 'Cabins', '', 'text', '', 'n', NULL, NULL, 8, 256, 'n', 'ltr', 'n', 'n', 'br', 'n', 8, 'all', 'YTozOntzOjEwOiJmaWVsZF9tYXhsIjtzOjM6IjI1NiI7czoxODoiZmllbGRfY29udGVudF90eXBlIjtzOjM6ImFsbCI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtiOjA7fQ=='),
(9, 1, 1, 'brokerage_status', 'Status', '', 'select', 'Available\nUnder offer\nNow sold', 'n', NULL, NULL, 8, NULL, 'n', 'ltr', 'n', 'n', 'br', 'y', 9, 'any', 'YTowOnt9'),
(10, 1, 1, 'brokerage_price', 'Price', '', 'text', '', 'n', NULL, NULL, 8, 256, 'n', 'ltr', 'n', 'n', 'br', 'n', 10, 'numeric', 'YTozOntzOjEwOiJmaWVsZF9tYXhsIjtzOjM6IjI1NiI7czoxODoiZmllbGRfY29udGVudF90eXBlIjtzOjc6Im51bWVyaWMiO3M6MjQ6ImZpZWxkX3Nob3dfZmlsZV9zZWxlY3RvciI7YjowO30='),
(11, 1, 1, 'brokerage_listing_size', 'Listing size', '', 'select', 'Small\nMedium\nLarge', 'n', NULL, NULL, 8, NULL, 'n', 'ltr', 'n', 'n', 'br', 'y', 11, 'any', 'YTowOnt9'),
(12, 1, 1, 'brokerage_feature_text', 'Feature text', '', 'text', '', 'n', NULL, NULL, 8, 256, 'n', 'ltr', 'n', 'n', 'br', 'n', 12, 'all', 'YTozOntzOjEwOiJmaWVsZF9tYXhsIjtzOjM6IjI1NiI7czoxODoiZmllbGRfY29udGVudF90eXBlIjtzOjM6ImFsbCI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtiOjA7fQ=='),
(13, 1, 1, 'brokerage_design_features', 'Design features', '', 'grid', '', 'n', NULL, NULL, 8, NULL, 'n', 'ltr', 'n', 'n', 'xhtml', 'y', 13, 'any', 'YToyOntzOjEzOiJncmlkX21pbl9yb3dzIjtpOjA7czoxMzoiZ3JpZF9tYXhfcm93cyI7czoxOiIzIjt9'),
(14, 1, 1, 'brokerage_layout', 'Layout', '', 'channel_images', '', 'n', NULL, NULL, 8, NULL, 'n', 'ltr', 'n', 'n', 'xhtml', 'y', 14, 'any', 'YToyOntzOjEwOiJmaWVsZF93aWRlIjtiOjE7czoxNDoiY2hhbm5lbF9pbWFnZXMiO2E6MzI6e3M6OToidmlld19tb2RlIjtzOjU6InRpbGVzIjtzOjEzOiJrZWVwX29yaWdpbmFsIjtzOjM6InllcyI7czoxNToidXBsb2FkX2xvY2F0aW9uIjtzOjU6ImxvY2FsIjtzOjk6ImxvY2F0aW9ucyI7YToxOntzOjU6ImxvY2FsIjthOjE6e3M6ODoibG9jYXRpb24iO3M6MToiNiI7fX1zOjEwOiJjYXRlZ29yaWVzIjthOjA6e31zOjE2OiJkZWZhdWx0X2NhdGVnb3J5IjtzOjA6IiI7czoxODoic2hvd19zdG9yZWRfaW1hZ2VzIjtzOjI6Im5vIjtzOjI2OiJsaW1pdF9zdG9yZWRfaW1hZ2VzX2F1dGhvciI7czoyOiJubyI7czoyNToic3RvcmVkX2ltYWdlc19zZWFyY2hfdHlwZSI7czo1OiJlbnRyeSI7czoxNzoic2hvd19pbXBvcnRfZmlsZXMiO3M6Mjoibm8iO3M6MTE6ImltcG9ydF9wYXRoIjtzOjE6Ii8iO3M6MTU6InNob3dfaW1hZ2VfZWRpdCI7czoyOiJubyI7czoxODoic2hvd19pbWFnZV9yZXBsYWNlIjtzOjI6Im5vIjtzOjIyOiJhbGxvd19wZXJfaW1hZ2VfYWN0aW9uIjtzOjI6Im5vIjtzOjExOiJpbWFnZV9saW1pdCI7czowOiIiO3M6MTM6Imh5YnJpZF91cGxvYWQiO3M6MzoieWVzIjtzOjE2OiJwcm9ncmVzc2l2ZV9qcGVnIjtzOjI6Im5vIjtzOjE2OiJ3eXNpd3lnX29yaWdpbmFsIjtzOjM6InllcyI7czoxODoic2F2ZV9kYXRhX2luX2ZpZWxkIjtzOjI6Im5vIjtzOjEzOiJkaXNhYmxlX2NvdmVyIjtzOjM6InllcyI7czoxMToiY29udmVydF9qcGciO3M6Mjoibm8iO3M6MTE6ImNvdmVyX2ZpcnN0IjtzOjM6InllcyI7czoxNDoid3lzaXd5Z19vdXRwdXQiO3M6OToiaW1hZ2VfdXJsIjtzOjEwOiJkaXJlY3RfdXJsIjtzOjM6InllcyI7czoxMjoibWF4X2ZpbGVzaXplIjtzOjA6IiI7czoxMDoicGFyc2VfaXB0YyI7czoyOiJubyI7czoxMDoicGFyc2VfZXhpZiI7czoyOiJubyI7czo5OiJwYXJzZV94bXAiO3M6Mjoibm8iO3M6NzoiY29sdW1ucyI7YToxMzp7czo3OiJyb3dfbnVtIjtzOjE6IiMiO3M6MjoiaWQiO3M6MjoiSUQiO3M6NToiaW1hZ2UiO3M6NToiSW1hZ2UiO3M6ODoiZmlsZW5hbWUiO3M6MDoiIjtzOjU6InRpdGxlIjtzOjU6IlRpdGxlIjtzOjk6InVybF90aXRsZSI7czowOiIiO3M6NDoiZGVzYyI7czoxMToiRGVzY3JpcHRpb24iO3M6ODoiY2F0ZWdvcnkiO3M6MDoiIjtzOjk6ImNpZmllbGRfMSI7czowOiIiO3M6OToiY2lmaWVsZF8yIjtzOjA6IiI7czo5OiJjaWZpZWxkXzMiO3M6MDoiIjtzOjk6ImNpZmllbGRfNCI7czowOiIiO3M6OToiY2lmaWVsZF81IjtzOjA6IiI7fXM6MTU6ImNvbHVtbnNfZGVmYXVsdCI7YTo5OntzOjU6InRpdGxlIjtzOjA6IiI7czo5OiJ1cmxfdGl0bGUiO3M6MDoiIjtzOjQ6ImRlc2MiO3M6MDoiIjtzOjg6ImNhdGVnb3J5IjtzOjA6IiI7czo5OiJjaWZpZWxkXzEiO3M6MDoiIjtzOjk6ImNpZmllbGRfMiI7czowOiIiO3M6OToiY2lmaWVsZF8zIjtzOjA6IiI7czo5OiJjaWZpZWxkXzQiO3M6MDoiIjtzOjk6ImNpZmllbGRfNSI7czowOiIiO31zOjg6Im5vX3NpemVzIjtzOjM6InllcyI7czoxMzoiYWN0aW9uX2dyb3VwcyI7YTowOnt9fX0='),
(15, 1, 1, 'brokerage_gallery_interior', 'Gallery - interior', '', 'channel_images', '', 'n', NULL, NULL, 8, NULL, 'n', 'ltr', 'n', 'n', 'xhtml', 'y', 15, 'any', 'YToyOntzOjEwOiJmaWVsZF93aWRlIjtiOjE7czoxNDoiY2hhbm5lbF9pbWFnZXMiO2E6MzI6e3M6OToidmlld19tb2RlIjtzOjU6InRpbGVzIjtzOjEzOiJrZWVwX29yaWdpbmFsIjtzOjM6InllcyI7czoxNToidXBsb2FkX2xvY2F0aW9uIjtzOjU6ImxvY2FsIjtzOjk6ImxvY2F0aW9ucyI7YToxOntzOjU6ImxvY2FsIjthOjE6e3M6ODoibG9jYXRpb24iO3M6MToiNiI7fX1zOjEwOiJjYXRlZ29yaWVzIjthOjA6e31zOjE2OiJkZWZhdWx0X2NhdGVnb3J5IjtzOjA6IiI7czoxODoic2hvd19zdG9yZWRfaW1hZ2VzIjtzOjI6Im5vIjtzOjI2OiJsaW1pdF9zdG9yZWRfaW1hZ2VzX2F1dGhvciI7czoyOiJubyI7czoyNToic3RvcmVkX2ltYWdlc19zZWFyY2hfdHlwZSI7czo1OiJlbnRyeSI7czoxNzoic2hvd19pbXBvcnRfZmlsZXMiO3M6Mjoibm8iO3M6MTE6ImltcG9ydF9wYXRoIjtzOjE6Ii8iO3M6MTU6InNob3dfaW1hZ2VfZWRpdCI7czoyOiJubyI7czoxODoic2hvd19pbWFnZV9yZXBsYWNlIjtzOjI6Im5vIjtzOjIyOiJhbGxvd19wZXJfaW1hZ2VfYWN0aW9uIjtzOjI6Im5vIjtzOjExOiJpbWFnZV9saW1pdCI7czowOiIiO3M6MTM6Imh5YnJpZF91cGxvYWQiO3M6MzoieWVzIjtzOjE2OiJwcm9ncmVzc2l2ZV9qcGVnIjtzOjI6Im5vIjtzOjE2OiJ3eXNpd3lnX29yaWdpbmFsIjtzOjM6InllcyI7czoxODoic2F2ZV9kYXRhX2luX2ZpZWxkIjtzOjI6Im5vIjtzOjEzOiJkaXNhYmxlX2NvdmVyIjtzOjM6InllcyI7czoxMToiY29udmVydF9qcGciO3M6Mjoibm8iO3M6MTE6ImNvdmVyX2ZpcnN0IjtzOjM6InllcyI7czoxNDoid3lzaXd5Z19vdXRwdXQiO3M6OToiaW1hZ2VfdXJsIjtzOjEwOiJkaXJlY3RfdXJsIjtzOjM6InllcyI7czoxMjoibWF4X2ZpbGVzaXplIjtzOjA6IiI7czoxMDoicGFyc2VfaXB0YyI7czoyOiJubyI7czoxMDoicGFyc2VfZXhpZiI7czoyOiJubyI7czo5OiJwYXJzZV94bXAiO3M6Mjoibm8iO3M6NzoiY29sdW1ucyI7YToxMzp7czo3OiJyb3dfbnVtIjtzOjE6IiMiO3M6MjoiaWQiO3M6MjoiSUQiO3M6NToiaW1hZ2UiO3M6NToiSW1hZ2UiO3M6ODoiZmlsZW5hbWUiO3M6MDoiIjtzOjU6InRpdGxlIjtzOjU6IlRpdGxlIjtzOjk6InVybF90aXRsZSI7czowOiIiO3M6NDoiZGVzYyI7czoxMToiRGVzY3JpcHRpb24iO3M6ODoiY2F0ZWdvcnkiO3M6MDoiIjtzOjk6ImNpZmllbGRfMSI7czowOiIiO3M6OToiY2lmaWVsZF8yIjtzOjA6IiI7czo5OiJjaWZpZWxkXzMiO3M6MDoiIjtzOjk6ImNpZmllbGRfNCI7czowOiIiO3M6OToiY2lmaWVsZF81IjtzOjA6IiI7fXM6MTU6ImNvbHVtbnNfZGVmYXVsdCI7YTo5OntzOjU6InRpdGxlIjtzOjA6IiI7czo5OiJ1cmxfdGl0bGUiO3M6MDoiIjtzOjQ6ImRlc2MiO3M6MDoiIjtzOjg6ImNhdGVnb3J5IjtzOjA6IiI7czo5OiJjaWZpZWxkXzEiO3M6MDoiIjtzOjk6ImNpZmllbGRfMiI7czowOiIiO3M6OToiY2lmaWVsZF8zIjtzOjA6IiI7czo5OiJjaWZpZWxkXzQiO3M6MDoiIjtzOjk6ImNpZmllbGRfNSI7czowOiIiO31zOjg6Im5vX3NpemVzIjtzOjM6InllcyI7czoxMzoiYWN0aW9uX2dyb3VwcyI7YTowOnt9fX0='),
(16, 1, 1, 'brokerage_listing_images', 'Listing images', '', 'channel_images', '', 'n', NULL, NULL, 8, NULL, 'n', 'ltr', 'n', 'n', 'xhtml', 'y', 16, 'any', 'YToyOntzOjEwOiJmaWVsZF93aWRlIjtiOjE7czoxNDoiY2hhbm5lbF9pbWFnZXMiO2E6MzI6e3M6OToidmlld19tb2RlIjtzOjU6InRpbGVzIjtzOjEzOiJrZWVwX29yaWdpbmFsIjtzOjM6InllcyI7czoxNToidXBsb2FkX2xvY2F0aW9uIjtzOjU6ImxvY2FsIjtzOjk6ImxvY2F0aW9ucyI7YToxOntzOjU6ImxvY2FsIjthOjE6e3M6ODoibG9jYXRpb24iO3M6MToiNiI7fX1zOjEwOiJjYXRlZ29yaWVzIjthOjA6e31zOjE2OiJkZWZhdWx0X2NhdGVnb3J5IjtzOjA6IiI7czoxODoic2hvd19zdG9yZWRfaW1hZ2VzIjtzOjI6Im5vIjtzOjI2OiJsaW1pdF9zdG9yZWRfaW1hZ2VzX2F1dGhvciI7czoyOiJubyI7czoyNToic3RvcmVkX2ltYWdlc19zZWFyY2hfdHlwZSI7czo1OiJlbnRyeSI7czoxNzoic2hvd19pbXBvcnRfZmlsZXMiO3M6Mjoibm8iO3M6MTE6ImltcG9ydF9wYXRoIjtzOjE6Ii8iO3M6MTU6InNob3dfaW1hZ2VfZWRpdCI7czoyOiJubyI7czoxODoic2hvd19pbWFnZV9yZXBsYWNlIjtzOjI6Im5vIjtzOjIyOiJhbGxvd19wZXJfaW1hZ2VfYWN0aW9uIjtzOjI6Im5vIjtzOjExOiJpbWFnZV9saW1pdCI7czowOiIiO3M6MTM6Imh5YnJpZF91cGxvYWQiO3M6MzoieWVzIjtzOjE2OiJwcm9ncmVzc2l2ZV9qcGVnIjtzOjI6Im5vIjtzOjE2OiJ3eXNpd3lnX29yaWdpbmFsIjtzOjM6InllcyI7czoxODoic2F2ZV9kYXRhX2luX2ZpZWxkIjtzOjI6Im5vIjtzOjEzOiJkaXNhYmxlX2NvdmVyIjtzOjM6InllcyI7czoxMToiY29udmVydF9qcGciO3M6Mjoibm8iO3M6MTE6ImNvdmVyX2ZpcnN0IjtzOjM6InllcyI7czoxNDoid3lzaXd5Z19vdXRwdXQiO3M6OToiaW1hZ2VfdXJsIjtzOjEwOiJkaXJlY3RfdXJsIjtzOjM6InllcyI7czoxMjoibWF4X2ZpbGVzaXplIjtzOjA6IiI7czoxMDoicGFyc2VfaXB0YyI7czoyOiJubyI7czoxMDoicGFyc2VfZXhpZiI7czoyOiJubyI7czo5OiJwYXJzZV94bXAiO3M6Mjoibm8iO3M6NzoiY29sdW1ucyI7YToxMzp7czo3OiJyb3dfbnVtIjtzOjE6IiMiO3M6MjoiaWQiO3M6MjoiSUQiO3M6NToiaW1hZ2UiO3M6NToiSW1hZ2UiO3M6ODoiZmlsZW5hbWUiO3M6MDoiIjtzOjU6InRpdGxlIjtzOjU6IlRpdGxlIjtzOjk6InVybF90aXRsZSI7czowOiIiO3M6NDoiZGVzYyI7czoxMToiRGVzY3JpcHRpb24iO3M6ODoiY2F0ZWdvcnkiO3M6MDoiIjtzOjk6ImNpZmllbGRfMSI7czowOiIiO3M6OToiY2lmaWVsZF8yIjtzOjA6IiI7czo5OiJjaWZpZWxkXzMiO3M6MDoiIjtzOjk6ImNpZmllbGRfNCI7czowOiIiO3M6OToiY2lmaWVsZF81IjtzOjA6IiI7fXM6MTU6ImNvbHVtbnNfZGVmYXVsdCI7YTo5OntzOjU6InRpdGxlIjtzOjA6IiI7czo5OiJ1cmxfdGl0bGUiO3M6MDoiIjtzOjQ6ImRlc2MiO3M6MDoiIjtzOjg6ImNhdGVnb3J5IjtzOjA6IiI7czo5OiJjaWZpZWxkXzEiO3M6MDoiIjtzOjk6ImNpZmllbGRfMiI7czowOiIiO3M6OToiY2lmaWVsZF8zIjtzOjA6IiI7czo5OiJjaWZpZWxkXzQiO3M6MDoiIjtzOjk6ImNpZmllbGRfNSI7czowOiIiO31zOjg6Im5vX3NpemVzIjtzOjM6InllcyI7czoxMzoiYWN0aW9uX2dyb3VwcyI7YTowOnt9fX0='),
(17, 1, 1, 'brokerage_main_image', 'Main image', '', 'channel_images', '', 'n', NULL, NULL, 8, NULL, 'n', 'ltr', 'n', 'n', 'xhtml', 'y', 17, 'any', 'YToyOntzOjEwOiJmaWVsZF93aWRlIjtiOjE7czoxNDoiY2hhbm5lbF9pbWFnZXMiO2E6MzI6e3M6OToidmlld19tb2RlIjtzOjU6InRpbGVzIjtzOjEzOiJrZWVwX29yaWdpbmFsIjtzOjM6InllcyI7czoxNToidXBsb2FkX2xvY2F0aW9uIjtzOjU6ImxvY2FsIjtzOjk6ImxvY2F0aW9ucyI7YToxOntzOjU6ImxvY2FsIjthOjE6e3M6ODoibG9jYXRpb24iO3M6MToiNiI7fX1zOjEwOiJjYXRlZ29yaWVzIjthOjA6e31zOjE2OiJkZWZhdWx0X2NhdGVnb3J5IjtzOjA6IiI7czoxODoic2hvd19zdG9yZWRfaW1hZ2VzIjtzOjI6Im5vIjtzOjI2OiJsaW1pdF9zdG9yZWRfaW1hZ2VzX2F1dGhvciI7czoyOiJubyI7czoyNToic3RvcmVkX2ltYWdlc19zZWFyY2hfdHlwZSI7czo1OiJlbnRyeSI7czoxNzoic2hvd19pbXBvcnRfZmlsZXMiO3M6Mjoibm8iO3M6MTE6ImltcG9ydF9wYXRoIjtzOjE6Ii8iO3M6MTU6InNob3dfaW1hZ2VfZWRpdCI7czoyOiJubyI7czoxODoic2hvd19pbWFnZV9yZXBsYWNlIjtzOjI6Im5vIjtzOjIyOiJhbGxvd19wZXJfaW1hZ2VfYWN0aW9uIjtzOjI6Im5vIjtzOjExOiJpbWFnZV9saW1pdCI7czoxOiIxIjtzOjEzOiJoeWJyaWRfdXBsb2FkIjtzOjM6InllcyI7czoxNjoicHJvZ3Jlc3NpdmVfanBlZyI7czoyOiJubyI7czoxNjoid3lzaXd5Z19vcmlnaW5hbCI7czozOiJ5ZXMiO3M6MTg6InNhdmVfZGF0YV9pbl9maWVsZCI7czoyOiJubyI7czoxMzoiZGlzYWJsZV9jb3ZlciI7czozOiJ5ZXMiO3M6MTE6ImNvbnZlcnRfanBnIjtzOjI6Im5vIjtzOjExOiJjb3Zlcl9maXJzdCI7czozOiJ5ZXMiO3M6MTQ6Ind5c2l3eWdfb3V0cHV0IjtzOjk6ImltYWdlX3VybCI7czoxMDoiZGlyZWN0X3VybCI7czozOiJ5ZXMiO3M6MTI6Im1heF9maWxlc2l6ZSI7czowOiIiO3M6MTA6InBhcnNlX2lwdGMiO3M6Mjoibm8iO3M6MTA6InBhcnNlX2V4aWYiO3M6Mjoibm8iO3M6OToicGFyc2VfeG1wIjtzOjI6Im5vIjtzOjc6ImNvbHVtbnMiO2E6MTM6e3M6Nzoicm93X251bSI7czoxOiIjIjtzOjI6ImlkIjtzOjI6IklEIjtzOjU6ImltYWdlIjtzOjU6IkltYWdlIjtzOjg6ImZpbGVuYW1lIjtzOjA6IiI7czo1OiJ0aXRsZSI7czo1OiJUaXRsZSI7czo5OiJ1cmxfdGl0bGUiO3M6MDoiIjtzOjQ6ImRlc2MiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjg6ImNhdGVnb3J5IjtzOjA6IiI7czo5OiJjaWZpZWxkXzEiO3M6MDoiIjtzOjk6ImNpZmllbGRfMiI7czowOiIiO3M6OToiY2lmaWVsZF8zIjtzOjA6IiI7czo5OiJjaWZpZWxkXzQiO3M6MDoiIjtzOjk6ImNpZmllbGRfNSI7czowOiIiO31zOjE1OiJjb2x1bW5zX2RlZmF1bHQiO2E6OTp7czo1OiJ0aXRsZSI7czowOiIiO3M6OToidXJsX3RpdGxlIjtzOjA6IiI7czo0OiJkZXNjIjtzOjA6IiI7czo4OiJjYXRlZ29yeSI7czowOiIiO3M6OToiY2lmaWVsZF8xIjtzOjA6IiI7czo5OiJjaWZpZWxkXzIiO3M6MDoiIjtzOjk6ImNpZmllbGRfMyI7czowOiIiO3M6OToiY2lmaWVsZF80IjtzOjA6IiI7czo5OiJjaWZpZWxkXzUiO3M6MDoiIjt9czo4OiJub19zaXplcyI7czozOiJ5ZXMiO3M6MTM6ImFjdGlvbl9ncm91cHMiO2E6MDp7fX19'),
(18, 1, 1, 'brokerage_currency', 'Currency', '', 'select', '£\n$\n€', 'n', NULL, NULL, 8, NULL, 'n', 'ltr', 'n', 'n', 'br', 'y', 18, 'any', 'YTowOnt9'),
(19, 1, 1, 'brokerage_vat', 'VAT', '', 'select', 'inc VAT\nexc VAT', 'n', NULL, NULL, 8, NULL, 'n', 'ltr', 'n', 'n', 'br', 'y', 19, 'any', 'YTowOnt9'),
(20, 1, 1, 'brokerage_gallery_exterior', 'Gallery - exterior', '', 'channel_images', '', 'n', NULL, NULL, 8, NULL, 'n', 'ltr', 'n', 'n', 'xhtml', 'y', 20, 'any', 'YToyOntzOjEwOiJmaWVsZF93aWRlIjtiOjE7czoxNDoiY2hhbm5lbF9pbWFnZXMiO2E6MzI6e3M6OToidmlld19tb2RlIjtzOjU6InRpbGVzIjtzOjEzOiJrZWVwX29yaWdpbmFsIjtzOjM6InllcyI7czoxNToidXBsb2FkX2xvY2F0aW9uIjtzOjU6ImxvY2FsIjtzOjk6ImxvY2F0aW9ucyI7YToxOntzOjU6ImxvY2FsIjthOjE6e3M6ODoibG9jYXRpb24iO3M6MToiNiI7fX1zOjEwOiJjYXRlZ29yaWVzIjthOjA6e31zOjE2OiJkZWZhdWx0X2NhdGVnb3J5IjtzOjA6IiI7czoxODoic2hvd19zdG9yZWRfaW1hZ2VzIjtzOjI6Im5vIjtzOjI2OiJsaW1pdF9zdG9yZWRfaW1hZ2VzX2F1dGhvciI7czoyOiJubyI7czoyNToic3RvcmVkX2ltYWdlc19zZWFyY2hfdHlwZSI7czo1OiJlbnRyeSI7czoxNzoic2hvd19pbXBvcnRfZmlsZXMiO3M6Mjoibm8iO3M6MTE6ImltcG9ydF9wYXRoIjtzOjE6Ii8iO3M6MTU6InNob3dfaW1hZ2VfZWRpdCI7czoyOiJubyI7czoxODoic2hvd19pbWFnZV9yZXBsYWNlIjtzOjI6Im5vIjtzOjIyOiJhbGxvd19wZXJfaW1hZ2VfYWN0aW9uIjtzOjI6Im5vIjtzOjExOiJpbWFnZV9saW1pdCI7czowOiIiO3M6MTM6Imh5YnJpZF91cGxvYWQiO3M6MzoieWVzIjtzOjE2OiJwcm9ncmVzc2l2ZV9qcGVnIjtzOjI6Im5vIjtzOjE2OiJ3eXNpd3lnX29yaWdpbmFsIjtzOjM6InllcyI7czoxODoic2F2ZV9kYXRhX2luX2ZpZWxkIjtzOjI6Im5vIjtzOjEzOiJkaXNhYmxlX2NvdmVyIjtzOjM6InllcyI7czoxMToiY29udmVydF9qcGciO3M6Mjoibm8iO3M6MTE6ImNvdmVyX2ZpcnN0IjtzOjM6InllcyI7czoxNDoid3lzaXd5Z19vdXRwdXQiO3M6OToiaW1hZ2VfdXJsIjtzOjEwOiJkaXJlY3RfdXJsIjtzOjM6InllcyI7czoxMjoibWF4X2ZpbGVzaXplIjtzOjA6IiI7czoxMDoicGFyc2VfaXB0YyI7czoyOiJubyI7czoxMDoicGFyc2VfZXhpZiI7czoyOiJubyI7czo5OiJwYXJzZV94bXAiO3M6Mjoibm8iO3M6NzoiY29sdW1ucyI7YToxMzp7czo3OiJyb3dfbnVtIjtzOjE6IiMiO3M6MjoiaWQiO3M6MjoiSUQiO3M6NToiaW1hZ2UiO3M6NToiSW1hZ2UiO3M6ODoiZmlsZW5hbWUiO3M6MDoiIjtzOjU6InRpdGxlIjtzOjU6IlRpdGxlIjtzOjk6InVybF90aXRsZSI7czowOiIiO3M6NDoiZGVzYyI7czoxMToiRGVzY3JpcHRpb24iO3M6ODoiY2F0ZWdvcnkiO3M6MDoiIjtzOjk6ImNpZmllbGRfMSI7czowOiIiO3M6OToiY2lmaWVsZF8yIjtzOjA6IiI7czo5OiJjaWZpZWxkXzMiO3M6MDoiIjtzOjk6ImNpZmllbGRfNCI7czowOiIiO3M6OToiY2lmaWVsZF81IjtzOjA6IiI7fXM6MTU6ImNvbHVtbnNfZGVmYXVsdCI7YTo5OntzOjU6InRpdGxlIjtzOjA6IiI7czo5OiJ1cmxfdGl0bGUiO3M6MDoiIjtzOjQ6ImRlc2MiO3M6MDoiIjtzOjg6ImNhdGVnb3J5IjtzOjA6IiI7czo5OiJjaWZpZWxkXzEiO3M6MDoiIjtzOjk6ImNpZmllbGRfMiI7czowOiIiO3M6OToiY2lmaWVsZF8zIjtzOjA6IiI7czo5OiJjaWZpZWxkXzQiO3M6MDoiIjtzOjk6ImNpZmllbGRfNSI7czowOiIiO31zOjg6Im5vX3NpemVzIjtzOjM6InllcyI7czoxMzoiYWN0aW9uX2dyb3VwcyI7YTowOnt9fX0=');

-- --------------------------------------------------------

--
-- Table structure for table `exp_channel_form_settings`
--

CREATE TABLE `exp_channel_form_settings` (
`channel_form_settings_id` int(10) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '0',
  `channel_id` int(6) unsigned NOT NULL DEFAULT '0',
  `default_status` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'open',
  `allow_guest_posts` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `default_author` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_channel_grid_field_13`
--

CREATE TABLE `exp_channel_grid_field_13` (
`row_id` int(10) unsigned NOT NULL,
  `entry_id` int(10) unsigned DEFAULT NULL,
  `row_order` int(10) unsigned DEFAULT NULL,
  `col_id_1` text COLLATE utf8_unicode_ci,
  `col_id_2` text COLLATE utf8_unicode_ci,
  `col_id_3` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_channel_grid_field_13`
--

INSERT INTO `exp_channel_grid_field_13` (`row_id`, `entry_id`, `row_order`, `col_id_1`, `col_id_2`, `col_id_3`) VALUES
(1, 1, 0, 'Design Feature 1', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit</p>', '{filedir_6}feature1.jpg'),
(2, 1, 1, 'Design Feature 1', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit</p>', '{filedir_6}feature2.jpg'),
(3, 1, 2, 'Design Feature 1', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit</p>', '{filedir_6}feature3.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `exp_channel_images`
--

CREATE TABLE `exp_channel_images` (
`image_id` int(10) unsigned NOT NULL,
  `site_id` tinyint(3) unsigned DEFAULT '1',
  `entry_id` int(10) unsigned DEFAULT '0',
  `field_id` mediumint(8) unsigned DEFAULT '0',
  `channel_id` tinyint(3) unsigned DEFAULT '0',
  `member_id` int(10) unsigned DEFAULT '0',
  `is_draft` tinyint(3) unsigned DEFAULT '0',
  `link_image_id` int(10) unsigned DEFAULT '0',
  `link_entry_id` int(10) unsigned DEFAULT '0',
  `link_channel_id` int(10) unsigned DEFAULT '0',
  `link_field_id` int(10) unsigned DEFAULT '0',
  `upload_date` int(10) unsigned DEFAULT '0',
  `cover` tinyint(1) unsigned DEFAULT '0',
  `image_order` smallint(5) unsigned DEFAULT '1',
  `filename` varchar(250) COLLATE utf8_unicode_ci DEFAULT '',
  `extension` varchar(20) COLLATE utf8_unicode_ci DEFAULT '',
  `filesize` int(10) unsigned DEFAULT '0',
  `mime` varchar(20) COLLATE utf8_unicode_ci DEFAULT '',
  `width` smallint(6) DEFAULT '0',
  `height` smallint(6) DEFAULT '0',
  `title` varchar(250) COLLATE utf8_unicode_ci DEFAULT '',
  `url_title` varchar(250) COLLATE utf8_unicode_ci DEFAULT '',
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT '',
  `category` varchar(250) COLLATE utf8_unicode_ci DEFAULT '',
  `cifield_1` varchar(250) COLLATE utf8_unicode_ci DEFAULT '',
  `cifield_2` varchar(250) COLLATE utf8_unicode_ci DEFAULT '',
  `cifield_3` varchar(250) COLLATE utf8_unicode_ci DEFAULT '',
  `cifield_4` varchar(250) COLLATE utf8_unicode_ci DEFAULT '',
  `cifield_5` varchar(250) COLLATE utf8_unicode_ci DEFAULT '',
  `sizes_metadata` varchar(250) COLLATE utf8_unicode_ci DEFAULT '',
  `iptc` text COLLATE utf8_unicode_ci,
  `exif` text COLLATE utf8_unicode_ci,
  `xmp` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_channel_images`
--

INSERT INTO `exp_channel_images` (`image_id`, `site_id`, `entry_id`, `field_id`, `channel_id`, `member_id`, `is_draft`, `link_image_id`, `link_entry_id`, `link_channel_id`, `link_field_id`, `upload_date`, `cover`, `image_order`, `filename`, `extension`, `filesize`, `mime`, `width`, `height`, `title`, `url_title`, `description`, `category`, `cifield_1`, `cifield_2`, `cifield_3`, `cifield_4`, `cifield_5`, `sizes_metadata`, `iptc`, `exif`, `xmp`) VALUES
(1, 1, 1, 15, 1, 1, 0, 0, 0, 0, 0, 1456329189, 0, 1, 'cover.jpg', 'jpg', 372072, 'image/jpeg', 1366, 641, 'Cover', 'cover', '', '', '', '', '', '', '', '', 'YTowOnt9', 'YTowOnt9', ''),
(2, 1, 1, 17, 1, 1, 0, 0, 0, 0, 0, 1456396685, 0, 1, 'cover-2.jpg', 'jpg', 372072, 'image/jpeg', 1366, 641, 'Cover-2', 'cover-2', '', '', '', '', '', '', '', '', 'YTowOnt9', 'YTowOnt9', '');

-- --------------------------------------------------------

--
-- Table structure for table `exp_channel_member_groups`
--

CREATE TABLE `exp_channel_member_groups` (
  `group_id` smallint(4) unsigned NOT NULL,
  `channel_id` int(6) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_channel_titles`
--

CREATE TABLE `exp_channel_titles` (
`entry_id` int(10) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `channel_id` int(4) unsigned NOT NULL,
  `author_id` int(10) unsigned NOT NULL DEFAULT '0',
  `forum_topic_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `url_title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `versioning_enabled` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `view_count_one` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count_two` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count_three` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count_four` int(10) unsigned NOT NULL DEFAULT '0',
  `allow_comments` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `sticky` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `entry_date` int(10) NOT NULL,
  `year` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `month` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `day` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `expiration_date` int(10) NOT NULL DEFAULT '0',
  `comment_expiration_date` int(10) NOT NULL DEFAULT '0',
  `edit_date` bigint(14) DEFAULT NULL,
  `recent_comment_date` int(10) DEFAULT NULL,
  `comment_total` int(4) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_channel_titles`
--

INSERT INTO `exp_channel_titles` (`entry_id`, `site_id`, `channel_id`, `author_id`, `forum_topic_id`, `ip_address`, `title`, `url_title`, `status`, `versioning_enabled`, `view_count_one`, `view_count_two`, `view_count_three`, `view_count_four`, `allow_comments`, `sticky`, `entry_date`, `year`, `month`, `day`, `expiration_date`, `comment_expiration_date`, `edit_date`, `recent_comment_date`, `comment_total`) VALUES
(1, 1, 1, 1, NULL, '127.0.0.1', 'Penelope', 'penelope', 'open', 'n', 0, 0, 0, 0, 'y', 'n', 1456328580, '2016', '02', '24', 0, 0, 1456396685, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `exp_comments`
--

CREATE TABLE `exp_comments` (
`comment_id` int(10) unsigned NOT NULL,
  `site_id` int(4) DEFAULT '1',
  `entry_id` int(10) unsigned DEFAULT '0',
  `channel_id` int(4) unsigned DEFAULT '1',
  `author_id` int(10) unsigned DEFAULT '0',
  `status` char(1) COLLATE utf8_unicode_ci DEFAULT '0',
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(75) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(75) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment_date` int(10) DEFAULT NULL,
  `edit_date` int(10) DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_comment_subscriptions`
--

CREATE TABLE `exp_comment_subscriptions` (
`subscription_id` int(10) unsigned NOT NULL,
  `entry_id` int(10) unsigned DEFAULT NULL,
  `member_id` int(10) DEFAULT '0',
  `email` varchar(75) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subscription_date` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notification_sent` char(1) COLLATE utf8_unicode_ci DEFAULT 'n',
  `hash` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_content_types`
--

CREATE TABLE `exp_content_types` (
`content_type_id` int(10) unsigned NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_content_types`
--

INSERT INTO `exp_content_types` (`content_type_id`, `name`) VALUES
(1, 'grid'),
(2, 'channel');

-- --------------------------------------------------------

--
-- Table structure for table `exp_cp_log`
--

CREATE TABLE `exp_cp_log` (
`id` int(10) NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `member_id` int(10) unsigned NOT NULL,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `act_date` int(10) NOT NULL,
  `action` varchar(200) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_cp_log`
--

INSERT INTO `exp_cp_log` (`id`, `site_id`, `member_id`, `username`, `ip_address`, `act_date`, `action`) VALUES
(1, 1, 1, 'Chris', '127.0.0.1', 1456308616, 'Logged in'),
(2, 1, 1, 'Chris', '127.0.0.1', 1456323994, 'Channel Created&nbsp;&nbsp;Brokerage Fleet'),
(3, 1, 1, 'Chris', '127.0.0.1', 1456393817, 'Logged in');

-- --------------------------------------------------------

--
-- Table structure for table `exp_cp_search_index`
--

CREATE TABLE `exp_cp_search_index` (
`search_id` int(10) unsigned NOT NULL,
  `controller` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `method` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_developer_log`
--

CREATE TABLE `exp_developer_log` (
`log_id` int(10) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `viewed` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `description` text COLLATE utf8_unicode_ci,
  `function` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `line` int(10) unsigned DEFAULT NULL,
  `file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deprecated_since` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `use_instead` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_id` int(10) unsigned NOT NULL DEFAULT '0',
  `template_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_group` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addon_module` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addon_method` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `snippets` text COLLATE utf8_unicode_ci,
  `hash` char(32) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_developer_log`
--

INSERT INTO `exp_developer_log` (`log_id`, `timestamp`, `viewed`, `description`, `function`, `line`, `file`, `deprecated_since`, `use_instead`, `template_id`, `template_name`, `template_group`, `addon_module`, `addon_method`, `snippets`, `hash`) VALUES
(1, 1456329189, 'n', NULL, 'fetch_file_paths()', 494, '/Applications/MAMP/htdocs/oyster/system/user/addons/editor/ft.editor.php', '3.0', 'File_upload_preferences_model::get_paths()', 0, NULL, NULL, NULL, NULL, NULL, 'b86b6f63893ac050bfa827947ada69a9');

-- --------------------------------------------------------

--
-- Table structure for table `exp_editor_configs`
--

CREATE TABLE `exp_editor_configs` (
`id` int(10) unsigned NOT NULL,
  `site_id` smallint(5) unsigned DEFAULT '1',
  `label` varchar(250) COLLATE utf8_unicode_ci DEFAULT '',
  `type` varchar(250) COLLATE utf8_unicode_ci DEFAULT '',
  `settings` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_editor_configs`
--

INSERT INTO `exp_editor_configs` (`id`, `site_id`, `label`, `type`, `settings`) VALUES
(1, 1, 'Basic', 'redactor', '{"buttons":["format","bold","italic","lists","link"],"plugins":["video"],"upload_service":"local","files_upload_location":"0","images_upload_location":"0","s3":{"files":{"bucket":"","region":"us-east-1"},"images":{"bucket":"","region":"us-east-1"},"aws_access_key":"","aws_secret_key":""}}'),
(2, 1, 'Advanced', 'redactor', '{"buttons":["format","bold","italic","underline","deleted","lists","image","file","link","horizontalrule"],"plugins":["source","video","table"],"upload_service":"local","files_upload_location":"0","images_upload_location":"0","s3":{"files":{"bucket":"","region":"us-east-1"},"images":{"bucket":"","region":"us-east-1"},"aws_access_key":"","aws_secret_key":""}}');

-- --------------------------------------------------------

--
-- Table structure for table `exp_email_cache`
--

CREATE TABLE `exp_email_cache` (
`cache_id` int(6) unsigned NOT NULL,
  `cache_date` int(10) unsigned NOT NULL DEFAULT '0',
  `total_sent` int(6) unsigned NOT NULL,
  `from_name` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `from_email` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  `recipient` text COLLATE utf8_unicode_ci NOT NULL,
  `cc` text COLLATE utf8_unicode_ci NOT NULL,
  `bcc` text COLLATE utf8_unicode_ci NOT NULL,
  `recipient_array` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `plaintext_alt` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `mailtype` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `text_fmt` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `wordwrap` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `attachments` mediumtext COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_email_cache_mg`
--

CREATE TABLE `exp_email_cache_mg` (
  `cache_id` int(6) unsigned NOT NULL,
  `group_id` smallint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_email_cache_ml`
--

CREATE TABLE `exp_email_cache_ml` (
  `cache_id` int(6) unsigned NOT NULL,
  `list_id` smallint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_email_console_cache`
--

CREATE TABLE `exp_email_console_cache` (
`cache_id` int(6) unsigned NOT NULL,
  `cache_date` int(10) unsigned NOT NULL DEFAULT '0',
  `member_id` int(10) unsigned NOT NULL,
  `member_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `recipient` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  `recipient_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_entry_versioning`
--

CREATE TABLE `exp_entry_versioning` (
`version_id` int(10) unsigned NOT NULL,
  `entry_id` int(10) unsigned NOT NULL,
  `channel_id` int(4) unsigned NOT NULL,
  `author_id` int(10) unsigned NOT NULL,
  `version_date` int(10) NOT NULL,
  `version_data` mediumtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_entry_versioning`
--

INSERT INTO `exp_entry_versioning` (`version_id`, `entry_id`, `channel_id`, `author_id`, `version_date`, `version_data`) VALUES
(1, 1, 1, 1, 1456331871, 'a:69:{s:8:"entry_id";s:1:"1";s:7:"site_id";s:1:"1";s:10:"channel_id";s:1:"1";s:9:"author_id";i:1;s:14:"forum_topic_id";N;s:10:"ip_address";s:9:"127.0.0.1";s:5:"title";s:8:"Penelope";s:9:"url_title";s:8:"penelope";s:6:"status";s:4:"open";s:18:"versioning_enabled";b:1;s:14:"view_count_one";s:1:"0";s:14:"view_count_two";s:1:"0";s:16:"view_count_three";s:1:"0";s:15:"view_count_four";s:1:"0";s:14:"allow_comments";b:1;s:6:"sticky";b:0;s:10:"entry_date";i:1456328580;s:4:"year";s:4:"2016";s:5:"month";s:2:"02";s:3:"day";s:2:"24";s:15:"expiration_date";i:0;s:23:"comment_expiration_date";i:0;s:9:"edit_date";i:1456331871;s:19:"recent_comment_date";s:0:"";s:13:"comment_total";s:1:"0";s:10:"field_id_1";s:10:"Oyster 885";s:10:"field_ft_1";s:2:"br";s:10:"field_id_2";s:1639:"<p>An exclusive listing with Oyster Brokerage and a rare opportunity for the discerning Superyacht enthusiast to own the second in class Oyster 100. Penelope has recently completed her flag, class and 3 year docking surveys, which she passed with flying colours. This, along with many other maintenance projects mean that she is truly ''ready to sail away''. She is currently lying in Palma and intends to stay all winter. Penelope has enjoyed many successes since her launch in 2013 including: Finalist in the Boat International World Superyacht Awards 2013, first place in the 2014 Oyster regatta in Antigua and flourishing charter seasons in the Caribbean and Mediterranean – all forming an attractive offering in the pre-owned Superyacht arena.</p>\n\n<p>Penelope is designed with the specification, features and classification of very much larger yachts. Her accommodation layout offers three sumptuous staterooms aft and two crew cabins forward. With panoramic views from the raised saloon which leads forward and down to a further lounge and separate dining area. Forward of the main living arrangement is the crew mess, galley and two crew cabins.</p>\n\n<p>Penelope couldn’t be in a finer condition since her launch in 2012. The Southampton yard visit in early 2014 ensured the warranty list was ticked off, systems thoroughly serviced and allowed some owner modifications. Along with the highest possible Lloyds certification the Oyster 100 is designed and built to ''go places'' and when compared to other like sized sailing yachts, the amount of light and space available will certainly ensure that she is one not to be missed.</p>";s:10:"field_ft_2";s:5:"xhtml";s:10:"field_id_3";s:247:"<p>For further details please contact Jamie Collins - <a href="mailto:jamie.collins@oysteryachts.com">jamie.collins@oysteryachts.com</a> <br>For general information about the history of this Oyster model, please go to the Oyster Fleet Overview</p>";s:10:"field_ft_3";s:5:"xhtml";s:10:"field_id_4";s:12:"Oyster Palma";s:10:"field_ft_4";s:2:"br";s:10:"field_id_5";s:4:"2012";s:10:"field_ft_5";s:2:"br";s:10:"field_id_6";s:6:"Cutter";s:10:"field_ft_6";s:2:"br";s:10:"field_id_7";s:17:" Crown Cut Cherry";s:10:"field_ft_7";s:2:"br";s:10:"field_id_8";s:27:"Eight berths in four cabins";s:10:"field_ft_8";s:2:"br";s:10:"field_id_9";s:9:"Available";s:10:"field_ft_9";s:2:"br";s:11:"field_id_10";s:19:"£6,000,000 inc VAT";s:11:"field_ft_10";s:2:"br";s:11:"field_id_11";s:5:"Small";s:11:"field_ft_11";s:2:"br";s:11:"field_id_12";s:0:"";s:11:"field_ft_12";s:2:"br";s:11:"field_id_13";s:1:" ";s:11:"field_ft_13";s:5:"xhtml";s:11:"field_id_14";s:0:"";s:11:"field_ft_14";s:5:"xhtml";s:11:"field_id_15";s:13:"ChannelImages";s:11:"field_ft_15";s:5:"xhtml";s:11:"field_id_16";s:0:"";s:11:"field_ft_16";s:5:"xhtml";s:14:"field_ft_title";N;s:18:"field_ft_url_title";s:5:"xhtml";s:19:"field_ft_entry_date";s:4:"text";s:24:"field_ft_expiration_date";s:4:"text";s:32:"field_ft_comment_expiration_date";s:4:"text";s:19:"field_ft_channel_id";N;s:15:"field_ft_status";N;s:18:"field_ft_author_id";N;s:15:"field_ft_sticky";N;s:23:"field_ft_allow_comments";N;s:19:"channel_images_file";s:0:"";s:12:"locationtype";s:5:"local";}'),
(2, 1, 1, 1, 1456331889, 'a:69:{s:8:"entry_id";s:1:"1";s:7:"site_id";s:1:"1";s:10:"channel_id";s:1:"1";s:9:"author_id";i:1;s:14:"forum_topic_id";N;s:10:"ip_address";s:9:"127.0.0.1";s:5:"title";s:8:"Penelope";s:9:"url_title";s:8:"penelope";s:6:"status";s:4:"open";s:18:"versioning_enabled";b:1;s:14:"view_count_one";s:1:"0";s:14:"view_count_two";s:1:"0";s:16:"view_count_three";s:1:"0";s:15:"view_count_four";s:1:"0";s:14:"allow_comments";b:1;s:6:"sticky";b:0;s:10:"entry_date";i:1456328580;s:4:"year";s:4:"2016";s:5:"month";s:2:"02";s:3:"day";s:2:"24";s:15:"expiration_date";i:0;s:23:"comment_expiration_date";i:0;s:9:"edit_date";i:1456331889;s:19:"recent_comment_date";s:0:"";s:13:"comment_total";s:1:"0";s:10:"field_id_1";s:10:"Oyster 885";s:10:"field_ft_1";s:2:"br";s:10:"field_id_2";s:1641:"<p>An exclusive listing with Oyster Brokerage and a rare opportunity for the discerning Superyacht enthusiast to own the second in class Oyster 100. Penelope has recently completed her flag, class and 3 year docking surveys, which she passed with flying colours. This, along with many other maintenance projects mean that she is truly ''ready to sail away''. She is currently lying in Palma and intends to stay all winter. Penelope has enjoyed many successes since her launch in 2013 including: Finalist in the Boat International World Superyacht Awards 2013, first place in the 2014 Oyster regatta in Antigua and flourishing charter seasons in the Caribbean and Mediterranean – all forming an attractive offering in the pre-owned Superyacht arena.</p>\n\n<p>Penelope is designed with the specification, features and classification of very much larger yachts. Her accommodation layout offers three sumptuous staterooms aft and two crew cabins forward. With panoramic views from the raised saloon which leads forward and down to a further lounge and separate dining area. Forward of the main living arrangement is the crew mess, galley and two crew cabins.</p>\n\n\n\n<p>Penelope couldn’t be in a finer condition since her launch in 2012. The Southampton yard visit in early 2014 ensured the warranty list was ticked off, systems thoroughly serviced and allowed some owner modifications. Along with the highest possible Lloyds certification the Oyster 100 is designed and built to ''go places'' and when compared to other like sized sailing yachts, the amount of light and space available will certainly ensure that she is one not to be missed.</p>";s:10:"field_ft_2";s:5:"xhtml";s:10:"field_id_3";s:247:"<p>For further details please contact Jamie Collins - <a href="mailto:jamie.collins@oysteryachts.com">jamie.collins@oysteryachts.com</a> <br>For general information about the history of this Oyster model, please go to the Oyster Fleet Overview</p>";s:10:"field_ft_3";s:5:"xhtml";s:10:"field_id_4";s:12:"Oyster Palma";s:10:"field_ft_4";s:2:"br";s:10:"field_id_5";s:4:"2012";s:10:"field_ft_5";s:2:"br";s:10:"field_id_6";s:6:"Cutter";s:10:"field_ft_6";s:2:"br";s:10:"field_id_7";s:17:" Crown Cut Cherry";s:10:"field_ft_7";s:2:"br";s:10:"field_id_8";s:27:"Eight berths in four cabins";s:10:"field_ft_8";s:2:"br";s:10:"field_id_9";s:9:"Available";s:10:"field_ft_9";s:2:"br";s:11:"field_id_10";s:19:"£6,000,000 inc VAT";s:11:"field_ft_10";s:2:"br";s:11:"field_id_11";s:5:"Large";s:11:"field_ft_11";s:2:"br";s:11:"field_id_12";s:13:"Reduced Price";s:11:"field_ft_12";s:2:"br";s:11:"field_id_13";s:1:" ";s:11:"field_ft_13";s:5:"xhtml";s:11:"field_id_14";s:0:"";s:11:"field_ft_14";s:5:"xhtml";s:11:"field_id_15";s:13:"ChannelImages";s:11:"field_ft_15";s:5:"xhtml";s:11:"field_id_16";s:0:"";s:11:"field_ft_16";s:5:"xhtml";s:14:"field_ft_title";N;s:18:"field_ft_url_title";s:5:"xhtml";s:19:"field_ft_entry_date";s:4:"text";s:24:"field_ft_expiration_date";s:4:"text";s:32:"field_ft_comment_expiration_date";s:4:"text";s:19:"field_ft_channel_id";N;s:15:"field_ft_status";N;s:18:"field_ft_author_id";N;s:15:"field_ft_sticky";N;s:23:"field_ft_allow_comments";N;s:19:"channel_images_file";s:0:"";s:12:"locationtype";s:5:"local";}'),
(3, 1, 1, 1, 1456393981, 'a:75:{s:8:"entry_id";s:1:"1";s:7:"site_id";s:1:"1";s:10:"channel_id";s:1:"1";s:9:"author_id";i:1;s:14:"forum_topic_id";N;s:10:"ip_address";s:9:"127.0.0.1";s:5:"title";s:8:"Penelope";s:9:"url_title";s:8:"penelope";s:6:"status";s:4:"open";s:18:"versioning_enabled";b:1;s:14:"view_count_one";s:1:"0";s:14:"view_count_two";s:1:"0";s:16:"view_count_three";s:1:"0";s:15:"view_count_four";s:1:"0";s:14:"allow_comments";b:1;s:6:"sticky";b:0;s:10:"entry_date";i:1456328580;s:4:"year";s:4:"2016";s:5:"month";s:2:"02";s:3:"day";s:2:"24";s:15:"expiration_date";i:0;s:23:"comment_expiration_date";i:0;s:9:"edit_date";i:1456393981;s:19:"recent_comment_date";s:0:"";s:13:"comment_total";s:1:"0";s:10:"field_id_1";s:10:"Oyster 885";s:10:"field_ft_1";s:2:"br";s:10:"field_id_2";s:1643:"<p>An exclusive listing with Oyster Brokerage and a rare opportunity for the discerning Superyacht enthusiast to own the second in class Oyster 100. Penelope has recently completed her flag, class and 3 year docking surveys, which she passed with flying colours. This, along with many other maintenance projects mean that she is truly ''ready to sail away''. She is currently lying in Palma and intends to stay all winter. Penelope has enjoyed many successes since her launch in 2013 including: Finalist in the Boat International World Superyacht Awards 2013, first place in the 2014 Oyster regatta in Antigua and flourishing charter seasons in the Caribbean and Mediterranean – all forming an attractive offering in the pre-owned Superyacht arena.</p>\n\n<p>Penelope is designed with the specification, features and classification of very much larger yachts. Her accommodation layout offers three sumptuous staterooms aft and two crew cabins forward. With panoramic views from the raised saloon which leads forward and down to a further lounge and separate dining area. Forward of the main living arrangement is the crew mess, galley and two crew cabins.</p>\n\n\n\n\n\n<p>Penelope couldn’t be in a finer condition since her launch in 2012. The Southampton yard visit in early 2014 ensured the warranty list was ticked off, systems thoroughly serviced and allowed some owner modifications. Along with the highest possible Lloyds certification the Oyster 100 is designed and built to ''go places'' and when compared to other like sized sailing yachts, the amount of light and space available will certainly ensure that she is one not to be missed.</p>";s:10:"field_ft_2";s:5:"xhtml";s:10:"field_id_3";s:247:"<p>For further details please contact Jamie Collins - <a href="mailto:jamie.collins@oysteryachts.com">jamie.collins@oysteryachts.com</a> <br>For general information about the history of this Oyster model, please go to the Oyster Fleet Overview</p>";s:10:"field_ft_3";s:5:"xhtml";s:10:"field_id_4";s:12:"Oyster Palma";s:10:"field_ft_4";s:2:"br";s:10:"field_id_5";s:4:"2012";s:10:"field_ft_5";s:2:"br";s:10:"field_id_6";s:6:"Cutter";s:10:"field_ft_6";s:2:"br";s:10:"field_id_7";s:17:" Crown Cut Cherry";s:10:"field_ft_7";s:2:"br";s:10:"field_id_8";s:27:"Eight berths in four cabins";s:10:"field_ft_8";s:2:"br";s:10:"field_id_9";s:9:"Available";s:10:"field_ft_9";s:2:"br";s:11:"field_id_10";s:7:"6000000";s:11:"field_ft_10";s:2:"br";s:11:"field_id_11";s:5:"Large";s:11:"field_ft_11";s:2:"br";s:11:"field_id_12";s:13:"Reduced Price";s:11:"field_ft_12";s:2:"br";s:11:"field_id_13";s:1:" ";s:11:"field_ft_13";s:5:"xhtml";s:11:"field_id_14";s:0:"";s:11:"field_ft_14";s:5:"xhtml";s:11:"field_id_15";s:13:"ChannelImages";s:11:"field_ft_15";s:5:"xhtml";s:11:"field_id_16";s:0:"";s:11:"field_ft_16";s:5:"xhtml";s:14:"field_ft_title";N;s:18:"field_ft_url_title";s:5:"xhtml";s:19:"field_ft_entry_date";s:4:"text";s:24:"field_ft_expiration_date";s:4:"text";s:32:"field_ft_comment_expiration_date";s:4:"text";s:19:"field_ft_channel_id";N;s:15:"field_ft_status";N;s:18:"field_ft_author_id";N;s:15:"field_ft_sticky";N;s:23:"field_ft_allow_comments";N;s:11:"field_ft_17";s:5:"xhtml";s:11:"field_ft_18";s:2:"br";s:11:"field_ft_19";s:2:"br";s:19:"channel_images_file";s:0:"";s:12:"locationtype";s:5:"local";s:11:"field_id_17";s:0:"";s:11:"field_id_18";s:2:"£";s:11:"field_id_19";s:7:"inc VAT";}'),
(4, 1, 1, 1, 1456396685, 'a:75:{s:8:"entry_id";s:1:"1";s:7:"site_id";s:1:"1";s:10:"channel_id";s:1:"1";s:9:"author_id";i:1;s:14:"forum_topic_id";N;s:10:"ip_address";s:9:"127.0.0.1";s:5:"title";s:8:"Penelope";s:9:"url_title";s:8:"penelope";s:6:"status";s:4:"open";s:18:"versioning_enabled";b:1;s:14:"view_count_one";s:1:"0";s:14:"view_count_two";s:1:"0";s:16:"view_count_three";s:1:"0";s:15:"view_count_four";s:1:"0";s:14:"allow_comments";b:1;s:6:"sticky";b:0;s:10:"entry_date";i:1456328580;s:4:"year";s:4:"2016";s:5:"month";s:2:"02";s:3:"day";s:2:"24";s:15:"expiration_date";i:0;s:23:"comment_expiration_date";i:0;s:9:"edit_date";i:1456396685;s:19:"recent_comment_date";s:0:"";s:13:"comment_total";s:1:"0";s:10:"field_id_1";s:10:"Oyster 885";s:10:"field_ft_1";s:2:"br";s:10:"field_id_2";s:1645:"<p>An exclusive listing with Oyster Brokerage and a rare opportunity for the discerning Superyacht enthusiast to own the second in class Oyster 100. Penelope has recently completed her flag, class and 3 year docking surveys, which she passed with flying colours. This, along with many other maintenance projects mean that she is truly ''ready to sail away''. She is currently lying in Palma and intends to stay all winter. Penelope has enjoyed many successes since her launch in 2013 including: Finalist in the Boat International World Superyacht Awards 2013, first place in the 2014 Oyster regatta in Antigua and flourishing charter seasons in the Caribbean and Mediterranean – all forming an attractive offering in the pre-owned Superyacht arena.</p>\n\n<p>Penelope is designed with the specification, features and classification of very much larger yachts. Her accommodation layout offers three sumptuous staterooms aft and two crew cabins forward. With panoramic views from the raised saloon which leads forward and down to a further lounge and separate dining area. Forward of the main living arrangement is the crew mess, galley and two crew cabins.</p>\n\n\n\n\n\n\n\n<p>Penelope couldn’t be in a finer condition since her launch in 2012. The Southampton yard visit in early 2014 ensured the warranty list was ticked off, systems thoroughly serviced and allowed some owner modifications. Along with the highest possible Lloyds certification the Oyster 100 is designed and built to ''go places'' and when compared to other like sized sailing yachts, the amount of light and space available will certainly ensure that she is one not to be missed.</p>";s:10:"field_ft_2";s:5:"xhtml";s:10:"field_id_3";s:247:"<p>For further details please contact Jamie Collins - <a href="mailto:jamie.collins@oysteryachts.com">jamie.collins@oysteryachts.com</a> <br>For general information about the history of this Oyster model, please go to the Oyster Fleet Overview</p>";s:10:"field_ft_3";s:5:"xhtml";s:10:"field_id_4";s:12:"Oyster Palma";s:10:"field_ft_4";s:2:"br";s:10:"field_id_5";s:4:"2012";s:10:"field_ft_5";s:2:"br";s:10:"field_id_6";s:6:"Cutter";s:10:"field_ft_6";s:2:"br";s:10:"field_id_7";s:17:" Crown Cut Cherry";s:10:"field_ft_7";s:2:"br";s:10:"field_id_8";s:27:"Eight berths in four cabins";s:10:"field_ft_8";s:2:"br";s:10:"field_id_9";s:9:"Available";s:10:"field_ft_9";s:2:"br";s:11:"field_id_10";s:7:"6000000";s:11:"field_ft_10";s:2:"br";s:11:"field_id_11";s:5:"Large";s:11:"field_ft_11";s:2:"br";s:11:"field_id_12";s:13:"Reduced Price";s:11:"field_ft_12";s:2:"br";s:11:"field_id_13";s:1:" ";s:11:"field_ft_13";s:5:"xhtml";s:11:"field_id_14";s:0:"";s:11:"field_ft_14";s:5:"xhtml";s:11:"field_id_15";s:13:"ChannelImages";s:11:"field_ft_15";s:5:"xhtml";s:11:"field_id_16";s:0:"";s:11:"field_ft_16";s:5:"xhtml";s:11:"field_id_17";s:13:"ChannelImages";s:11:"field_ft_17";s:5:"xhtml";s:11:"field_id_18";s:2:"£";s:11:"field_ft_18";s:2:"br";s:11:"field_id_19";s:7:"inc VAT";s:11:"field_ft_19";s:2:"br";s:14:"field_ft_title";N;s:18:"field_ft_url_title";s:5:"xhtml";s:19:"field_ft_entry_date";s:4:"text";s:24:"field_ft_expiration_date";s:4:"text";s:32:"field_ft_comment_expiration_date";s:4:"text";s:19:"field_ft_channel_id";N;s:15:"field_ft_status";N;s:18:"field_ft_author_id";N;s:15:"field_ft_sticky";N;s:23:"field_ft_allow_comments";N;s:19:"channel_images_file";s:0:"";s:12:"locationtype";s:5:"local";}');

-- --------------------------------------------------------

--
-- Table structure for table `exp_extensions`
--

CREATE TABLE `exp_extensions` (
`extension_id` int(10) unsigned NOT NULL,
  `class` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `method` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `hook` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `settings` text COLLATE utf8_unicode_ci NOT NULL,
  `priority` int(2) NOT NULL DEFAULT '10',
  `version` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `enabled` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y'
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_extensions`
--

INSERT INTO `exp_extensions` (`extension_id`, `class`, `method`, `hook`, `settings`, `priority`, `version`, `enabled`) VALUES
(1, 'Rte_ext', 'myaccount_nav_setup', 'myaccount_nav_setup', '', 10, '1.0.1', 'y'),
(2, 'Rte_ext', 'cp_menu_array', 'cp_menu_array', '', 10, '1.0.1', 'y'),
(3, 'Zenbu_ext', 'send_to_addon_post_edit', 'update_multi_entries_start', 's:0:"";', 10, '2.0.0', 'y'),
(4, 'Zenbu_ext', 'send_to_addon_post_delete', 'delete_entries_end', 's:0:"";', 10, '2.0.0', 'y'),
(5, 'Zenbu_ext', 'replace_edit_dropdown', 'cp_js_end', 's:0:"";', 100, '2.0.0', 'y'),
(6, 'Channel_images_ext', 'wygwam_config', 'wygwam_config', 'a:0:{}', 100, '6.0.1', 'y'),
(7, 'Channel_images_ext', 'wygwam_tb_groups', 'wygwam_tb_groups', 'a:0:{}', 100, '6.0.1', 'y'),
(8, 'Channel_images_ext', 'wygwam_before_display', 'wygwam_before_display', 'a:0:{}', 100, '6.0.1', 'y'),
(9, 'Channel_images_ext', 'wygwam_before_save', 'wygwam_before_save', 'a:0:{}', 100, '6.0.1', 'y'),
(10, 'Channel_images_ext', 'wygwam_before_replace', 'wygwam_before_replace', 'a:0:{}', 100, '6.0.1', 'y'),
(11, 'Channel_images_ext', 'editor_before_display', 'editor_before_display', 'a:0:{}', 100, '6.0.1', 'y'),
(12, 'Channel_images_ext', 'editor_before_save', 'editor_before_save', 'a:0:{}', 100, '6.0.1', 'y'),
(13, 'Channel_images_ext', 'editor_before_replace', 'editor_before_replace', 'a:0:{}', 100, '6.0.1', 'y');

-- --------------------------------------------------------

--
-- Table structure for table `exp_fieldtypes`
--

CREATE TABLE `exp_fieldtypes` (
`fieldtype_id` int(4) unsigned NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `version` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `settings` text COLLATE utf8_unicode_ci,
  `has_global_settings` char(1) COLLATE utf8_unicode_ci DEFAULT 'n'
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_fieldtypes`
--

INSERT INTO `exp_fieldtypes` (`fieldtype_id`, `name`, `version`, `settings`, `has_global_settings`) VALUES
(1, 'select', '1.0.0', 'YTowOnt9', 'n'),
(2, 'text', '1.0.0', 'YTowOnt9', 'n'),
(3, 'textarea', '1.0.0', 'YTowOnt9', 'n'),
(4, 'date', '1.0.0', 'YTowOnt9', 'n'),
(5, 'file', '1.0.0', 'YTowOnt9', 'n'),
(6, 'grid', '1.0.0', 'YTowOnt9', 'n'),
(7, 'multi_select', '1.0.0', 'YTowOnt9', 'n'),
(8, 'checkboxes', '1.0.0', 'YTowOnt9', 'n'),
(9, 'radio', '1.0.0', 'YTowOnt9', 'n'),
(10, 'relationship', '1.0.0', 'YTowOnt9', 'n'),
(11, 'rte', '1.0.1', 'YTowOnt9', 'n'),
(12, 'channel_images', '6.0.1', 'YTowOnt9', 'n'),
(13, 'editor', '4.0.1', 'YTowOnt9', 'n');

-- --------------------------------------------------------

--
-- Table structure for table `exp_field_groups`
--

CREATE TABLE `exp_field_groups` (
`group_id` int(4) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_field_groups`
--

INSERT INTO `exp_field_groups` (`group_id`, `site_id`, `group_name`) VALUES
(1, 1, 'Brokerage Yacht');

-- --------------------------------------------------------

--
-- Table structure for table `exp_files`
--

CREATE TABLE `exp_files` (
`file_id` int(10) unsigned NOT NULL,
  `site_id` int(4) unsigned DEFAULT '1',
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `upload_location_id` int(4) unsigned DEFAULT '0',
  `mime_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_size` int(10) DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `credit` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uploaded_by_member_id` int(10) unsigned DEFAULT '0',
  `upload_date` int(10) DEFAULT NULL,
  `modified_by_member_id` int(10) unsigned DEFAULT '0',
  `modified_date` int(10) DEFAULT NULL,
  `file_hw_original` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_files`
--

INSERT INTO `exp_files` (`file_id`, `site_id`, `title`, `upload_location_id`, `mime_type`, `file_name`, `file_size`, `description`, `credit`, `location`, `uploaded_by_member_id`, `upload_date`, `modified_by_member_id`, `modified_date`, `file_hw_original`) VALUES
(1, 1, 'feature1.jpg', 6, 'image/jpeg', 'feature1.jpg', 51036, '', '', '', 1, 1456329145, 1, 1456329145, '246 429'),
(2, 1, 'feature2.jpg', 6, 'image/jpeg', 'feature2.jpg', 61338, '', '', '', 1, 1456329166, 1, 1456329166, '246 429'),
(3, 1, 'feature3.jpg', 6, 'image/jpeg', 'feature3.jpg', 62628, '', '', '', 1, 1456329185, 1, 1456329185, '246 428');

-- --------------------------------------------------------

--
-- Table structure for table `exp_file_categories`
--

CREATE TABLE `exp_file_categories` (
  `file_id` int(10) unsigned DEFAULT NULL,
  `cat_id` int(10) unsigned DEFAULT NULL,
  `sort` int(10) unsigned DEFAULT '0',
  `is_cover` char(1) COLLATE utf8_unicode_ci DEFAULT 'n'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_file_dimensions`
--

CREATE TABLE `exp_file_dimensions` (
`id` int(10) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `upload_location_id` int(4) unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `short_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `resize_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
  `width` int(10) DEFAULT '0',
  `height` int(10) DEFAULT '0',
  `watermark_id` int(4) unsigned DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_file_watermarks`
--

CREATE TABLE `exp_file_watermarks` (
`wm_id` int(4) unsigned NOT NULL,
  `wm_name` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wm_type` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'text',
  `wm_image_path` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wm_test_image_path` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wm_use_font` char(1) COLLATE utf8_unicode_ci DEFAULT 'y',
  `wm_font` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wm_font_size` int(3) unsigned DEFAULT NULL,
  `wm_text` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wm_vrt_alignment` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'top',
  `wm_hor_alignment` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'left',
  `wm_padding` int(3) unsigned DEFAULT NULL,
  `wm_opacity` int(3) unsigned DEFAULT NULL,
  `wm_hor_offset` int(4) unsigned DEFAULT NULL,
  `wm_vrt_offset` int(4) unsigned DEFAULT NULL,
  `wm_x_transp` int(4) DEFAULT NULL,
  `wm_y_transp` int(4) DEFAULT NULL,
  `wm_font_color` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wm_use_drop_shadow` char(1) COLLATE utf8_unicode_ci DEFAULT 'y',
  `wm_shadow_distance` int(3) unsigned DEFAULT NULL,
  `wm_shadow_color` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_global_variables`
--

CREATE TABLE `exp_global_variables` (
`variable_id` int(6) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `variable_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `variable_data` text COLLATE utf8_unicode_ci NOT NULL,
  `edit_date` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_grid_columns`
--

CREATE TABLE `exp_grid_columns` (
`col_id` int(10) unsigned NOT NULL,
  `field_id` int(10) unsigned DEFAULT NULL,
  `content_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `col_order` int(3) unsigned DEFAULT NULL,
  `col_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `col_label` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `col_name` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `col_instructions` text COLLATE utf8_unicode_ci,
  `col_required` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `col_search` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `col_width` int(3) unsigned DEFAULT NULL,
  `col_settings` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_grid_columns`
--

INSERT INTO `exp_grid_columns` (`col_id`, `field_id`, `content_type`, `col_order`, `col_type`, `col_label`, `col_name`, `col_instructions`, `col_required`, `col_search`, `col_width`, `col_settings`) VALUES
(1, 13, 'channel', 0, 'text', 'Title', 'feature_title', '', 'n', 'n', 0, '{"field_maxl":"256","field_fmt":"none","field_text_direction":"ltr","field_content_type":"all","field_required":"n"}'),
(2, 13, 'channel', 1, 'editor', 'Description', 'feature_description', '', 'n', 'n', 0, '{"editor":{"config":"1"},"field_wide":false,"field_required":"n"}'),
(3, 13, 'channel', 2, 'file', 'Image', 'feature_image', '', 'n', 'n', 0, '{"field_content_type":"image","allowed_directories":"6","show_existing":"y","num_existing":"50","field_fmt":"none","field_required":"n"}');

-- --------------------------------------------------------

--
-- Table structure for table `exp_html_buttons`
--

CREATE TABLE `exp_html_buttons` (
`id` int(10) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `member_id` int(10) NOT NULL DEFAULT '0',
  `tag_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `tag_open` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `tag_close` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `accesskey` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `tag_order` int(3) unsigned NOT NULL,
  `tag_row` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `classname` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_html_buttons`
--

INSERT INTO `exp_html_buttons` (`id`, `site_id`, `member_id`, `tag_name`, `tag_open`, `tag_close`, `accesskey`, `tag_order`, `tag_row`, `classname`) VALUES
(1, 1, 0, 'html_btn_bold', '<strong>', '</strong>', 'b', 1, '1', 'html-bold'),
(2, 1, 0, 'html_btn_italic', '<em>', '</em>', 'i', 2, '1', 'html-italic'),
(3, 1, 0, 'html_btn_blockquote', '<blockquote>', '</blockquote>', 'q', 3, '1', 'html-quote'),
(4, 1, 0, 'html_btn_anchor', '<a href="[![Link:!:http://]!]"(!( title="[![Title]!]")!)>', '</a>', 'a', 4, '1', 'html-link'),
(5, 1, 0, 'html_btn_picture', '<img src="[![Link:!:http://]!]" alt="[![Alternative text]!]" />', '', '', 5, '1', 'html-upload');

-- --------------------------------------------------------

--
-- Table structure for table `exp_layout_publish`
--

CREATE TABLE `exp_layout_publish` (
`layout_id` int(10) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `channel_id` int(4) unsigned NOT NULL DEFAULT '0',
  `layout_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `field_layout` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_layout_publish`
--

INSERT INTO `exp_layout_publish` (`layout_id`, `site_id`, `channel_id`, `layout_name`, `field_layout`) VALUES
(1, 1, 1, 'Brokerage', 'a:4:{i:0;a:4:{s:2:"id";s:7:"publish";s:4:"name";s:7:"publish";s:7:"visible";b:1;s:6:"fields";a:22:{i:0;a:3:{s:5:"field";s:5:"title";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:1;a:3:{s:5:"field";s:9:"url_title";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:2;a:3:{s:5:"field";s:10:"field_id_1";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:3;a:3:{s:5:"field";s:11:"field_id_17";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:4;a:3:{s:5:"field";s:10:"field_id_2";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:5;a:3:{s:5:"field";s:10:"field_id_3";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:6;a:3:{s:5:"field";s:10:"field_id_4";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:7;a:3:{s:5:"field";s:10:"field_id_5";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:8;a:3:{s:5:"field";s:10:"field_id_6";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:9;a:3:{s:5:"field";s:10:"field_id_7";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:10;a:3:{s:5:"field";s:10:"field_id_8";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:11;a:3:{s:5:"field";s:10:"field_id_9";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:12;a:3:{s:5:"field";s:11:"field_id_10";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:13;a:3:{s:5:"field";s:11:"field_id_18";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:14;a:3:{s:5:"field";s:11:"field_id_19";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:15;a:3:{s:5:"field";s:11:"field_id_11";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:16;a:3:{s:5:"field";s:11:"field_id_12";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:17;a:3:{s:5:"field";s:11:"field_id_13";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:18;a:3:{s:5:"field";s:11:"field_id_14";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:19;a:3:{s:5:"field";s:11:"field_id_15";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:20;a:3:{s:5:"field";s:11:"field_id_20";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:21;a:3:{s:5:"field";s:11:"field_id_16";s:7:"visible";b:1;s:9:"collapsed";b:0;}}}i:1;a:4:{s:2:"id";s:4:"date";s:4:"name";s:4:"date";s:7:"visible";b:1;s:6:"fields";a:3:{i:0;a:3:{s:5:"field";s:10:"entry_date";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:1;a:3:{s:5:"field";s:15:"expiration_date";s:7:"visible";b:0;s:9:"collapsed";b:0;}i:2;a:3:{s:5:"field";s:23:"comment_expiration_date";s:7:"visible";b:0;s:9:"collapsed";b:0;}}}i:2;a:4:{s:2:"id";s:10:"categories";s:4:"name";s:10:"categories";s:7:"visible";b:0;s:6:"fields";a:0:{}}i:3;a:4:{s:2:"id";s:7:"options";s:4:"name";s:7:"options";s:7:"visible";b:1;s:6:"fields";a:5:{i:0;a:3:{s:5:"field";s:10:"channel_id";s:7:"visible";b:0;s:9:"collapsed";b:0;}i:1;a:3:{s:5:"field";s:6:"status";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:2;a:3:{s:5:"field";s:9:"author_id";s:7:"visible";b:1;s:9:"collapsed";b:0;}i:3;a:3:{s:5:"field";s:6:"sticky";s:7:"visible";b:0;s:9:"collapsed";b:0;}i:4;a:3:{s:5:"field";s:14:"allow_comments";s:7:"visible";b:0;s:9:"collapsed";b:0;}}}}');

-- --------------------------------------------------------

--
-- Table structure for table `exp_layout_publish_member_groups`
--

CREATE TABLE `exp_layout_publish_member_groups` (
  `layout_id` int(10) unsigned NOT NULL,
  `group_id` int(4) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_layout_publish_member_groups`
--

INSERT INTO `exp_layout_publish_member_groups` (`layout_id`, `group_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `exp_members`
--

CREATE TABLE `exp_members` (
`member_id` int(10) unsigned NOT NULL,
  `group_id` smallint(4) NOT NULL DEFAULT '0',
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `screen_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `unique_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `crypt_key` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `authcode` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `occupation` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `interests` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bday_d` int(2) DEFAULT NULL,
  `bday_m` int(2) DEFAULT NULL,
  `bday_y` int(4) DEFAULT NULL,
  `aol_im` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `yahoo_im` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `msn_im` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `icq` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8_unicode_ci,
  `signature` text COLLATE utf8_unicode_ci,
  `avatar_filename` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar_width` int(4) unsigned DEFAULT NULL,
  `avatar_height` int(4) unsigned DEFAULT NULL,
  `photo_filename` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photo_width` int(4) unsigned DEFAULT NULL,
  `photo_height` int(4) unsigned DEFAULT NULL,
  `sig_img_filename` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sig_img_width` int(4) unsigned DEFAULT NULL,
  `sig_img_height` int(4) unsigned DEFAULT NULL,
  `ignore_list` text COLLATE utf8_unicode_ci,
  `private_messages` int(4) unsigned NOT NULL DEFAULT '0',
  `accept_messages` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `last_view_bulletins` int(10) NOT NULL DEFAULT '0',
  `last_bulletin_date` int(10) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `join_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_visit` int(10) unsigned NOT NULL DEFAULT '0',
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `total_entries` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `total_comments` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `total_forum_topics` mediumint(8) NOT NULL DEFAULT '0',
  `total_forum_posts` mediumint(8) NOT NULL DEFAULT '0',
  `last_entry_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_comment_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_forum_post_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_email_date` int(10) unsigned NOT NULL DEFAULT '0',
  `in_authorlist` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `accept_admin_email` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `accept_user_email` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `notify_by_default` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `notify_of_pm` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `display_avatars` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `display_signatures` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `parse_smileys` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `smart_notifications` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `language` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `timezone` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `time_format` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '12',
  `date_format` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '%n/%j/%Y',
  `include_seconds` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `profile_theme` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `forum_theme` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tracker` text COLLATE utf8_unicode_ci,
  `template_size` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '28',
  `notepad` text COLLATE utf8_unicode_ci,
  `notepad_size` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '18',
  `bookmarklets` text COLLATE utf8_unicode_ci,
  `quick_links` text COLLATE utf8_unicode_ci,
  `quick_tabs` text COLLATE utf8_unicode_ci,
  `show_sidebar` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `pmember_id` int(10) NOT NULL DEFAULT '0',
  `rte_enabled` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `rte_toolset_id` int(10) NOT NULL DEFAULT '0',
  `cp_homepage` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cp_homepage_channel` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cp_homepage_custom` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_members`
--

INSERT INTO `exp_members` (`member_id`, `group_id`, `username`, `screen_name`, `password`, `salt`, `unique_id`, `crypt_key`, `authcode`, `email`, `url`, `location`, `occupation`, `interests`, `bday_d`, `bday_m`, `bday_y`, `aol_im`, `yahoo_im`, `msn_im`, `icq`, `bio`, `signature`, `avatar_filename`, `avatar_width`, `avatar_height`, `photo_filename`, `photo_width`, `photo_height`, `sig_img_filename`, `sig_img_width`, `sig_img_height`, `ignore_list`, `private_messages`, `accept_messages`, `last_view_bulletins`, `last_bulletin_date`, `ip_address`, `join_date`, `last_visit`, `last_activity`, `total_entries`, `total_comments`, `total_forum_topics`, `total_forum_posts`, `last_entry_date`, `last_comment_date`, `last_forum_post_date`, `last_email_date`, `in_authorlist`, `accept_admin_email`, `accept_user_email`, `notify_by_default`, `notify_of_pm`, `display_avatars`, `display_signatures`, `parse_smileys`, `smart_notifications`, `language`, `timezone`, `time_format`, `date_format`, `include_seconds`, `profile_theme`, `forum_theme`, `tracker`, `template_size`, `notepad`, `notepad_size`, `bookmarklets`, `quick_links`, `quick_tabs`, `show_sidebar`, `pmember_id`, `rte_enabled`, `rte_toolset_id`, `cp_homepage`, `cp_homepage_channel`, `cp_homepage_custom`) VALUES
(1, 1, 'Chris', 'Chris', 'ac40d5de1881ff2c883bb27c17bedb8bf6153400fb2628dfd97c60b7d4f470d79e46e6eb15c7bdd7639ed458d7378e32202bab25cf18988b5f7abeb1bd1f4a21', ':?qdsomg]?R<5}c7+A@1.-}A<d0N1"Vh|i5Onvwff9rp%x@1"nWt$k/QsLs5vsyCXeXu!y?#[EkwE~FJ=PiKJ7GhPiKMR`6l=$kl#Q:}_hoVXL.[PY=vrIoqne_6C74J', '40c7c2cbb33d06b4b2f8f833b2082786a195130c', '3b222d893fe2b90962ff07ad3f918f157f9b758e', NULL, 'macdochris@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'y', 0, 0, '127.0.0.1', 1456308576, 1456336634, 1456397602, 0, 0, 0, 0, 0, 0, 0, 0, 'n', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'english', 'Europe/Berlin', '12', '%n/%j/%Y', 'n', NULL, NULL, NULL, '28', NULL, '18', NULL, '', NULL, 'n', 0, 'y', 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exp_member_bulletin_board`
--

CREATE TABLE `exp_member_bulletin_board` (
`bulletin_id` int(10) unsigned NOT NULL,
  `sender_id` int(10) unsigned NOT NULL,
  `bulletin_group` int(8) unsigned NOT NULL,
  `bulletin_date` int(10) unsigned NOT NULL,
  `hash` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `bulletin_expires` int(10) unsigned NOT NULL DEFAULT '0',
  `bulletin_message` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_member_data`
--

CREATE TABLE `exp_member_data` (
  `member_id` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_member_data`
--

INSERT INTO `exp_member_data` (`member_id`) VALUES
(1);

-- --------------------------------------------------------

--
-- Table structure for table `exp_member_fields`
--

CREATE TABLE `exp_member_fields` (
`m_field_id` int(4) unsigned NOT NULL,
  `m_field_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `m_field_label` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `m_field_description` text COLLATE utf8_unicode_ci NOT NULL,
  `m_field_type` varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  `m_field_list_items` text COLLATE utf8_unicode_ci NOT NULL,
  `m_field_ta_rows` tinyint(2) DEFAULT '8',
  `m_field_maxl` smallint(3) DEFAULT NULL,
  `m_field_width` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `m_field_search` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `m_field_required` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `m_field_public` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `m_field_reg` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `m_field_cp_reg` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `m_field_fmt` char(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  `m_field_show_fmt` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `m_field_order` int(3) unsigned DEFAULT NULL,
  `m_field_text_direction` char(3) COLLATE utf8_unicode_ci DEFAULT 'ltr'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_member_groups`
--

CREATE TABLE `exp_member_groups` (
  `group_id` smallint(4) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `group_description` text COLLATE utf8_unicode_ci NOT NULL,
  `is_locked` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `can_view_offline_system` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_view_online_system` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `can_access_cp` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `can_access_footer_report_bug` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_access_footer_new_ticket` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_access_footer_user_guide` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_access_files` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_access_design` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_access_addons` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_access_members` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_access_sys_prefs` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_access_comm` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_access_utilities` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_access_data` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_access_logs` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_admin_channels` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_admin_design` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_members` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_admin_mbr_groups` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_admin_mbr_templates` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_ban_users` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_admin_addons` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_categories` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_categories` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_view_other_entries` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_other_entries` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_assign_post_authors` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_self_entries` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_all_entries` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_view_other_comments` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_own_comments` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_own_comments` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_all_comments` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_all_comments` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_moderate_comments` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_send_cached_email` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_email_member_groups` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_email_from_profile` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_view_profiles` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_html_buttons` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_self` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `mbr_delete_notify_emails` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `can_post_comments` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `exclude_from_moderation` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_search` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `search_flood_control` mediumint(5) unsigned NOT NULL,
  `can_send_private_messages` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `prv_msg_send_limit` smallint(5) unsigned NOT NULL DEFAULT '20',
  `prv_msg_storage_limit` smallint(5) unsigned NOT NULL DEFAULT '60',
  `can_attach_in_private_messages` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_send_bulletins` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `include_in_authorlist` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `include_in_memberlist` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `cp_homepage` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cp_homepage_channel` int(10) unsigned NOT NULL DEFAULT '0',
  `cp_homepage_custom` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `can_create_entries` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_self_entries` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_upload_new_files` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_files` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_files` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_upload_new_toolsets` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_toolsets` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_toolsets` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_create_upload_directories` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_upload_directories` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_upload_directories` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_create_channels` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_channels` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_channels` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_create_channel_fields` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_channel_fields` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_channel_fields` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_create_statuses` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_statuses` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_statuses` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_create_categories` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_create_member_groups` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_member_groups` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_member_groups` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_create_members` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_members` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_create_new_templates` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_templates` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_templates` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_create_template_groups` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_template_groups` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_template_groups` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_create_template_partials` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_template_partials` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_template_partials` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_create_template_variables` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_delete_template_variables` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_edit_template_variables` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_access_security_settings` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_access_translate` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_access_import` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `can_access_sql_manager` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_member_groups`
--

INSERT INTO `exp_member_groups` (`group_id`, `site_id`, `group_title`, `group_description`, `is_locked`, `can_view_offline_system`, `can_view_online_system`, `can_access_cp`, `can_access_footer_report_bug`, `can_access_footer_new_ticket`, `can_access_footer_user_guide`, `can_access_files`, `can_access_design`, `can_access_addons`, `can_access_members`, `can_access_sys_prefs`, `can_access_comm`, `can_access_utilities`, `can_access_data`, `can_access_logs`, `can_admin_channels`, `can_admin_design`, `can_delete_members`, `can_admin_mbr_groups`, `can_admin_mbr_templates`, `can_ban_users`, `can_admin_addons`, `can_edit_categories`, `can_delete_categories`, `can_view_other_entries`, `can_edit_other_entries`, `can_assign_post_authors`, `can_delete_self_entries`, `can_delete_all_entries`, `can_view_other_comments`, `can_edit_own_comments`, `can_delete_own_comments`, `can_edit_all_comments`, `can_delete_all_comments`, `can_moderate_comments`, `can_send_cached_email`, `can_email_member_groups`, `can_email_from_profile`, `can_view_profiles`, `can_edit_html_buttons`, `can_delete_self`, `mbr_delete_notify_emails`, `can_post_comments`, `exclude_from_moderation`, `can_search`, `search_flood_control`, `can_send_private_messages`, `prv_msg_send_limit`, `prv_msg_storage_limit`, `can_attach_in_private_messages`, `can_send_bulletins`, `include_in_authorlist`, `include_in_memberlist`, `cp_homepage`, `cp_homepage_channel`, `cp_homepage_custom`, `can_create_entries`, `can_edit_self_entries`, `can_upload_new_files`, `can_edit_files`, `can_delete_files`, `can_upload_new_toolsets`, `can_edit_toolsets`, `can_delete_toolsets`, `can_create_upload_directories`, `can_edit_upload_directories`, `can_delete_upload_directories`, `can_create_channels`, `can_edit_channels`, `can_delete_channels`, `can_create_channel_fields`, `can_edit_channel_fields`, `can_delete_channel_fields`, `can_create_statuses`, `can_delete_statuses`, `can_edit_statuses`, `can_create_categories`, `can_create_member_groups`, `can_delete_member_groups`, `can_edit_member_groups`, `can_create_members`, `can_edit_members`, `can_create_new_templates`, `can_edit_templates`, `can_delete_templates`, `can_create_template_groups`, `can_edit_template_groups`, `can_delete_template_groups`, `can_create_template_partials`, `can_edit_template_partials`, `can_delete_template_partials`, `can_create_template_variables`, `can_delete_template_variables`, `can_edit_template_variables`, `can_access_security_settings`, `can_access_translate`, `can_access_import`, `can_access_sql_manager`) VALUES
(1, 1, 'Super Admin', '', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', NULL, 'n', 'y', 'n', 0, 'y', 20, 60, 'y', 'y', 'y', 'y', NULL, 0, NULL, 'n', 'n', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'n', 'n', 'n', 'n'),
(2, 1, 'Banned', '', 'y', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', NULL, 'n', 'n', 'n', 60, 'n', 20, 60, 'n', 'n', 'n', 'n', NULL, 0, NULL, 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n'),
(3, 1, 'Guests', '', 'y', 'n', 'y', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', NULL, 'n', 'n', 'n', 10, 'n', 20, 60, 'n', 'n', 'n', 'y', NULL, 0, NULL, 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n'),
(4, 1, 'Pending', '', 'y', 'n', 'y', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', NULL, 'n', 'n', 'n', 10, 'n', 20, 60, 'n', 'n', 'n', 'y', NULL, 0, NULL, 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n'),
(5, 1, 'Members', '', 'y', 'n', 'y', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'y', 'y', 'y', 'y', NULL, 'n', 'n', 'n', 10, 'y', 20, 60, 'y', 'n', 'n', 'y', NULL, 0, NULL, 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n');

-- --------------------------------------------------------

--
-- Table structure for table `exp_member_homepage`
--

CREATE TABLE `exp_member_homepage` (
  `member_id` int(10) unsigned NOT NULL,
  `recent_entries` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'l',
  `recent_entries_order` int(3) unsigned NOT NULL DEFAULT '0',
  `recent_comments` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'l',
  `recent_comments_order` int(3) unsigned NOT NULL DEFAULT '0',
  `recent_members` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `recent_members_order` int(3) unsigned NOT NULL DEFAULT '0',
  `site_statistics` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'r',
  `site_statistics_order` int(3) unsigned NOT NULL DEFAULT '0',
  `member_search_form` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `member_search_form_order` int(3) unsigned NOT NULL DEFAULT '0',
  `notepad` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'r',
  `notepad_order` int(3) unsigned NOT NULL DEFAULT '0',
  `bulletin_board` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'r',
  `bulletin_board_order` int(3) unsigned NOT NULL DEFAULT '0',
  `pmachine_news_feed` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `pmachine_news_feed_order` int(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_member_homepage`
--

INSERT INTO `exp_member_homepage` (`member_id`, `recent_entries`, `recent_entries_order`, `recent_comments`, `recent_comments_order`, `recent_members`, `recent_members_order`, `site_statistics`, `site_statistics_order`, `member_search_form`, `member_search_form_order`, `notepad`, `notepad_order`, `bulletin_board`, `bulletin_board_order`, `pmachine_news_feed`, `pmachine_news_feed_order`) VALUES
(1, 'l', 1, 'l', 2, 'n', 0, 'r', 1, 'n', 0, 'r', 2, 'r', 0, 'l', 0);

-- --------------------------------------------------------

--
-- Table structure for table `exp_member_search`
--

CREATE TABLE `exp_member_search` (
  `search_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `search_date` int(10) unsigned NOT NULL,
  `keywords` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `fields` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `member_id` int(10) unsigned NOT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `total_results` int(8) unsigned NOT NULL,
  `query` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_message_attachments`
--

CREATE TABLE `exp_message_attachments` (
`attachment_id` int(10) unsigned NOT NULL,
  `sender_id` int(10) unsigned NOT NULL DEFAULT '0',
  `message_id` int(10) unsigned NOT NULL DEFAULT '0',
  `attachment_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `attachment_hash` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `attachment_extension` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `attachment_location` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `attachment_date` int(10) unsigned NOT NULL DEFAULT '0',
  `attachment_size` int(10) unsigned NOT NULL DEFAULT '0',
  `is_temp` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_message_copies`
--

CREATE TABLE `exp_message_copies` (
`copy_id` int(10) unsigned NOT NULL,
  `message_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sender_id` int(10) unsigned NOT NULL DEFAULT '0',
  `recipient_id` int(10) unsigned NOT NULL DEFAULT '0',
  `message_received` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `message_read` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `message_time_read` int(10) unsigned NOT NULL DEFAULT '0',
  `attachment_downloaded` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `message_folder` int(10) unsigned NOT NULL DEFAULT '1',
  `message_authcode` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `message_deleted` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `message_status` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_message_data`
--

CREATE TABLE `exp_message_data` (
`message_id` int(10) unsigned NOT NULL,
  `sender_id` int(10) unsigned NOT NULL DEFAULT '0',
  `message_date` int(10) unsigned NOT NULL DEFAULT '0',
  `message_subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `message_body` text COLLATE utf8_unicode_ci NOT NULL,
  `message_tracking` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `message_attachments` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `message_recipients` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `message_cc` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `message_hide_cc` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `message_sent_copy` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `total_recipients` int(5) unsigned NOT NULL DEFAULT '0',
  `message_status` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_message_folders`
--

CREATE TABLE `exp_message_folders` (
  `member_id` int(10) unsigned NOT NULL DEFAULT '0',
  `folder1_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'InBox',
  `folder2_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Sent',
  `folder3_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `folder4_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `folder5_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `folder6_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `folder7_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `folder8_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `folder9_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `folder10_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_message_listed`
--

CREATE TABLE `exp_message_listed` (
`listed_id` int(10) unsigned NOT NULL,
  `member_id` int(10) unsigned NOT NULL DEFAULT '0',
  `listed_member` int(10) unsigned NOT NULL DEFAULT '0',
  `listed_description` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `listed_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'blocked'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_modules`
--

CREATE TABLE `exp_modules` (
`module_id` int(4) unsigned NOT NULL,
  `module_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `module_version` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `has_cp_backend` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `has_publish_fields` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `settings` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_modules`
--

INSERT INTO `exp_modules` (`module_id`, `module_name`, `module_version`, `has_cp_backend`, `has_publish_fields`, `settings`) VALUES
(1, 'Channel', '2.0.1', 'n', 'n', NULL),
(2, 'Comment', '2.3.2', 'y', 'n', NULL),
(3, 'Member', '2.1', 'n', 'n', NULL),
(4, 'Stats', '2.0', 'n', 'n', NULL),
(5, 'Rte', '1.0.1', 'y', 'n', NULL),
(6, 'File', '1.0.0', 'n', 'n', NULL),
(7, 'Filepicker', '1.0', 'y', 'n', NULL),
(8, 'Search', '2.2.2', 'n', 'n', NULL),
(9, 'Zenbu', '2.0.0', 'y', 'n', NULL),
(10, 'Channel_images', '6.0.1', 'y', 'n', NULL),
(11, 'Editor', '4.0.1', 'y', 'n', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exp_module_member_groups`
--

CREATE TABLE `exp_module_member_groups` (
  `group_id` smallint(4) unsigned NOT NULL,
  `module_id` mediumint(5) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_online_users`
--

CREATE TABLE `exp_online_users` (
`online_id` int(10) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `member_id` int(10) NOT NULL DEFAULT '0',
  `in_forum` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  `anon` char(1) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_online_users`
--

INSERT INTO `exp_online_users` (`online_id`, `site_id`, `member_id`, `in_forum`, `name`, `ip_address`, `date`, `anon`) VALUES
(8, 1, 1, 'n', 'Chris', '127.0.0.1', 1456397796, ''),
(9, 1, 1, 'n', 'Chris', '127.0.0.1', 1456397796, '');

-- --------------------------------------------------------

--
-- Table structure for table `exp_password_lockout`
--

CREATE TABLE `exp_password_lockout` (
`lockout_id` int(10) unsigned NOT NULL,
  `login_date` int(10) unsigned NOT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `user_agent` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_plugins`
--

CREATE TABLE `exp_plugins` (
`plugin_id` int(10) unsigned NOT NULL,
  `plugin_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `plugin_package` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `plugin_version` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `is_typography_related` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_relationships`
--

CREATE TABLE `exp_relationships` (
`relationship_id` int(6) unsigned NOT NULL,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `child_id` int(10) unsigned NOT NULL DEFAULT '0',
  `field_id` int(10) unsigned NOT NULL DEFAULT '0',
  `grid_field_id` int(10) unsigned NOT NULL DEFAULT '0',
  `grid_col_id` int(10) unsigned NOT NULL DEFAULT '0',
  `grid_row_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_remember_me`
--

CREATE TABLE `exp_remember_me` (
  `remember_me_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `member_id` int(10) DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8_unicode_ci DEFAULT '0',
  `user_agent` varchar(120) COLLATE utf8_unicode_ci DEFAULT '',
  `admin_sess` tinyint(1) DEFAULT '0',
  `site_id` int(4) DEFAULT '1',
  `expiration` int(10) DEFAULT '0',
  `last_refresh` int(10) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_remember_me`
--

INSERT INTO `exp_remember_me` (`remember_me_id`, `member_id`, `ip_address`, `user_agent`, `admin_sess`, `site_id`, `expiration`, `last_refresh`) VALUES
('0f38d8884f41b3de5188f2fa0cf899e8ea9c1639', 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.109 Safari/537.3', 0, 1, 1457518216, 1456308616);

-- --------------------------------------------------------

--
-- Table structure for table `exp_reset_password`
--

CREATE TABLE `exp_reset_password` (
`reset_id` int(10) unsigned NOT NULL,
  `member_id` int(10) unsigned NOT NULL,
  `resetcode` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `date` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_revision_tracker`
--

CREATE TABLE `exp_revision_tracker` (
`tracker_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `item_table` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `item_field` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `item_date` int(10) NOT NULL,
  `item_author_id` int(10) unsigned NOT NULL,
  `item_data` mediumtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_rte_tools`
--

CREATE TABLE `exp_rte_tools` (
`tool_id` int(10) unsigned NOT NULL,
  `name` varchar(75) COLLATE utf8_unicode_ci DEFAULT NULL,
  `class` varchar(75) COLLATE utf8_unicode_ci DEFAULT NULL,
  `enabled` char(1) COLLATE utf8_unicode_ci DEFAULT 'y'
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_rte_tools`
--

INSERT INTO `exp_rte_tools` (`tool_id`, `name`, `class`, `enabled`) VALUES
(1, 'Blockquote', 'Blockquote_rte', 'y'),
(2, 'Bold', 'Bold_rte', 'y'),
(3, 'Headings', 'Headings_rte', 'y'),
(4, 'Image', 'Image_rte', 'y'),
(5, 'Italic', 'Italic_rte', 'y'),
(6, 'Link', 'Link_rte', 'y'),
(7, 'Ordered List', 'Ordered_list_rte', 'y'),
(8, 'Underline', 'Underline_rte', 'y'),
(9, 'Unordered List', 'Unordered_list_rte', 'y'),
(10, 'View Source', 'View_source_rte', 'y');

-- --------------------------------------------------------

--
-- Table structure for table `exp_rte_toolsets`
--

CREATE TABLE `exp_rte_toolsets` (
`toolset_id` int(10) unsigned NOT NULL,
  `member_id` int(10) DEFAULT '0',
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tools` text COLLATE utf8_unicode_ci,
  `enabled` char(1) COLLATE utf8_unicode_ci DEFAULT 'y'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_rte_toolsets`
--

INSERT INTO `exp_rte_toolsets` (`toolset_id`, `member_id`, `name`, `tools`, `enabled`) VALUES
(1, 0, 'Default', '3|2|5|1|9|7|6|4|10', 'y');

-- --------------------------------------------------------

--
-- Table structure for table `exp_search`
--

CREATE TABLE `exp_search` (
  `search_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `site_id` int(4) NOT NULL DEFAULT '1',
  `search_date` int(10) NOT NULL,
  `keywords` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `member_id` int(10) unsigned NOT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `total_results` int(6) NOT NULL,
  `per_page` tinyint(3) unsigned NOT NULL,
  `query` mediumtext COLLATE utf8_unicode_ci,
  `custom_fields` mediumtext COLLATE utf8_unicode_ci,
  `result_page` varchar(70) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_search_log`
--

CREATE TABLE `exp_search_log` (
`id` int(10) NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `member_id` int(10) unsigned NOT NULL,
  `screen_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `search_date` int(10) NOT NULL,
  `search_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `search_terms` varchar(200) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_security_hashes`
--

CREATE TABLE `exp_security_hashes` (
`hash_id` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL,
  `session_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `hash` varchar(40) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_security_hashes`
--

INSERT INTO `exp_security_hashes` (`hash_id`, `date`, `session_id`, `hash`) VALUES
(1, 1456308617, '31431f8b43cadfb2501378e23a0f232ebf4c80bb', 'd2dd0c5b36aa8e186a1d1523be6e3fbcf8ce2d88'),
(2, 1456393818, 'da05e420ee83d6f008604c41f51f7779f344e647', '449a133e16de02d7db74f20856f0e901a4f66a6f');

-- --------------------------------------------------------

--
-- Table structure for table `exp_sessions`
--

CREATE TABLE `exp_sessions` (
  `session_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `member_id` int(10) NOT NULL DEFAULT '0',
  `admin_sess` tinyint(1) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `user_agent` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `login_state` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fingerprint` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `sess_start` int(10) unsigned NOT NULL DEFAULT '0',
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_sessions`
--

INSERT INTO `exp_sessions` (`session_id`, `member_id`, `admin_sess`, `ip_address`, `user_agent`, `login_state`, `fingerprint`, `sess_start`, `last_activity`) VALUES
('da05e420ee83d6f008604c41f51f7779f344e647', 1, 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.3', NULL, '0d99e343f91c6e967d9e7c6cebc2de4c', 1456397730, 1456397796);

-- --------------------------------------------------------

--
-- Table structure for table `exp_sites`
--

CREATE TABLE `exp_sites` (
`site_id` int(5) unsigned NOT NULL,
  `site_label` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `site_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `site_description` text COLLATE utf8_unicode_ci,
  `site_system_preferences` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `site_member_preferences` text COLLATE utf8_unicode_ci NOT NULL,
  `site_template_preferences` text COLLATE utf8_unicode_ci NOT NULL,
  `site_channel_preferences` text COLLATE utf8_unicode_ci NOT NULL,
  `site_bootstrap_checksums` text COLLATE utf8_unicode_ci NOT NULL,
  `site_pages` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_sites`
--

INSERT INTO `exp_sites` (`site_id`, `site_label`, `site_name`, `site_description`, `site_system_preferences`, `site_member_preferences`, `site_template_preferences`, `site_channel_preferences`, `site_bootstrap_checksums`, `site_pages`) VALUES
(1, 'Oyster Yachts', 'oyster-yachts', NULL, 'YTo4Nzp7czoxMDoiaXNfc2l0ZV9vbiI7czoxOiJ5IjtzOjEwOiJzaXRlX2luZGV4IjtzOjA6IiI7czo4OiJzaXRlX3VybCI7czoyMDoiaHR0cDovL295c3Rlci5sb2NhbC8iO3M6NjoiY3BfdXJsIjtzOjI5OiJodHRwOi8vb3lzdGVyLmxvY2FsL2FkbWluLnBocCI7czoxNjoidGhlbWVfZm9sZGVyX3VybCI7czoyNzoiaHR0cDovL295c3Rlci5sb2NhbC90aGVtZXMvIjtzOjE3OiJ0aGVtZV9mb2xkZXJfcGF0aCI7czo0MDoiL0FwcGxpY2F0aW9ucy9NQU1QL2h0ZG9jcy9veXN0ZXIvdGhlbWVzLyI7czoxNToid2VibWFzdGVyX2VtYWlsIjtzOjIwOiJtYWNkb2NocmlzQGdtYWlsLmNvbSI7czoxNDoid2VibWFzdGVyX25hbWUiO3M6MDoiIjtzOjIwOiJjaGFubmVsX25vbWVuY2xhdHVyZSI7czo3OiJjaGFubmVsIjtzOjEwOiJtYXhfY2FjaGVzIjtzOjM6IjE1MCI7czoxMToiY2FwdGNoYV91cmwiO3M6MzY6Imh0dHA6Ly9veXN0ZXIubG9jYWwvaW1hZ2VzL2NhcHRjaGFzLyI7czoxMjoiY2FwdGNoYV9wYXRoIjtzOjQ5OiIvQXBwbGljYXRpb25zL01BTVAvaHRkb2NzL295c3Rlci9pbWFnZXMvY2FwdGNoYXMvIjtzOjEyOiJjYXB0Y2hhX2ZvbnQiO3M6MToieSI7czoxMjoiY2FwdGNoYV9yYW5kIjtzOjE6InkiO3M6MjM6ImNhcHRjaGFfcmVxdWlyZV9tZW1iZXJzIjtzOjE6Im4iO3M6MTU6InJlcXVpcmVfY2FwdGNoYSI7czoxOiJuIjtzOjE4OiJlbmFibGVfc3FsX2NhY2hpbmciO3M6MToibiI7czoxODoiZm9yY2VfcXVlcnlfc3RyaW5nIjtzOjE6Im4iO3M6MTM6InNob3dfcHJvZmlsZXIiO3M6MToibiI7czoxNToiaW5jbHVkZV9zZWNvbmRzIjtzOjE6Im4iO3M6MTM6ImNvb2tpZV9kb21haW4iO3M6MDoiIjtzOjExOiJjb29raWVfcGF0aCI7czowOiIiO3M6MTU6ImNvb2tpZV9odHRwb25seSI7TjtzOjEzOiJjb29raWVfc2VjdXJlIjtOO3M6MjA6IndlYnNpdGVfc2Vzc2lvbl90eXBlIjtzOjE6ImMiO3M6MTU6ImNwX3Nlc3Npb25fdHlwZSI7czoxOiJjIjtzOjIxOiJhbGxvd191c2VybmFtZV9jaGFuZ2UiO3M6MToieSI7czoxODoiYWxsb3dfbXVsdGlfbG9naW5zIjtzOjE6InkiO3M6MTY6InBhc3N3b3JkX2xvY2tvdXQiO3M6MToieSI7czoyNToicGFzc3dvcmRfbG9ja291dF9pbnRlcnZhbCI7czoxOiIxIjtzOjIwOiJyZXF1aXJlX2lwX2Zvcl9sb2dpbiI7czoxOiJ5IjtzOjIyOiJyZXF1aXJlX2lwX2Zvcl9wb3N0aW5nIjtzOjE6InkiO3M6MjQ6InJlcXVpcmVfc2VjdXJlX3Bhc3N3b3JkcyI7czoxOiJuIjtzOjE5OiJhbGxvd19kaWN0aW9uYXJ5X3B3IjtzOjE6InkiO3M6MjM6Im5hbWVfb2ZfZGljdGlvbmFyeV9maWxlIjtzOjA6IiI7czoxNzoieHNzX2NsZWFuX3VwbG9hZHMiO3M6MToieSI7czoxNToicmVkaXJlY3RfbWV0aG9kIjtzOjg6InJlZGlyZWN0IjtzOjk6ImRlZnRfbGFuZyI7czo3OiJlbmdsaXNoIjtzOjg6InhtbF9sYW5nIjtzOjI6ImVuIjtzOjEyOiJzZW5kX2hlYWRlcnMiO3M6MToieSI7czoxMToiZ3ppcF9vdXRwdXQiO3M6MToibiI7czoyMToiZGVmYXVsdF9zaXRlX3RpbWV6b25lIjtzOjEzOiJFdXJvcGUvTG9uZG9uIjtzOjExOiJkYXRlX2Zvcm1hdCI7czo4OiIlai8lbi8lWSI7czoxMToidGltZV9mb3JtYXQiO3M6MjoiMjQiO3M6MTM6Im1haWxfcHJvdG9jb2wiO3M6NDoibWFpbCI7czoxMToic210cF9zZXJ2ZXIiO3M6MDoiIjtzOjk6InNtdHBfcG9ydCI7TjtzOjEzOiJzbXRwX3VzZXJuYW1lIjtzOjA6IiI7czoxMzoic210cF9wYXNzd29yZCI7czowOiIiO3M6MTE6ImVtYWlsX2RlYnVnIjtzOjE6Im4iO3M6MTM6ImVtYWlsX2NoYXJzZXQiO3M6NToidXRmLTgiO3M6MTU6ImVtYWlsX2JhdGNobW9kZSI7czoxOiJuIjtzOjE2OiJlbWFpbF9iYXRjaF9zaXplIjtzOjA6IiI7czoxMToibWFpbF9mb3JtYXQiO3M6NToicGxhaW4iO3M6OToid29yZF93cmFwIjtzOjE6InkiO3M6MjI6ImVtYWlsX2NvbnNvbGVfdGltZWxvY2siO3M6MToiNSI7czoyMjoibG9nX2VtYWlsX2NvbnNvbGVfbXNncyI7czoxOiJ5IjtzOjE2OiJsb2dfc2VhcmNoX3Rlcm1zIjtzOjE6InkiO3M6MTk6ImRlbnlfZHVwbGljYXRlX2RhdGEiO3M6MToieSI7czoyNDoicmVkaXJlY3Rfc3VibWl0dGVkX2xpbmtzIjtzOjE6Im4iO3M6MTY6ImVuYWJsZV9jZW5zb3JpbmciO3M6MToibiI7czoxNDoiY2Vuc29yZWRfd29yZHMiO3M6MDoiIjtzOjE4OiJjZW5zb3JfcmVwbGFjZW1lbnQiO3M6MDoiIjtzOjEwOiJiYW5uZWRfaXBzIjtzOjA6IiI7czoxMzoiYmFubmVkX2VtYWlscyI7czowOiIiO3M6MTY6ImJhbm5lZF91c2VybmFtZXMiO3M6MDoiIjtzOjE5OiJiYW5uZWRfc2NyZWVuX25hbWVzIjtzOjA6IiI7czoxMDoiYmFuX2FjdGlvbiI7czo4OiJyZXN0cmljdCI7czoxMToiYmFuX21lc3NhZ2UiO3M6MzQ6IlRoaXMgc2l0ZSBpcyBjdXJyZW50bHkgdW5hdmFpbGFibGUiO3M6MTU6ImJhbl9kZXN0aW5hdGlvbiI7czoyMToiaHR0cDovL3d3dy55YWhvby5jb20vIjtzOjE2OiJlbmFibGVfZW1vdGljb25zIjtzOjE6Im4iO3M6MTI6ImVtb3RpY29uX3VybCI7czozNToiaHR0cDovL295c3Rlci5sb2NhbC9pbWFnZXMvc21pbGV5cy8iO3M6MTk6InJlY291bnRfYmF0Y2hfdG90YWwiO3M6NDoiMTAwMCI7czoxNzoibmV3X3ZlcnNpb25fY2hlY2siO3M6MToieSI7czoxNzoiZW5hYmxlX3Rocm90dGxpbmciO3M6MToibiI7czoxNzoiYmFuaXNoX21hc2tlZF9pcHMiO3M6MToieSI7czoxNDoibWF4X3BhZ2VfbG9hZHMiO3M6MjoiMTAiO3M6MTM6InRpbWVfaW50ZXJ2YWwiO3M6MToiOCI7czoxMjoibG9ja291dF90aW1lIjtzOjI6IjMwIjtzOjE1OiJiYW5pc2htZW50X3R5cGUiO3M6NzoibWVzc2FnZSI7czoxNDoiYmFuaXNobWVudF91cmwiO3M6MDoiIjtzOjE4OiJiYW5pc2htZW50X21lc3NhZ2UiO3M6NTA6IllvdSBoYXZlIGV4Y2VlZGVkIHRoZSBhbGxvd2VkIHBhZ2UgbG9hZCBmcmVxdWVuY3kuIjtzOjE3OiJlbmFibGVfc2VhcmNoX2xvZyI7czoxOiJ5IjtzOjE5OiJtYXhfbG9nZ2VkX3NlYXJjaGVzIjtzOjM6IjUwMCI7czoxMToicnRlX2VuYWJsZWQiO3M6MToieSI7czoyMjoicnRlX2RlZmF1bHRfdG9vbHNldF9pZCI7czoxOiIxIjtzOjEzOiJmb3J1bV90cmlnZ2VyIjtOO30=', 'YTo0Nzp7czoxMDoidW5fbWluX2xlbiI7czoxOiI0IjtzOjEwOiJwd19taW5fbGVuIjtzOjE6IjUiO3M6MjU6ImFsbG93X21lbWJlcl9yZWdpc3RyYXRpb24iO3M6MToibiI7czoyNToiYWxsb3dfbWVtYmVyX2xvY2FsaXphdGlvbiI7czoxOiJ5IjtzOjE4OiJyZXFfbWJyX2FjdGl2YXRpb24iO3M6NToiZW1haWwiO3M6MjM6Im5ld19tZW1iZXJfbm90aWZpY2F0aW9uIjtzOjE6Im4iO3M6MjM6Im1icl9ub3RpZmljYXRpb25fZW1haWxzIjtzOjA6IiI7czoyNDoicmVxdWlyZV90ZXJtc19vZl9zZXJ2aWNlIjtzOjE6InkiO3M6MjA6ImRlZmF1bHRfbWVtYmVyX2dyb3VwIjtzOjE6IjUiO3M6MTU6InByb2ZpbGVfdHJpZ2dlciI7czo2OiJtZW1iZXIiO3M6MTI6Im1lbWJlcl90aGVtZSI7czo3OiJkZWZhdWx0IjtzOjE0OiJlbmFibGVfYXZhdGFycyI7czoxOiJuIjtzOjIwOiJhbGxvd19hdmF0YXJfdXBsb2FkcyI7czoxOiJuIjtzOjEwOiJhdmF0YXJfdXJsIjtzOjM1OiJodHRwOi8vb3lzdGVyLmxvY2FsL2ltYWdlcy9hdmF0YXJzLyI7czoxMToiYXZhdGFyX3BhdGgiO3M6NDg6Ii9BcHBsaWNhdGlvbnMvTUFNUC9odGRvY3Mvb3lzdGVyL2ltYWdlcy9hdmF0YXJzLyI7czoxNjoiYXZhdGFyX21heF93aWR0aCI7czozOiIxMDAiO3M6MTc6ImF2YXRhcl9tYXhfaGVpZ2h0IjtzOjM6IjEwMCI7czoxMzoiYXZhdGFyX21heF9rYiI7czoyOiI1MCI7czoxMzoiZW5hYmxlX3Bob3RvcyI7czoxOiJuIjtzOjk6InBob3RvX3VybCI7czo0MToiaHR0cDovL295c3Rlci5sb2NhbC9pbWFnZXMvbWVtYmVyX3Bob3Rvcy8iO3M6MTA6InBob3RvX3BhdGgiO3M6NTQ6Ii9BcHBsaWNhdGlvbnMvTUFNUC9odGRvY3Mvb3lzdGVyL2ltYWdlcy9tZW1iZXJfcGhvdG9zLyI7czoxNToicGhvdG9fbWF4X3dpZHRoIjtzOjM6IjEwMCI7czoxNjoicGhvdG9fbWF4X2hlaWdodCI7czozOiIxMDAiO3M6MTI6InBob3RvX21heF9rYiI7czoyOiI1MCI7czoxNjoiYWxsb3dfc2lnbmF0dXJlcyI7czoxOiJ5IjtzOjEzOiJzaWdfbWF4bGVuZ3RoIjtzOjM6IjUwMCI7czoyMToic2lnX2FsbG93X2ltZ19ob3RsaW5rIjtzOjE6Im4iO3M6MjA6InNpZ19hbGxvd19pbWdfdXBsb2FkIjtzOjE6Im4iO3M6MTE6InNpZ19pbWdfdXJsIjtzOjQ5OiJodHRwOi8vb3lzdGVyLmxvY2FsL2ltYWdlcy9zaWduYXR1cmVfYXR0YWNobWVudHMvIjtzOjEyOiJzaWdfaW1nX3BhdGgiO3M6NjI6Ii9BcHBsaWNhdGlvbnMvTUFNUC9odGRvY3Mvb3lzdGVyL2ltYWdlcy9zaWduYXR1cmVfYXR0YWNobWVudHMvIjtzOjE3OiJzaWdfaW1nX21heF93aWR0aCI7czozOiI0ODAiO3M6MTg6InNpZ19pbWdfbWF4X2hlaWdodCI7czoyOiI4MCI7czoxNDoic2lnX2ltZ19tYXhfa2IiO3M6MjoiMzAiO3M6MTU6InBydl9tc2dfZW5hYmxlZCI7czoxOiJ5IjtzOjI1OiJwcnZfbXNnX2FsbG93X2F0dGFjaG1lbnRzIjtzOjE6InkiO3M6MTk6InBydl9tc2dfdXBsb2FkX3BhdGgiO3M6NTU6Ii9BcHBsaWNhdGlvbnMvTUFNUC9odGRvY3Mvb3lzdGVyL2ltYWdlcy9wbV9hdHRhY2htZW50cy8iO3M6MjM6InBydl9tc2dfbWF4X2F0dGFjaG1lbnRzIjtzOjE6IjMiO3M6MjI6InBydl9tc2dfYXR0YWNoX21heHNpemUiO3M6MzoiMjUwIjtzOjIwOiJwcnZfbXNnX2F0dGFjaF90b3RhbCI7czozOiIxMDAiO3M6MTk6InBydl9tc2dfaHRtbF9mb3JtYXQiO3M6NDoic2FmZSI7czoxODoicHJ2X21zZ19hdXRvX2xpbmtzIjtzOjE6InkiO3M6MTc6InBydl9tc2dfbWF4X2NoYXJzIjtzOjQ6IjYwMDAiO3M6MTk6Im1lbWJlcmxpc3Rfb3JkZXJfYnkiO3M6MTE6InRvdGFsX3Bvc3RzIjtzOjIxOiJtZW1iZXJsaXN0X3NvcnRfb3JkZXIiO3M6NDoiZGVzYyI7czoyMDoibWVtYmVybGlzdF9yb3dfbGltaXQiO3M6MjoiMjAiO3M6Mjg6ImFwcHJvdmVkX21lbWJlcl9ub3RpZmljYXRpb24iO047czoyODoiZGVjbGluZWRfbWVtYmVyX25vdGlmaWNhdGlvbiI7Tjt9', 'YTo3OntzOjIyOiJlbmFibGVfdGVtcGxhdGVfcm91dGVzIjtzOjE6InkiO3M6MTE6InN0cmljdF91cmxzIjtzOjE6InkiO3M6ODoic2l0ZV80MDQiO3M6MDoiIjtzOjE5OiJzYXZlX3RtcGxfcmV2aXNpb25zIjtzOjE6Im4iO3M6MTg6Im1heF90bXBsX3JldmlzaW9ucyI7czoxOiI1IjtzOjE1OiJzYXZlX3RtcGxfZmlsZXMiO3M6MToieSI7czoxODoidG1wbF9maWxlX2Jhc2VwYXRoIjtOO30=', 'YToxMzp7czoyMzoiYXV0b19hc3NpZ25fY2F0X3BhcmVudHMiO3M6MToieSI7czoyMzoiYXV0b19jb252ZXJ0X2hpZ2hfYXNjaWkiO3M6MToibiI7czoyMzoiY29tbWVudF9lZGl0X3RpbWVfbGltaXQiO047czoyNzoiY29tbWVudF9tb2RlcmF0aW9uX292ZXJyaWRlIjtOO3M6MjI6ImNvbW1lbnRfd29yZF9jZW5zb3JpbmciO047czoxNToiZW5hYmxlX2NvbW1lbnRzIjtOO3M6MTg6ImltYWdlX2xpYnJhcnlfcGF0aCI7czowOiIiO3M6MjE6ImltYWdlX3Jlc2l6ZV9wcm90b2NvbCI7czozOiJnZDIiO3M6MjI6Im5ld19wb3N0c19jbGVhcl9jYWNoZXMiO3M6MToieSI7czoyMjoicmVzZXJ2ZWRfY2F0ZWdvcnlfd29yZCI7czo4OiJjYXRlZ29yeSI7czoxNjoidGh1bWJuYWlsX3ByZWZpeCI7czo1OiJ0aHVtYiI7czoxNzoidXNlX2NhdGVnb3J5X25hbWUiO3M6MToibiI7czoxNDoid29yZF9zZXBhcmF0b3IiO3M6NDoiZGFzaCI7fQ==', 'YToxOntzOjQyOiIvQXBwbGljYXRpb25zL01BTVAvaHRkb2NzL295c3Rlci9pbmRleC5waHAiO3M6MzI6IjIwYmY1Y2UzN2VhMjdkOWM1NjFjNTJkNTBkZGFmMmQ5Ijt9', 'YToxOntpOjE7YToxOntzOjM6InVybCI7czoyMDoiaHR0cDovL295c3Rlci5sb2NhbC8iO319');

-- --------------------------------------------------------

--
-- Table structure for table `exp_snippets`
--

CREATE TABLE `exp_snippets` (
`snippet_id` int(10) unsigned NOT NULL,
  `site_id` int(4) NOT NULL,
  `snippet_name` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  `snippet_contents` text COLLATE utf8_unicode_ci,
  `edit_date` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_snippets`
--

INSERT INTO `exp_snippets` (`snippet_id`, `site_id`, `snippet_name`, `snippet_contents`, `edit_date`) VALUES
(1, 1, 'header', '<!doctype html>\n<html class="no-js" lang="en">\n	<head>\n		<meta charset="utf-8">\n		<meta http-equiv="x-ua-compatible" content="ie=edge">\n		<meta name="viewport" content="width=device-width, initial-scale=1.0">\n		<title>Oyster Yachts</title>\n		<link rel="stylesheet" href="{site_url}dist/style.css">\n	</head>\n	<body>', 1456328053),
(2, 1, 'footer', '<script src="{site_url}dist/scripts.js"></script>\n</body>\n</html>', 1456328177);

-- --------------------------------------------------------

--
-- Table structure for table `exp_specialty_templates`
--

CREATE TABLE `exp_specialty_templates` (
`template_id` int(6) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `enable_template` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `template_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `data_title` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `template_type` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_subtype` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_data` text COLLATE utf8_unicode_ci NOT NULL,
  `template_notes` text COLLATE utf8_unicode_ci,
  `edit_date` int(10) NOT NULL DEFAULT '0',
  `last_author_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_specialty_templates`
--

INSERT INTO `exp_specialty_templates` (`template_id`, `site_id`, `enable_template`, `template_name`, `data_title`, `template_type`, `template_subtype`, `template_data`, `template_notes`, `edit_date`, `last_author_id`) VALUES
(1, 1, 'y', 'offline_template', '', 'system', NULL, '<html>\n<head>\n\n<title>System Offline</title>\n\n<style type="text/css">\n\nbody {\nbackground-color:	#ffffff;\nmargin:				50px;\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-size:			11px;\ncolor:				#000;\nbackground-color:	#fff;\n}\n\na {\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-weight:		bold;\nletter-spacing:		.09em;\ntext-decoration:	none;\ncolor:			  #330099;\nbackground-color:	transparent;\n}\n\na:visited {\ncolor:				#330099;\nbackground-color:	transparent;\n}\n\na:hover {\ncolor:				#000;\ntext-decoration:	underline;\nbackground-color:	transparent;\n}\n\n#content  {\nborder:				#999999 1px solid;\npadding:			22px 25px 14px 25px;\n}\n\nh1 {\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-weight:		bold;\nfont-size:			14px;\ncolor:				#000;\nmargin-top: 		0;\nmargin-bottom:		14px;\n}\n\np {\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-size: 			12px;\nfont-weight: 		normal;\nmargin-top: 		12px;\nmargin-bottom: 		14px;\ncolor: 				#000;\n}\n</style>\n\n</head>\n\n<body>\n\n<div id="content">\n\n<h1>System Offline</h1>\n\n<p>This site is currently offline</p>\n\n</div>\n\n</body>\n\n</html>', NULL, 1456308576, 0),
(2, 1, 'y', 'message_template', '', 'system', NULL, '<html>\n<head>\n\n<title>{title}</title>\n\n<meta http-equiv=''content-type'' content=''text/html; charset={charset}'' />\n\n{meta_refresh}\n\n<style type="text/css">\n\nbody {\nbackground-color:	#ffffff;\nmargin:				50px;\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-size:			11px;\ncolor:				#000;\nbackground-color:	#fff;\n}\n\na {\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nletter-spacing:		.09em;\ntext-decoration:	none;\ncolor:			  #330099;\nbackground-color:	transparent;\n}\n\na:visited {\ncolor:				#330099;\nbackground-color:	transparent;\n}\n\na:active {\ncolor:				#ccc;\nbackground-color:	transparent;\n}\n\na:hover {\ncolor:				#000;\ntext-decoration:	underline;\nbackground-color:	transparent;\n}\n\n#content  {\nborder:				#000 1px solid;\nbackground-color: 	#DEDFE3;\npadding:			22px 25px 14px 25px;\n}\n\nh1 {\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-weight:		bold;\nfont-size:			14px;\ncolor:				#000;\nmargin-top: 		0;\nmargin-bottom:		14px;\n}\n\np {\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-size: 			12px;\nfont-weight: 		normal;\nmargin-top: 		12px;\nmargin-bottom: 		14px;\ncolor: 				#000;\n}\n\nul {\nmargin-bottom: 		16px;\n}\n\nli {\nlist-style:			square;\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-size: 			12px;\nfont-weight: 		normal;\nmargin-top: 		8px;\nmargin-bottom: 		8px;\ncolor: 				#000;\n}\n\n</style>\n\n</head>\n\n<body>\n\n<div id="content">\n\n<h1>{heading}</h1>\n\n{content}\n\n<p>{link}</p>\n\n</div>\n\n</body>\n\n</html>', NULL, 1456308576, 0),
(3, 1, 'y', 'admin_notify_reg', 'Notification of new member registration', 'email', 'members', 'New member registration site: {site_name}\n\nScreen name: {name}\nUser name: {username}\nEmail: {email}\n\nYour control panel URL: {control_panel_url}', NULL, 1456308576, 0),
(4, 1, 'y', 'admin_notify_entry', 'A new channel entry has been posted', 'email', 'content', 'A new entry has been posted in the following channel:\n{channel_name}\n\nThe title of the entry is:\n{entry_title}\n\nPosted by: {name}\nEmail: {email}\n\nTo read the entry please visit:\n{entry_url}\n', NULL, 1456308576, 0),
(5, 1, 'y', 'admin_notify_comment', 'You have just received a comment', 'email', 'comments', 'You have just received a comment for the following channel:\n{channel_name}\n\nThe title of the entry is:\n{entry_title}\n\nLocated at:\n{comment_url}\n\nPosted by: {name}\nEmail: {email}\nURL: {url}\nLocation: {location}\n\n{comment}', NULL, 1456308576, 0),
(6, 1, 'y', 'mbr_activation_instructions', 'Enclosed is your activation code', 'email', 'members', 'Thank you for your new member registration.\n\nTo activate your new account, please visit the following URL:\n\n{unwrap}{activation_url}{/unwrap}\n\nThank You!\n\n{site_name}\n\n{site_url}', NULL, 1456308576, 0),
(7, 1, 'y', 'forgot_password_instructions', 'Login information', 'email', 'members', '{name},\n\nTo reset your password, please go to the following page:\n\n{reset_url}\n\nThen log in with your username: {username}\n\nIf you do not wish to reset your password, ignore this message. It will expire in 24 hours.\n\n{site_name}\n{site_url}', NULL, 1456308576, 0),
(8, 1, 'y', 'validated_member_notify', 'Your membership account has been activated', 'email', 'members', '{name},\n\nYour membership account has been activated and is ready for use.\n\nThank You!\n\n{site_name}\n{site_url}', NULL, 1456308576, 0),
(9, 1, 'y', 'decline_member_validation', 'Your membership account has been declined', 'email', 'members', '{name},\n\nWe''re sorry but our staff has decided not to validate your membership.\n\n{site_name}\n{site_url}', NULL, 1456308576, 0),
(10, 1, 'y', 'comment_notification', 'Someone just responded to your comment', 'email', 'comments', '{name_of_commenter} just responded to the entry you subscribed to at:\n{channel_name}\n\nThe title of the entry is:\n{entry_title}\n\nYou can see the comment at the following URL:\n{comment_url}\n\n{comment}\n\nTo stop receiving notifications for this comment, click here:\n{notification_removal_url}', NULL, 1456308576, 0),
(11, 1, 'y', 'comments_opened_notification', 'New comments have been added', 'email', 'comments', 'Responses have been added to the entry you subscribed to at:\n{channel_name}\n\nThe title of the entry is:\n{entry_title}\n\nYou can see the comments at the following URL:\n{comment_url}\n\n{comments}\n{comment}\n{/comments}\n\nTo stop receiving notifications for this entry, click here:\n{notification_removal_url}', NULL, 1456308576, 0),
(12, 1, 'y', 'private_message_notification', 'Someone has sent you a Private Message', 'email', 'private_messages', '\n{recipient_name},\n\n{sender_name} has just sent you a Private Message titled ‘{message_subject}’.\n\nYou can see the Private Message by logging in and viewing your inbox at:\n{site_url}\n\nContent:\n\n{message_content}\n\nTo stop receiving notifications of Private Messages, turn the option off in your Email Settings.\n\n{site_name}\n{site_url}', NULL, 1456308576, 0),
(13, 1, 'y', 'pm_inbox_full', 'Your private message mailbox is full', 'email', 'private_messages', '{recipient_name},\n\n{sender_name} has just attempted to send you a Private Message,\nbut your inbox is full, exceeding the maximum of {pm_storage_limit}.\n\nPlease log in and remove unwanted messages from your inbox at:\n{site_url}', NULL, 1456308576, 0);

-- --------------------------------------------------------

--
-- Table structure for table `exp_stats`
--

CREATE TABLE `exp_stats` (
`stat_id` int(10) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `total_members` mediumint(7) NOT NULL DEFAULT '0',
  `recent_member_id` int(10) NOT NULL DEFAULT '0',
  `recent_member` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `total_entries` mediumint(8) NOT NULL DEFAULT '0',
  `total_forum_topics` mediumint(8) NOT NULL DEFAULT '0',
  `total_forum_posts` mediumint(8) NOT NULL DEFAULT '0',
  `total_comments` mediumint(8) NOT NULL DEFAULT '0',
  `last_entry_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_forum_post_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_comment_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_visitor_date` int(10) unsigned NOT NULL DEFAULT '0',
  `most_visitors` mediumint(7) NOT NULL DEFAULT '0',
  `most_visitor_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_cache_clear` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_stats`
--

INSERT INTO `exp_stats` (`stat_id`, `site_id`, `total_members`, `recent_member_id`, `recent_member`, `total_entries`, `total_forum_topics`, `total_forum_posts`, `total_comments`, `last_entry_date`, `last_forum_post_date`, `last_comment_date`, `last_visitor_date`, `most_visitors`, `most_visitor_date`, `last_cache_clear`) VALUES
(1, 1, 1, 1, 'Chris', 1, 0, 0, 0, 1456328580, 0, 0, 1456397796, 6, 1456336451, 1456308576);

-- --------------------------------------------------------

--
-- Table structure for table `exp_statuses`
--

CREATE TABLE `exp_statuses` (
`status_id` int(6) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_id` int(4) unsigned NOT NULL,
  `status` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `status_order` int(3) unsigned NOT NULL,
  `highlight` varchar(30) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_statuses`
--

INSERT INTO `exp_statuses` (`status_id`, `site_id`, `group_id`, `status`, `status_order`, `highlight`) VALUES
(1, 1, 1, 'open', 1, '009933'),
(2, 1, 1, 'closed', 2, '990000');

-- --------------------------------------------------------

--
-- Table structure for table `exp_status_groups`
--

CREATE TABLE `exp_status_groups` (
`group_id` int(4) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_status_groups`
--

INSERT INTO `exp_status_groups` (`group_id`, `site_id`, `group_name`) VALUES
(1, 1, 'Default');

-- --------------------------------------------------------

--
-- Table structure for table `exp_status_no_access`
--

CREATE TABLE `exp_status_no_access` (
  `status_id` int(6) unsigned NOT NULL,
  `member_group` smallint(4) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_templates`
--

CREATE TABLE `exp_templates` (
`template_id` int(10) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_id` int(6) unsigned NOT NULL,
  `template_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `template_type` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'webpage',
  `template_data` mediumtext COLLATE utf8_unicode_ci,
  `template_notes` text COLLATE utf8_unicode_ci,
  `edit_date` int(10) NOT NULL DEFAULT '0',
  `last_author_id` int(10) unsigned NOT NULL DEFAULT '0',
  `cache` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `refresh` int(6) unsigned NOT NULL DEFAULT '0',
  `no_auth_bounce` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `enable_http_auth` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `allow_php` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `php_parse_location` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'o',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `protect_javascript` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_templates`
--

INSERT INTO `exp_templates` (`template_id`, `site_id`, `group_id`, `template_name`, `template_type`, `template_data`, `template_notes`, `edit_date`, `last_author_id`, `cache`, `refresh`, `no_auth_bounce`, `enable_http_auth`, `allow_php`, `php_parse_location`, `hits`, `protect_javascript`) VALUES
(2, 1, 2, 'index', 'webpage', '{header}\n\n{exp:channel:entries}\n\n<section class="hero">\n\n</section>\n\n<section class="about-yacht">\n	<div class="row row-pad">\n		<div class="large-offset-1 large-7 float">\n			{brokerage_about}\n		</div>\n		<div class="large-offset-1 large-3 float">\n			<table class="table">\n				<thead>\n					<tr>\n						<td colspan="2">{title} overview</td>\n					</tr>\n				</thead>\n				<tbody>\n					<tr>\n						<td>Location</td>\n						<td>{brokerage_location}</td>\n					</tr>\n					<tr>\n						<td>Year Built</td>\n						<td>{brokerage_year_built}</td>\n					</tr>\n					<tr>\n						<td>Rig</td>\n						<td>{brokerage_rig}</td>\n					</tr>\n					<tr>\n						<td>Joinery</td>\n						<td>{brokerage_joinery}</td>\n					</tr>\n					<tr>\n						<td>Cabins</td>\n						<td>{brokerage_cabins}</td>\n					</tr>\n					<tr>\n						<td>Status</td>\n						<td>{brokerage_status}</td>\n					</tr>\n					<tr>\n						<td>Price</td>\n						<td>{brokerage_price}</td>\n					</tr>	\n					<tr>\n						<td>Year Built</td>\n						<td>{brokerage_year_built}</td>\n					</tr>\n				</tbody>\n				{if brokerage_feature_text != ""}\n				<tfoot>\n					<tr>\n						<td colspan="2" class="table-footer">{brokerage_feature_text}</td>\n					</tr>\n				</tfoot>\n				{/if}\n			</table>\n		</div>\n	</div>\n</section>\n\n{/exp:channel:entries}\n\n{footer}', NULL, 1456394753, 1, 'n', 0, '2', 'n', 'y', 'o', 0, 'n');

-- --------------------------------------------------------

--
-- Table structure for table `exp_template_groups`
--

CREATE TABLE `exp_template_groups` (
`group_id` int(6) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `group_order` int(3) unsigned NOT NULL,
  `is_site_default` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_template_groups`
--

INSERT INTO `exp_template_groups` (`group_id`, `site_id`, `group_name`, `group_order`, `is_site_default`) VALUES
(2, 1, 'brokerage', 1, 'y');

-- --------------------------------------------------------

--
-- Table structure for table `exp_template_member_groups`
--

CREATE TABLE `exp_template_member_groups` (
  `group_id` smallint(4) unsigned NOT NULL,
  `template_group_id` mediumint(5) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_template_no_access`
--

CREATE TABLE `exp_template_no_access` (
  `template_id` int(6) unsigned NOT NULL,
  `member_group` smallint(4) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_template_routes`
--

CREATE TABLE `exp_template_routes` (
`route_id` int(10) unsigned NOT NULL,
  `template_id` int(10) unsigned NOT NULL,
  `order` int(10) unsigned DEFAULT NULL,
  `route` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `route_parsed` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `route_required` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_throttle`
--

CREATE TABLE `exp_throttle` (
`throttle_id` int(10) unsigned NOT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL,
  `locked_out` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_update_log`
--

CREATE TABLE `exp_update_log` (
`log_id` int(10) unsigned NOT NULL,
  `timestamp` int(10) unsigned DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `method` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `line` int(10) unsigned DEFAULT NULL,
  `file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_update_log`
--

INSERT INTO `exp_update_log` (`log_id`, `timestamp`, `message`, `method`, `line`, `file`) VALUES
(1, 1456308576, 'Smartforge::add_key failed. Table ''exp_comments'' does not exist.', 'Smartforge::add_key', 120, '/Applications/MAMP/htdocs/oyster/system/ee/EllisLab/Addons/comment/upd.comment.php');

-- --------------------------------------------------------

--
-- Table structure for table `exp_upload_no_access`
--

CREATE TABLE `exp_upload_no_access` (
  `upload_id` int(6) unsigned NOT NULL,
  `member_group` smallint(4) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_upload_no_access`
--

INSERT INTO `exp_upload_no_access` (`upload_id`, `member_group`) VALUES
(6, 5);

-- --------------------------------------------------------

--
-- Table structure for table `exp_upload_prefs`
--

CREATE TABLE `exp_upload_prefs` (
`id` int(4) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `server_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `allowed_types` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'img',
  `default_modal_view` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'list',
  `max_size` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `max_height` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `max_width` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `properties` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pre_format` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_format` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_properties` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_pre_format` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_post_format` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat_group` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `batch_location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module_id` int(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exp_upload_prefs`
--

INSERT INTO `exp_upload_prefs` (`id`, `site_id`, `name`, `server_path`, `url`, `allowed_types`, `default_modal_view`, `max_size`, `max_height`, `max_width`, `properties`, `pre_format`, `post_format`, `file_properties`, `file_pre_format`, `file_post_format`, `cat_group`, `batch_location`, `module_id`) VALUES
(1, 1, 'Avatars', '/Applications/MAMP/htdocs/oyster/images/avatars/', 'http://oyster.local/images/avatars/', 'img', 'list', '50', '100', '100', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(2, 1, 'Default Avatars', '/Applications/MAMP/htdocs/oyster/images/avatars/default/', 'http://oyster.local/images/avatars/default/', 'img', 'list', '50', '100', '100', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(3, 1, 'Member Photos', '/Applications/MAMP/htdocs/oyster/images/member_photos/', 'http://oyster.local/images/member_photos/', 'img', 'list', '50', '100', '100', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(4, 1, 'Signature Attachments', '/Applications/MAMP/htdocs/oyster/images/signature_attachments/', 'http://oyster.local/images/signature_attachments/', 'img', 'list', '30', '80', '480', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(5, 1, 'PM Attachments', '/Applications/MAMP/htdocs/oyster/images/pm_attachments/', 'http://oyster.local/images/pm_attachments/', 'img', 'list', '250', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(6, 1, 'Brokerage', '/Applications/MAMP/htdocs/oyster/images/brokerage/', 'http://oyster.local/images/brokerage/', 'img', 'list', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `exp_zenbu_display_settings`
--

CREATE TABLE `exp_zenbu_display_settings` (
`id` int(10) unsigned NOT NULL,
  `fieldType` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `userId` int(10) unsigned DEFAULT NULL,
  `userGroupId` int(10) unsigned DEFAULT NULL,
  `fieldId` int(10) unsigned DEFAULT NULL,
  `sectionId` int(10) unsigned DEFAULT NULL,
  `subSectionId` int(10) unsigned DEFAULT NULL,
  `show` tinyint(1) unsigned DEFAULT '0',
  `order` int(10) unsigned DEFAULT NULL,
  `settings` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_zenbu_filter_cache`
--

CREATE TABLE `exp_zenbu_filter_cache` (
`cache_id` int(10) unsigned NOT NULL,
  `member_id` int(10) unsigned DEFAULT NULL,
  `site_id` int(10) unsigned DEFAULT NULL,
  `save_date` int(10) unsigned DEFAULT NULL,
  `filter_rules` mediumtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_zenbu_general_settings`
--

CREATE TABLE `exp_zenbu_general_settings` (
`id` int(10) unsigned NOT NULL,
  `userId` int(10) unsigned DEFAULT NULL,
  `userGroupId` int(10) unsigned DEFAULT NULL,
  `setting` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_zenbu_permissions`
--

CREATE TABLE `exp_zenbu_permissions` (
`id` int(10) unsigned NOT NULL,
  `userId` int(10) unsigned DEFAULT NULL,
  `userGroupId` int(10) unsigned DEFAULT NULL,
  `setting` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_zenbu_saved_searches`
--

CREATE TABLE `exp_zenbu_saved_searches` (
`id` int(10) unsigned NOT NULL,
  `label` text COLLATE utf8_unicode_ci,
  `userId` int(10) unsigned DEFAULT NULL,
  `userGroupId` int(10) unsigned DEFAULT '0',
  `order` int(10) unsigned DEFAULT '0',
  `site_id` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_zenbu_saved_search_filters`
--

CREATE TABLE `exp_zenbu_saved_search_filters` (
`id` int(10) unsigned NOT NULL,
  `searchId` int(10) unsigned DEFAULT NULL,
  `filterAttribute1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `filterAttribute2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `filterAttribute3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `exp_actions`
--
ALTER TABLE `exp_actions`
 ADD PRIMARY KEY (`action_id`);

--
-- Indexes for table `exp_captcha`
--
ALTER TABLE `exp_captcha`
 ADD PRIMARY KEY (`captcha_id`), ADD KEY `word` (`word`);

--
-- Indexes for table `exp_categories`
--
ALTER TABLE `exp_categories`
 ADD PRIMARY KEY (`cat_id`), ADD KEY `group_id` (`group_id`), ADD KEY `cat_name` (`cat_name`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_category_fields`
--
ALTER TABLE `exp_category_fields`
 ADD PRIMARY KEY (`field_id`), ADD KEY `site_id` (`site_id`), ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `exp_category_field_data`
--
ALTER TABLE `exp_category_field_data`
 ADD PRIMARY KEY (`cat_id`), ADD KEY `site_id` (`site_id`), ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `exp_category_groups`
--
ALTER TABLE `exp_category_groups`
 ADD PRIMARY KEY (`group_id`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_category_posts`
--
ALTER TABLE `exp_category_posts`
 ADD PRIMARY KEY (`entry_id`,`cat_id`);

--
-- Indexes for table `exp_channels`
--
ALTER TABLE `exp_channels`
 ADD PRIMARY KEY (`channel_id`), ADD KEY `cat_group` (`cat_group`), ADD KEY `status_group` (`status_group`), ADD KEY `field_group` (`field_group`), ADD KEY `channel_name` (`channel_name`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_channel_data`
--
ALTER TABLE `exp_channel_data`
 ADD PRIMARY KEY (`entry_id`), ADD KEY `channel_id` (`channel_id`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_channel_entries_autosave`
--
ALTER TABLE `exp_channel_entries_autosave`
 ADD PRIMARY KEY (`entry_id`), ADD KEY `channel_id` (`channel_id`), ADD KEY `author_id` (`author_id`), ADD KEY `url_title` (`url_title`), ADD KEY `status` (`status`), ADD KEY `entry_date` (`entry_date`), ADD KEY `expiration_date` (`expiration_date`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_channel_fields`
--
ALTER TABLE `exp_channel_fields`
 ADD PRIMARY KEY (`field_id`), ADD KEY `group_id` (`group_id`), ADD KEY `field_type` (`field_type`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_channel_form_settings`
--
ALTER TABLE `exp_channel_form_settings`
 ADD PRIMARY KEY (`channel_form_settings_id`), ADD KEY `site_id` (`site_id`), ADD KEY `channel_id` (`channel_id`);

--
-- Indexes for table `exp_channel_grid_field_13`
--
ALTER TABLE `exp_channel_grid_field_13`
 ADD PRIMARY KEY (`row_id`), ADD KEY `entry_id` (`entry_id`);

--
-- Indexes for table `exp_channel_images`
--
ALTER TABLE `exp_channel_images`
 ADD PRIMARY KEY (`image_id`), ADD KEY `entry_id` (`entry_id`);

--
-- Indexes for table `exp_channel_member_groups`
--
ALTER TABLE `exp_channel_member_groups`
 ADD PRIMARY KEY (`group_id`,`channel_id`);

--
-- Indexes for table `exp_channel_titles`
--
ALTER TABLE `exp_channel_titles`
 ADD PRIMARY KEY (`entry_id`), ADD KEY `channel_id` (`channel_id`), ADD KEY `author_id` (`author_id`), ADD KEY `url_title` (`url_title`), ADD KEY `status` (`status`), ADD KEY `entry_date` (`entry_date`), ADD KEY `expiration_date` (`expiration_date`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_comments`
--
ALTER TABLE `exp_comments`
 ADD PRIMARY KEY (`comment_id`), ADD KEY `entry_id` (`entry_id`), ADD KEY `channel_id` (`channel_id`), ADD KEY `author_id` (`author_id`), ADD KEY `status` (`status`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_comment_subscriptions`
--
ALTER TABLE `exp_comment_subscriptions`
 ADD PRIMARY KEY (`subscription_id`), ADD KEY `entry_id` (`entry_id`), ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `exp_content_types`
--
ALTER TABLE `exp_content_types`
 ADD PRIMARY KEY (`content_type_id`), ADD KEY `name` (`name`);

--
-- Indexes for table `exp_cp_log`
--
ALTER TABLE `exp_cp_log`
 ADD PRIMARY KEY (`id`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_cp_search_index`
--
ALTER TABLE `exp_cp_search_index`
 ADD PRIMARY KEY (`search_id`), ADD FULLTEXT KEY `keywords` (`keywords`);

--
-- Indexes for table `exp_developer_log`
--
ALTER TABLE `exp_developer_log`
 ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `exp_editor_configs`
--
ALTER TABLE `exp_editor_configs`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exp_email_cache`
--
ALTER TABLE `exp_email_cache`
 ADD PRIMARY KEY (`cache_id`);

--
-- Indexes for table `exp_email_cache_mg`
--
ALTER TABLE `exp_email_cache_mg`
 ADD PRIMARY KEY (`cache_id`,`group_id`);

--
-- Indexes for table `exp_email_cache_ml`
--
ALTER TABLE `exp_email_cache_ml`
 ADD PRIMARY KEY (`cache_id`,`list_id`);

--
-- Indexes for table `exp_email_console_cache`
--
ALTER TABLE `exp_email_console_cache`
 ADD PRIMARY KEY (`cache_id`);

--
-- Indexes for table `exp_entry_versioning`
--
ALTER TABLE `exp_entry_versioning`
 ADD PRIMARY KEY (`version_id`), ADD KEY `entry_id` (`entry_id`);

--
-- Indexes for table `exp_extensions`
--
ALTER TABLE `exp_extensions`
 ADD PRIMARY KEY (`extension_id`);

--
-- Indexes for table `exp_fieldtypes`
--
ALTER TABLE `exp_fieldtypes`
 ADD PRIMARY KEY (`fieldtype_id`);

--
-- Indexes for table `exp_field_groups`
--
ALTER TABLE `exp_field_groups`
 ADD PRIMARY KEY (`group_id`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_files`
--
ALTER TABLE `exp_files`
 ADD PRIMARY KEY (`file_id`), ADD KEY `upload_location_id` (`upload_location_id`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_file_categories`
--
ALTER TABLE `exp_file_categories`
 ADD KEY `file_id` (`file_id`), ADD KEY `cat_id` (`cat_id`);

--
-- Indexes for table `exp_file_dimensions`
--
ALTER TABLE `exp_file_dimensions`
 ADD PRIMARY KEY (`id`), ADD KEY `upload_location_id` (`upload_location_id`);

--
-- Indexes for table `exp_file_watermarks`
--
ALTER TABLE `exp_file_watermarks`
 ADD PRIMARY KEY (`wm_id`);

--
-- Indexes for table `exp_global_variables`
--
ALTER TABLE `exp_global_variables`
 ADD PRIMARY KEY (`variable_id`), ADD KEY `variable_name` (`variable_name`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_grid_columns`
--
ALTER TABLE `exp_grid_columns`
 ADD PRIMARY KEY (`col_id`), ADD KEY `field_id` (`field_id`);

--
-- Indexes for table `exp_html_buttons`
--
ALTER TABLE `exp_html_buttons`
 ADD PRIMARY KEY (`id`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_layout_publish`
--
ALTER TABLE `exp_layout_publish`
 ADD PRIMARY KEY (`layout_id`), ADD KEY `site_id` (`site_id`), ADD KEY `channel_id` (`channel_id`);

--
-- Indexes for table `exp_layout_publish_member_groups`
--
ALTER TABLE `exp_layout_publish_member_groups`
 ADD PRIMARY KEY (`layout_id`,`group_id`);

--
-- Indexes for table `exp_members`
--
ALTER TABLE `exp_members`
 ADD PRIMARY KEY (`member_id`), ADD KEY `group_id` (`group_id`), ADD KEY `unique_id` (`unique_id`), ADD KEY `password` (`password`);

--
-- Indexes for table `exp_member_bulletin_board`
--
ALTER TABLE `exp_member_bulletin_board`
 ADD PRIMARY KEY (`bulletin_id`), ADD KEY `sender_id` (`sender_id`), ADD KEY `hash` (`hash`);

--
-- Indexes for table `exp_member_data`
--
ALTER TABLE `exp_member_data`
 ADD PRIMARY KEY (`member_id`);

--
-- Indexes for table `exp_member_fields`
--
ALTER TABLE `exp_member_fields`
 ADD PRIMARY KEY (`m_field_id`);

--
-- Indexes for table `exp_member_groups`
--
ALTER TABLE `exp_member_groups`
 ADD PRIMARY KEY (`group_id`,`site_id`);

--
-- Indexes for table `exp_member_homepage`
--
ALTER TABLE `exp_member_homepage`
 ADD PRIMARY KEY (`member_id`);

--
-- Indexes for table `exp_member_search`
--
ALTER TABLE `exp_member_search`
 ADD PRIMARY KEY (`search_id`), ADD KEY `member_id` (`member_id`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_message_attachments`
--
ALTER TABLE `exp_message_attachments`
 ADD PRIMARY KEY (`attachment_id`);

--
-- Indexes for table `exp_message_copies`
--
ALTER TABLE `exp_message_copies`
 ADD PRIMARY KEY (`copy_id`), ADD KEY `message_id` (`message_id`), ADD KEY `recipient_id` (`recipient_id`), ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `exp_message_data`
--
ALTER TABLE `exp_message_data`
 ADD PRIMARY KEY (`message_id`), ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `exp_message_folders`
--
ALTER TABLE `exp_message_folders`
 ADD PRIMARY KEY (`member_id`);

--
-- Indexes for table `exp_message_listed`
--
ALTER TABLE `exp_message_listed`
 ADD PRIMARY KEY (`listed_id`);

--
-- Indexes for table `exp_modules`
--
ALTER TABLE `exp_modules`
 ADD PRIMARY KEY (`module_id`);

--
-- Indexes for table `exp_module_member_groups`
--
ALTER TABLE `exp_module_member_groups`
 ADD PRIMARY KEY (`group_id`,`module_id`);

--
-- Indexes for table `exp_online_users`
--
ALTER TABLE `exp_online_users`
 ADD PRIMARY KEY (`online_id`), ADD KEY `date` (`date`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_password_lockout`
--
ALTER TABLE `exp_password_lockout`
 ADD PRIMARY KEY (`lockout_id`), ADD KEY `login_date` (`login_date`), ADD KEY `ip_address` (`ip_address`), ADD KEY `user_agent` (`user_agent`);

--
-- Indexes for table `exp_plugins`
--
ALTER TABLE `exp_plugins`
 ADD PRIMARY KEY (`plugin_id`);

--
-- Indexes for table `exp_relationships`
--
ALTER TABLE `exp_relationships`
 ADD PRIMARY KEY (`relationship_id`), ADD KEY `parent_id` (`parent_id`), ADD KEY `child_id` (`child_id`), ADD KEY `field_id` (`field_id`), ADD KEY `grid_row_id` (`grid_row_id`);

--
-- Indexes for table `exp_remember_me`
--
ALTER TABLE `exp_remember_me`
 ADD PRIMARY KEY (`remember_me_id`), ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `exp_reset_password`
--
ALTER TABLE `exp_reset_password`
 ADD PRIMARY KEY (`reset_id`);

--
-- Indexes for table `exp_revision_tracker`
--
ALTER TABLE `exp_revision_tracker`
 ADD PRIMARY KEY (`tracker_id`), ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `exp_rte_tools`
--
ALTER TABLE `exp_rte_tools`
 ADD PRIMARY KEY (`tool_id`), ADD KEY `enabled` (`enabled`);

--
-- Indexes for table `exp_rte_toolsets`
--
ALTER TABLE `exp_rte_toolsets`
 ADD PRIMARY KEY (`toolset_id`), ADD KEY `member_id` (`member_id`), ADD KEY `enabled` (`enabled`);

--
-- Indexes for table `exp_search`
--
ALTER TABLE `exp_search`
 ADD PRIMARY KEY (`search_id`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_search_log`
--
ALTER TABLE `exp_search_log`
 ADD PRIMARY KEY (`id`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_security_hashes`
--
ALTER TABLE `exp_security_hashes`
 ADD PRIMARY KEY (`hash_id`), ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `exp_sessions`
--
ALTER TABLE `exp_sessions`
 ADD PRIMARY KEY (`session_id`), ADD KEY `member_id` (`member_id`), ADD KEY `last_activity_idx` (`last_activity`);

--
-- Indexes for table `exp_sites`
--
ALTER TABLE `exp_sites`
 ADD PRIMARY KEY (`site_id`), ADD KEY `site_name` (`site_name`);

--
-- Indexes for table `exp_snippets`
--
ALTER TABLE `exp_snippets`
 ADD PRIMARY KEY (`snippet_id`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_specialty_templates`
--
ALTER TABLE `exp_specialty_templates`
 ADD PRIMARY KEY (`template_id`), ADD KEY `template_name` (`template_name`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_stats`
--
ALTER TABLE `exp_stats`
 ADD PRIMARY KEY (`stat_id`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_statuses`
--
ALTER TABLE `exp_statuses`
 ADD PRIMARY KEY (`status_id`), ADD KEY `group_id` (`group_id`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_status_groups`
--
ALTER TABLE `exp_status_groups`
 ADD PRIMARY KEY (`group_id`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_status_no_access`
--
ALTER TABLE `exp_status_no_access`
 ADD PRIMARY KEY (`status_id`,`member_group`);

--
-- Indexes for table `exp_templates`
--
ALTER TABLE `exp_templates`
 ADD PRIMARY KEY (`template_id`), ADD KEY `group_id` (`group_id`), ADD KEY `template_name` (`template_name`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_template_groups`
--
ALTER TABLE `exp_template_groups`
 ADD PRIMARY KEY (`group_id`), ADD KEY `site_id` (`site_id`), ADD KEY `group_name_idx` (`group_name`), ADD KEY `group_order_idx` (`group_order`);

--
-- Indexes for table `exp_template_member_groups`
--
ALTER TABLE `exp_template_member_groups`
 ADD PRIMARY KEY (`group_id`,`template_group_id`);

--
-- Indexes for table `exp_template_no_access`
--
ALTER TABLE `exp_template_no_access`
 ADD PRIMARY KEY (`template_id`,`member_group`);

--
-- Indexes for table `exp_template_routes`
--
ALTER TABLE `exp_template_routes`
 ADD PRIMARY KEY (`route_id`), ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `exp_throttle`
--
ALTER TABLE `exp_throttle`
 ADD PRIMARY KEY (`throttle_id`), ADD KEY `ip_address` (`ip_address`), ADD KEY `last_activity` (`last_activity`);

--
-- Indexes for table `exp_update_log`
--
ALTER TABLE `exp_update_log`
 ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `exp_upload_no_access`
--
ALTER TABLE `exp_upload_no_access`
 ADD PRIMARY KEY (`upload_id`,`member_group`);

--
-- Indexes for table `exp_upload_prefs`
--
ALTER TABLE `exp_upload_prefs`
 ADD PRIMARY KEY (`id`), ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `exp_zenbu_display_settings`
--
ALTER TABLE `exp_zenbu_display_settings`
 ADD PRIMARY KEY (`id`), ADD KEY `userId` (`userId`), ADD KEY `userGroupId` (`userGroupId`);

--
-- Indexes for table `exp_zenbu_filter_cache`
--
ALTER TABLE `exp_zenbu_filter_cache`
 ADD PRIMARY KEY (`cache_id`);

--
-- Indexes for table `exp_zenbu_general_settings`
--
ALTER TABLE `exp_zenbu_general_settings`
 ADD PRIMARY KEY (`id`), ADD KEY `userId` (`userId`), ADD KEY `userGroupId` (`userGroupId`);

--
-- Indexes for table `exp_zenbu_permissions`
--
ALTER TABLE `exp_zenbu_permissions`
 ADD PRIMARY KEY (`id`), ADD KEY `userId` (`userId`), ADD KEY `userGroupId` (`userGroupId`);

--
-- Indexes for table `exp_zenbu_saved_searches`
--
ALTER TABLE `exp_zenbu_saved_searches`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exp_zenbu_saved_search_filters`
--
ALTER TABLE `exp_zenbu_saved_search_filters`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exp_actions`
--
ALTER TABLE `exp_actions`
MODIFY `action_id` int(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `exp_captcha`
--
ALTER TABLE `exp_captcha`
MODIFY `captcha_id` bigint(13) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_categories`
--
ALTER TABLE `exp_categories`
MODIFY `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_category_fields`
--
ALTER TABLE `exp_category_fields`
MODIFY `field_id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_category_groups`
--
ALTER TABLE `exp_category_groups`
MODIFY `group_id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_channels`
--
ALTER TABLE `exp_channels`
MODIFY `channel_id` int(6) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `exp_channel_entries_autosave`
--
ALTER TABLE `exp_channel_entries_autosave`
MODIFY `entry_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `exp_channel_fields`
--
ALTER TABLE `exp_channel_fields`
MODIFY `field_id` int(6) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `exp_channel_form_settings`
--
ALTER TABLE `exp_channel_form_settings`
MODIFY `channel_form_settings_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_channel_grid_field_13`
--
ALTER TABLE `exp_channel_grid_field_13`
MODIFY `row_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `exp_channel_images`
--
ALTER TABLE `exp_channel_images`
MODIFY `image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `exp_channel_titles`
--
ALTER TABLE `exp_channel_titles`
MODIFY `entry_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `exp_comments`
--
ALTER TABLE `exp_comments`
MODIFY `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_comment_subscriptions`
--
ALTER TABLE `exp_comment_subscriptions`
MODIFY `subscription_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_content_types`
--
ALTER TABLE `exp_content_types`
MODIFY `content_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `exp_cp_log`
--
ALTER TABLE `exp_cp_log`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `exp_cp_search_index`
--
ALTER TABLE `exp_cp_search_index`
MODIFY `search_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_developer_log`
--
ALTER TABLE `exp_developer_log`
MODIFY `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `exp_editor_configs`
--
ALTER TABLE `exp_editor_configs`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `exp_email_cache`
--
ALTER TABLE `exp_email_cache`
MODIFY `cache_id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_email_console_cache`
--
ALTER TABLE `exp_email_console_cache`
MODIFY `cache_id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_entry_versioning`
--
ALTER TABLE `exp_entry_versioning`
MODIFY `version_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `exp_extensions`
--
ALTER TABLE `exp_extensions`
MODIFY `extension_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `exp_fieldtypes`
--
ALTER TABLE `exp_fieldtypes`
MODIFY `fieldtype_id` int(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `exp_field_groups`
--
ALTER TABLE `exp_field_groups`
MODIFY `group_id` int(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `exp_files`
--
ALTER TABLE `exp_files`
MODIFY `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `exp_file_dimensions`
--
ALTER TABLE `exp_file_dimensions`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_file_watermarks`
--
ALTER TABLE `exp_file_watermarks`
MODIFY `wm_id` int(4) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_global_variables`
--
ALTER TABLE `exp_global_variables`
MODIFY `variable_id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_grid_columns`
--
ALTER TABLE `exp_grid_columns`
MODIFY `col_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `exp_html_buttons`
--
ALTER TABLE `exp_html_buttons`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `exp_layout_publish`
--
ALTER TABLE `exp_layout_publish`
MODIFY `layout_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `exp_members`
--
ALTER TABLE `exp_members`
MODIFY `member_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `exp_member_bulletin_board`
--
ALTER TABLE `exp_member_bulletin_board`
MODIFY `bulletin_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_member_fields`
--
ALTER TABLE `exp_member_fields`
MODIFY `m_field_id` int(4) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_message_attachments`
--
ALTER TABLE `exp_message_attachments`
MODIFY `attachment_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_message_copies`
--
ALTER TABLE `exp_message_copies`
MODIFY `copy_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_message_data`
--
ALTER TABLE `exp_message_data`
MODIFY `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_message_listed`
--
ALTER TABLE `exp_message_listed`
MODIFY `listed_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_modules`
--
ALTER TABLE `exp_modules`
MODIFY `module_id` int(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `exp_online_users`
--
ALTER TABLE `exp_online_users`
MODIFY `online_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `exp_password_lockout`
--
ALTER TABLE `exp_password_lockout`
MODIFY `lockout_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_plugins`
--
ALTER TABLE `exp_plugins`
MODIFY `plugin_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_relationships`
--
ALTER TABLE `exp_relationships`
MODIFY `relationship_id` int(6) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_reset_password`
--
ALTER TABLE `exp_reset_password`
MODIFY `reset_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_revision_tracker`
--
ALTER TABLE `exp_revision_tracker`
MODIFY `tracker_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_rte_tools`
--
ALTER TABLE `exp_rte_tools`
MODIFY `tool_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `exp_rte_toolsets`
--
ALTER TABLE `exp_rte_toolsets`
MODIFY `toolset_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `exp_search_log`
--
ALTER TABLE `exp_search_log`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_security_hashes`
--
ALTER TABLE `exp_security_hashes`
MODIFY `hash_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `exp_sites`
--
ALTER TABLE `exp_sites`
MODIFY `site_id` int(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `exp_snippets`
--
ALTER TABLE `exp_snippets`
MODIFY `snippet_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `exp_specialty_templates`
--
ALTER TABLE `exp_specialty_templates`
MODIFY `template_id` int(6) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `exp_stats`
--
ALTER TABLE `exp_stats`
MODIFY `stat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `exp_statuses`
--
ALTER TABLE `exp_statuses`
MODIFY `status_id` int(6) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `exp_status_groups`
--
ALTER TABLE `exp_status_groups`
MODIFY `group_id` int(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `exp_templates`
--
ALTER TABLE `exp_templates`
MODIFY `template_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `exp_template_groups`
--
ALTER TABLE `exp_template_groups`
MODIFY `group_id` int(6) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `exp_template_routes`
--
ALTER TABLE `exp_template_routes`
MODIFY `route_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_throttle`
--
ALTER TABLE `exp_throttle`
MODIFY `throttle_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_update_log`
--
ALTER TABLE `exp_update_log`
MODIFY `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `exp_upload_prefs`
--
ALTER TABLE `exp_upload_prefs`
MODIFY `id` int(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `exp_zenbu_display_settings`
--
ALTER TABLE `exp_zenbu_display_settings`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_zenbu_filter_cache`
--
ALTER TABLE `exp_zenbu_filter_cache`
MODIFY `cache_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_zenbu_general_settings`
--
ALTER TABLE `exp_zenbu_general_settings`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_zenbu_permissions`
--
ALTER TABLE `exp_zenbu_permissions`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_zenbu_saved_searches`
--
ALTER TABLE `exp_zenbu_saved_searches`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exp_zenbu_saved_search_filters`
--
ALTER TABLE `exp_zenbu_saved_search_filters`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
