SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


DROP TABLE IF EXISTS `{PREFIX}addons`;
CREATE TABLE IF NOT EXISTS `{PREFIX}addons` (
  `addon_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `addon_installed` int(1) NOT NULL,
  `addon_added` int(11) NOT NULL,
  `addon_group` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `{PREFIX}avps`;
CREATE TABLE IF NOT EXISTS `{PREFIX}avps` (
  `last_cleantime` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `{PREFIX}categories`;
CREATE TABLE IF NOT EXISTS `{PREFIX}categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=17 ;

INSERT INTO `{PREFIX}categories` (`category_id`, `category_name`, `category_icon`) VALUES
(1, 'Anime', 'anime.png'),
(2, 'Pc Games', 'pcgames.png'),
(3, 'Audiobooks', 'audiobooks.png'),
(4, 'DVD boxset', 'dvdboxseti.png'),
(5, 'DVD Video', 'dvdvideo.png'),
(6, 'Console Games', 'games.png'),
(7, 'HD Movies', 'hdmovies.png'),
(8, 'HD Tv', 'hdtv.png'),
(9, 'Misc', 'misc.png'),
(10, 'Music', 'music.png'),
(11, 'Software', 'softwareq.png'),
(12, 'TV', 'tv.png'),
(13, 'TV Season Packs', 'tvseasonpacks.png'),
(14, 'Xvid', 'xvidvideo.png'),
(16, 'Porn', 'xxx2u.png');

DROP TABLE IF EXISTS `{PREFIX}faqs`;
CREATE TABLE IF NOT EXISTS `{PREFIX}faqs` (
  `faq_id` int(11) NOT NULL AUTO_INCREMENT,
  `faq_lang` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `faq_content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `faq_edited` int(20) NOT NULL,
  PRIMARY KEY (`faq_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `{PREFIX}faqs` (`faq_id`, `faq_lang`, `faq_content`, `faq_edited`) VALUES
(1, 'en', '[h4]Frequently Asked Questions[/h4]\r\n[b]Welcome to openTracker![/b]\r\nOur goal is not to become another Bytemonsoon or Suprnova (not dizzying either of them though).\r\nThe goal is to provide the absolutely latest stuff. Therefore, only specially authorised users have\r\npermission to upload torrents. If you have access to 0-day stuff do not hesitate to\r\n[url=http://opentracker.nu/demo/staff]contact[/url] us!\r\n\r\nThis is a private tracker, and you have to register before you can get full access to the site.\r\nBefore you do anything here at openTracker we suggest you read the [url=http://opentracker.nu/demo/rule]rules[/url]!\r\nThere are only a few rules to abide by, but we do enforce them!\r\n\r\nIn order to use use our tracker you must  configure your client to use\r\nany port range that does not contain those ports (a range within the region 49152 through 65535 is preferable, Notice that some clients,\r\nlike Azureus 2.0.7.0 or higher, use a single port for all torrents, while most others use one port per open torrent. The size\r\nof the range you choose should take this into account (typically less than 10 ports wide. There\r\nis no benefit whatsoever in choosing a wide range, and there are possible security implications).\r\n\r\nThese ports are used for connections between peers, not client to tracker.\r\nTherefore this change will not interfere with your ability to use other trackers (in fact it\r\nshould [i]increase[/i] your speed with torrents from any tracker, not just ours). Your client\r\nwill also still be able to connect to peers that are using the standard ports.\r\nIf your client does not allow custom ports to be used, you will have to switch to one that does.\r\n\r\nDo not ask us, or in the forums, which ports you should choose. The more random the choice is the harder\r\nit will be for ISPs to catch on to us and start limiting speeds on the ports we use.\r\nIf we simply define another range ISPs will start throttling that range also.\r\n', 0);

DROP TABLE IF EXISTS `{PREFIX}forum_categories`;
CREATE TABLE IF NOT EXISTS `{PREFIX}forum_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_sort` int(11) NOT NULL,
  `category_group` int(11) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

DROP TABLE IF EXISTS `{PREFIX}forum_forums`;
CREATE TABLE IF NOT EXISTS `{PREFIX}forum_forums` (
  `forum_id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `forum_description` text COLLATE utf8_unicode_ci NOT NULL,
  `forum_group` int(11) NOT NULL,
  `forum_category` int(11) NOT NULL,
  `forum_sort` int(11) NOT NULL,
  PRIMARY KEY (`forum_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

DROP TABLE IF EXISTS `{PREFIX}forum_posts`;
CREATE TABLE IF NOT EXISTS `{PREFIX}forum_posts` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_topic` int(11) NOT NULL,
  `post_user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `post_content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `post_added` int(20) NOT NULL,
  `post_edited_by` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `post_edited_date` int(11) NOT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

DROP TABLE IF EXISTS `{PREFIX}forum_topics`;
CREATE TABLE IF NOT EXISTS `{PREFIX}forum_topics` (
  `topic_id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_userid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `topic_subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `topic_forum` int(11) NOT NULL,
  `topic_locked` int(11) NOT NULL,
  `topic_sticky` int(11) NOT NULL,
  PRIMARY KEY (`topic_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

DROP TABLE IF EXISTS `{PREFIX}friends`;
CREATE TABLE IF NOT EXISTS `{PREFIX}friends` (
  `friend_id` int(11) NOT NULL AUTO_INCREMENT,
  `friend_sender` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `friend_receiver` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `friend_status` int(11) NOT NULL,
  PRIMARY KEY (`friend_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{PREFIX}groups`;
CREATE TABLE IF NOT EXISTS `{PREFIX}groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `group_acl` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `group_upgradable` int(11) NOT NULL,
  `group_upgradeto` int(11) NOT NULL,
  `group_downgradeto` int(11) NOT NULL,
  `group_minupload` int(11) NOT NULL,
  `group_minratio` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

INSERT INTO `{PREFIX}groups` (`group_id`, `group_name`, `group_acl`, `group_upgradable`, `group_upgradeto`, `group_downgradeto`, `group_minupload`, `group_minratio`) VALUES
(1, 'Users', 'a', 1, 2, 0, 20971520, '1'),
(2, 'Power Users', 'ab', 1, 3, 1, 52428800, '1'),
(3, 'Elite Users', 'abc', 1, 4, 2, 524288000, '1'),
(4, 'Super Users', 'abcd', 0, 0, 3, 2147483647, '1'),
(10, 'Moderators', 'abcdefghijklmnopqrstuvx', 0, 0, 0, 0, '0'),
(11, 'Administrators', 'abcdefghijklmnopqrstuvxyz', 0, 0, 0, 0, '0'),
(12, 'SysOp', 'abcdefghijklmnopqrstuvxyz', 0, 0, 0, 0, '0');

DROP TABLE IF EXISTS `{PREFIX}messages`;
CREATE TABLE IF NOT EXISTS `{PREFIX}messages` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `message_sender` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message_receiver` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message_added` int(11) NOT NULL,
  `message_subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No Subject',
  `message_content` text COLLATE utf8_unicode_ci,
  `message_unread` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`message_id`),
  KEY `receiver` (`message_receiver`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{PREFIX}navigations`;
CREATE TABLE IF NOT EXISTS `{PREFIX}navigations` (
  `navigation_id` int(11) NOT NULL AUTO_INCREMENT,
  `navigation_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `navigation_application` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `navigation_module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `navigation_sorting` int(11) NOT NULL,
  `navigation_lang` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`navigation_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

INSERT INTO `{PREFIX}navigations` (`navigation_id`, `navigation_title`, `navigation_application`, `navigation_module`, `navigation_sorting`, `navigation_lang`) VALUES
(1, 'News', 'news', '', 0, 'en'),
(2, 'Browse', 'torrent', 'browse', 1, 'en'),
(3, 'Staff', 'staff', '', 6, 'en'),
(4, 'Forum', 'forums', '', 3, 'en'),
(5, 'Rules', 'rules', '', 4, 'en'),
(6, 'FAQ', 'faq', '', 5, 'en'),
(7, 'Search', 'search', '', 2, 'en');

DROP TABLE IF EXISTS `{PREFIX}news`;
CREATE TABLE IF NOT EXISTS `{PREFIX}news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_added` int(11) NOT NULL,
  `news_userid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `news_subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `news_content` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

DROP TABLE IF EXISTS `{PREFIX}notifications`;
CREATE TABLE IF NOT EXISTS `{PREFIX}notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `notification_added` int(11) NOT NULL,
  `notification_user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `notification_data` text COLLATE utf8_unicode_ci NOT NULL,
  `notification_unread` int(11) NOT NULL,
  PRIMARY KEY (`notification_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{PREFIX}peers`;
CREATE TABLE IF NOT EXISTS `{PREFIX}peers` (
  `peer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `peer_torrent` int(10) unsigned NOT NULL DEFAULT '0',
  `peer_passkey` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `peer_peer_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `peer_ip` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `peer_port` smallint(5) unsigned NOT NULL DEFAULT '0',
  `peer_uploaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `peer_downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `peer_to_go` bigint(20) unsigned NOT NULL DEFAULT '0',
  `peer_seeder` int(1) NOT NULL,
  `peer_started` int(11) NOT NULL,
  `peer_last_action` int(11) NOT NULL,
  `peer_connectable` int(11) NOT NULL,
  `peer_userid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `peer_agent` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `peer_finishedat` int(10) unsigned NOT NULL DEFAULT '0',
  `peer_downloadoffset` bigint(20) unsigned NOT NULL DEFAULT '0',
  `peer_uploadoffset` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`peer_id`),
  UNIQUE KEY `torrent_peer_id` (`peer_torrent`,`peer_peer_id`),
  KEY `torrent` (`peer_torrent`),
  KEY `torrent_seeder` (`peer_torrent`,`peer_seeder`),
  KEY `last_action` (`peer_last_action`),
  KEY `connectable` (`peer_connectable`),
  KEY `userid` (`peer_userid`),
  KEY `passkey` (`peer_passkey`),
  KEY `torrent_connect` (`peer_torrent`,`peer_connectable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{PREFIX}pref`;
CREATE TABLE IF NOT EXISTS `{PREFIX}pref` (
  `pref_target` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pref_name` longtext COLLATE utf8_unicode_ci NOT NULL,
  `pref_value` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `{PREFIX}pref` (`pref_target`, `pref_name`, `pref_value`) VALUES
('system', 'registration', '1'),
('system', 'max_users', '1'),
('time', 'adjust', ''),
('time', 'date', ''),
('time', 'joined', 'j-F y'),
('time', 'long', 'F j Y, H:i'),
('time', 'offset', '0.0'),
('time', 'short', 'd m Y - H:i'),
('time', 'tiny', ''),
('time', 'use_relative', '1'),
('time', 'use_relative_format', '{--}, H:i'),
('website', 'name', 'openTracker'),
('website', 'cleanurls', '0'),
('website', 'noreply_email', 'no-reply@domain.com'),
('website', 'footer', 'powered by openTracker'),
('website', 'url', ''),
('system', 'template', 'default'),
('website', 'language', 'en'),
('website', 'startapp', 'news');

DROP TABLE IF EXISTS `{PREFIX}rules`;
CREATE TABLE IF NOT EXISTS `{PREFIX}rules` (
  `rule_id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_lang` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `rule_content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `rule_edited` int(20) NOT NULL,
  PRIMARY KEY (`rule_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `{PREFIX}rules` (`rule_id`, `rule_lang`, `rule_content`, `rule_edited`) VALUES
(1, 'en', '[h4]General[/h4][ul]\r\n[li]Do not defy the moderators expressed wishes![/li]\r\n[li]Disruptive behaviour in the forums will result in a warning.[/li]\r\n[/ul][h4]Downloading rules[/h4][ul]\r\n[li]Access to the newest torrents is conditional on a good ratio![/li]\r\n[li]Low ratios may result in severe consequences, including banning in extreme cases.[/li]\r\n[/ul][h4]Forum rules[/h4][ul]\r\n[li]No trashing of other peoples topics (i.e. SPAM).[/li]\r\n[li]No language other than English in the forums.[/li]\r\n[li]No systematic foul language (and none at all on  titles).[/li]\r\n[li]No links to warez or crack sites in the forums.[/li]\r\n[li]No requesting or posting of serials, CD keys, passwords or cracks in the forums.[/li]\r\n[li]No requesting if there has been no scene release in the last 7 days.[/li]\r\n[li]No bumping... (All bumped threads will be deleted.)[/li]\r\n[li]No images larger than 800x600, and preferably web-optimised.[/li]\r\n[li]No double posting. If you wish to post again, and yours is the last post\r\nin the thread please use the EDIT function, instead of posting a double.[/li]\r\n[li]Please ensure all questions are posted in the correct section![/li]\r\n[/ul][h4]Uploading rules[/h4][ul]\r\n[li]All uploads must include a proper NFO.[/li]\r\n[li]Only scene releases.[/li]\r\n[li]The release must not be older than seven (7) days.[/li]\r\n[li]All files must be in original format (usually 14.3 MB RARs).[/li]\r\n[li]Pre-release stuff should be labeled with an *ALPHA* or *BETA* tag.[/li]\r\n[li]Make sure not to include any serial numbers, CD keys or similar in the description.[/li]\r\n[li]Make sure your torrents are well-seeded for at least 24 hours.[/li]\r\n[li]Do not include the release date in the torrent name.[/li]\r\n[li]Stay active! You risk being demoted if you have no active torrents.[/li]\r\n[/ul]\r\n[color=red]By not following these rules you will lose download privileges![/color]', 0);

DROP TABLE IF EXISTS `{PREFIX}system`;
CREATE TABLE IF NOT EXISTS `{PREFIX}system` (
  `system_revision` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `{PREFIX}system` (`system_revision`) VALUES(12);

DROP TABLE IF EXISTS `{PREFIX}system_languages`;
CREATE TABLE IF NOT EXISTS `{PREFIX}system_languages` (
  `language_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `language_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `language_flag` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `language_installed` int(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `{PREFIX}system_languages` (`language_id`, `language_name`, `language_flag`, `language_installed`) VALUES
('en', 'English', 'gb.png', 1342395650);

DROP TABLE IF EXISTS `{PREFIX}torrents`;
CREATE TABLE IF NOT EXISTS `{PREFIX}torrents` (
  `torrent_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `torrent_userid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `torrent_info_hash` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `torrent_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `torrent_filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `torrent_save_as` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `torrent_search_text` text COLLATE utf8_unicode_ci NOT NULL,
  `torrent_category` int(10) unsigned NOT NULL DEFAULT '0',
  `torrent_nfo` longtext COLLATE utf8_unicode_ci NOT NULL,
  `torrent_size` bigint(20) unsigned NOT NULL DEFAULT '0',
  `torrent_added` int(11) NOT NULL,
  `torrent_type` enum('single','multi') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'single',
  `torrent_numfiles` int(10) unsigned NOT NULL DEFAULT '0',
  `torrent_times_completed` int(10) unsigned NOT NULL DEFAULT '0',
  `torrent_leechers` int(10) unsigned NOT NULL DEFAULT '0',
  `torrent_seeders` int(10) unsigned NOT NULL DEFAULT '0',
  `torrent_visible` int(1) NOT NULL,
  `torrent_last_action` int(8) NOT NULL,
  `torrent_youtube` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `torrent_imdb` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `torrent_freeleech` int(11) NOT NULL,
  PRIMARY KEY (`torrent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `{PREFIX}torrents_comments`;
CREATE TABLE IF NOT EXISTS `{PREFIX}torrents_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment_content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `comment_added` int(20) NOT NULL,
  `comment_torrent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{PREFIX}torrents_files`;
CREATE TABLE IF NOT EXISTS `{PREFIX}torrents_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_torrent` varchar(255) NOT NULL,
  `file_name` text NOT NULL,
  `file_size` int(11) NOT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `{PREFIX}torrents_imdb`;
CREATE TABLE IF NOT EXISTS `{PREFIX}torrents_imdb` (
  `imdb_id` int(11) NOT NULL AUTO_INCREMENT,
  `imdb_torrent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imdb_genres` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imdb_plot` longtext COLLATE utf8_unicode_ci NOT NULL,
  `imdb_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imdb_rating` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `imdb_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`imdb_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{PREFIX}translation`;
CREATE TABLE IF NOT EXISTS `{PREFIX}translation` (
  `translation_lang_id` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `translation_phrase` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `translation_phrase_translated` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `translation_file` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `{PREFIX}translation` (`translation_lang_id`, `translation_phrase`, `translation_phrase_translated`, `translation_file`) VALUES
('en', 'Search', '', ''),
('en', 'Name', '', ''),
('en', 'Size', '', ''),
('en', 'Uploaded', '', ''),
('en', 'No torrents found', '', ''),
('en', 'No notifications found', '', ''),
('en', 'Mailbox', '', ''),
('en', 'Friends', '', ''),
('en', 'RSS Generator', '', ''),
('en', 'Upload', '', ''),
('en', 'Admin Panel', '', ''),
('en', 'Logout', '', ''),
('en', 'Welcome', '', ''),
('en', 'Ratio', '', ''),
('en', 'Statistics', '', ''),
('en', 'Edit', '', ''),
('en', 'Delete', '', ''),
('en', 'Error 404: file not found', '', ''),
('en', 'Active', '', ''),
('en', 'Dead', '', ''),
('en', 'All', '', ''),
('en', 'Staff Members', '', ''),
('en', 'Generate RSS feed link', '', ''),
('en', 'Get RSS link', '', ''),
('en', 'Upload Torrent', '', ''),
('en', 'Torrent file', '', ''),
('en', 'NFO', '', ''),
('en', 'IMDB-link', '', ''),
('en', 'Link shall only be pointed to valid imdb', '', ''),
('en', 'Example', '', ''),
('en', 'Youtube-link', '', ''),
('en', 'Link shall only be pointed to trailer at Youtube.', '', ''),
('en', 'Website Settings', '', ''),
('en', 'Website name', '', ''),
('en', 'Template', '', ''),
('en', 'No-Reply Email', '', ''),
('en', 'Clean Urls', '', ''),
('en', 'mod_rewrite is required', '', ''),
('en', 'Timezone', '', ''),
('en', 'Time format', '', ''),
('en', 'Open registration', '', ''),
('en', 'Max users', '', ''),
('en', 'Save settings', '', ''),
('en', 'Created', '', ''),
('en', 'By', '', ''),
('en', 'Create item', '', ''),
('en', 'Application', '', ''),
('en', 'Action', '', ''),
('en', 'Move naviation item', '', ''),
('en', 'Create forum category', '', ''),
('en', 'Create forum', '', ''),
('en', 'Members', '', ''),
('en', 'Create account', '', ''),
('en', 'Username / Email', '', ''),
('en', 'Addons', '', ''),
('en', 'Addon', '', ''),
('en', 'Group', '', ''),
('en', 'Installed', '', ''),
('en', 'Log in', '', ''),
('en', 'Username', '', ''),
('en', 'Password', '', ''),
('en', 'Remember my details', '', ''),
('en', 'Sign up', '', ''),
('en', 'Recover my account', '', ''),
('en', 'Missing lang id', '', ''),
('en', 'missing data', '', ''),
('en', 'Invalid data', '', ''),
('en', 'No language installed.', '', ''),
('en', 'This language is allready installed', '', ''),
('en', 'Editing Translation', '', ''),
('en', 'Export Language', '', ''),
('en', 'E-mail', '', ''),
('en', 'A confirmation e-mail will be sent to the new e-email address if this is changed.', '', ''),
('en', 'New password', '', ''),
('en', 'Current Password', '', ''),
('en', 'Retype Password', '', ''),
('en', 'Additional', '', ''),
('en', 'Language', '', ''),
('en', 'Anonymous', '', ''),
('en', 'Passkey', '', ''),
('en', 'Reset passkey', '', ''),
('en', 'Torrents per page', '', ''),
('en', '0 = default', '', ''),
('en', 'Forum posts per page', '', ''),
('en', 'Save profile', '', ''),
('en', 'your account has been saved.', '', ''),
('en', 'Access denied', '', ''),
('en', 'minute ago', '', ''),
('en', 'minutes ago', '', ''),
('en', 'Are you sure you wish to delete this?', '', ''),
('en', 'Import language file', '', ''),
('en', 'Import', '', ''),
('en', 'Edit profile', '', ''),
('en', 'Last seen', '', ''),
('en', 'Joined', '', ''),
('en', 'days ago', '', ''),
('en', 'Downloaded', '', ''),
('en', 'Torrents', '', ''),
('en', 'Seeding', '', ''),
('en', 'Leeching', '', ''),
('en', 'Seeded for', '', ''),
('', '', '', ''),
('en', 'Settings', '', ''),
('en', 'News', '', ''),
('en', 'Navigation', '', ''),
('en', 'Forum', '', ''),
('en', 'Groups', '', ''),
('en', 'Translations', '', ''),
('en', 'FAQ / Rules', '', ''),
('en', 'Categories', '', ''),
('en', 'Widgets', '', ''),
('en', 'Widget', '', ''),
('en', 'Visible for', '', ''),
('en', 'and above', '', ''),
('en', 'Install', '', ''),
('en', 'Uploaded Torrents', '', ''),
('en', 'Seeded Torrents', '', ''),
('en', 'Dead Torrents', '', ''),
('en', 'Users', '', ''),
('en', 'Registered Users', '', ''),
('en', 'Pending Users', '', ''),
('en', 'Banned Users', '', ''),
('en', 'Forum Topics', '', ''),
('en', 'Forum Posts', '', ''),
('en', 'Uninstall', '', ''),
('en', 'Move', '', ''),
('en', 'Forum activites', '', ''),
('en', 'Forum name', '', ''),
('en', 'Topics', '', ''),
('en', 'Last post', '', ''),
('en', 'Create new topic', '', ''),
('en', 'Topic', '', ''),
('en', 'Replies', '', ''),
('en', 'Create topic in', '', ''),
('en', 'Subject:', '', ''),
('en', 'Create topic', '', ''),
('en', 'Preview', '', ''),
('en', 'wrote in', '', ''),
('en', 'Lock Topic', '', ''),
('en', 'Sticky Topic', '', ''),
('en', 'Prev', '', ''),
('en', 'Next', '', ''),
('en', 'Posted ', '', ''),
('en', 'Today', '', ''),
('en', 'Profile', '', ''),
('en', 'PM', '', ''),
('en', 'Reply', '', ''),
('en', 'Composing news post', '', ''),
('en', 'Subject', '', ''),
('en', 'Publish', '', ''),
('en', 'Startpage', '', ''),
('en', 'Default language', '', ''),
('en', 'System settings saved.', '', '');

DROP TABLE IF EXISTS `{PREFIX}users`;
CREATE TABLE IF NOT EXISTS `{PREFIX}users` (
  `user_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_password_secret` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_status` int(2) NOT NULL,
  `user_downloaded` bigint(20) NOT NULL,
  `user_uploaded` bigint(20) NOT NULL,
  `user_group` int(11) NOT NULL,
  `user_ip` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `user_last_login` int(20) NOT NULL,
  `user_last_access` int(11) NOT NULL,
  `user_passkey` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `user_uploader` int(11) NOT NULL,
  `user_added` int(11) NOT NULL,
  `user_avatar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_torrents_perpage` int(11) NOT NULL,
  `user_posts_perpage` int(11) NOT NULL,
  `user_default_categories` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_anonymous` int(11) NOT NULL,
  `user_language` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `user_invites` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `{PREFIX}users_log`;
CREATE TABLE IF NOT EXISTS `{PREFIX}users_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `log_added` bigint(20) NOT NULL,
  `log_poster` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `log_msg` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `{PREFIX}widgets`;
CREATE TABLE IF NOT EXISTS `{PREFIX}widgets` (
  `widget_id` int(11) NOT NULL AUTO_INCREMENT,
  `widget_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `widget_module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `widget_sort` int(11) NOT NULL,
  `widget_group` int(11) NOT NULL,
  PRIMARY KEY (`widget_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `{PREFIX}widgets` (`widget_id`, `widget_name`, `widget_module`, `widget_sort`, `widget_group`) VALUES
(3, 'forum', 'forums', 1, 1),
(2, 'Forum', 'torrent', 2, 1);

DROP TABLE IF EXISTS `{PREFIX}support`;
CREATE TABLE IF NOT EXISTS `{PREFIX}support` (
  `ticket_id` varchar(255) NOT NULL,
  `ticket_user` varchar(255) NOT NULL,
  `ticket_added` int(11) NOT NULL,
  `ticket_subject` varchar(255) NOT NULL,
  `ticket_status` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `{PREFIX}support_messages`;
CREATE TABLE IF NOT EXISTS `{PREFIX}support_messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `message_user` varchar(255) NOT NULL,
  `message_added` int(11) NOT NULL,
  `message_content` longtext NOT NULL,
  `message_ticket` varchar(255) NOT NULL,
  `message_unread` int(11) NOT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
