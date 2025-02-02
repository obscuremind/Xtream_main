<?php
set_time_limit(0);
require str_replace('\\', '/', dirname($argv[0])) . '/../../wwwdir/init.php';

$ipTV_db->query("CREATE TABLE IF NOT EXISTS `servers_stats` (`id` int(11) NOT NULL AUTO_INCREMENT, `server_id` int(11) DEFAULT '0', `connections` int(11) DEFAULT '0', `streams` int(11) DEFAULT '0', `users` int(11) DEFAULT '0', `cpu` float DEFAULT '0', `cpu_cores` int(11) DEFAULT '0', `cpu_avg` float DEFAULT '0', `total_mem` int(11) DEFAULT '0', `total_mem_free` int(11) DEFAULT '0', `total_mem_used` int(11) DEFAULT '0', `total_mem_used_percent` float DEFAULT '0', `total_disk_space` bigint(20) DEFAULT '0', `uptime` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `total_running_streams` int(11) DEFAULT '0', `bytes_sent` bigint(20) DEFAULT '0', `bytes_received` bigint(20) DEFAULT '0', `bytes_sent_total` bigint(128) DEFAULT '0', `bytes_received_total` bigint(128) DEFAULT '0', `cpu_load_average` float DEFAULT '0', `gpu_info` mediumtext COLLATE utf8_unicode_ci, `iostat_info` mediumtext COLLATE utf8_unicode_ci, `time` int(16) DEFAULT '0', `total_users` int(11) DEFAULT '0', PRIMARY KEY (`id`) USING BTREE) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
$ipTV_db->query("CREATE TABLE IF NOT EXISTS `queue` (`id` int(11) NOT NULL AUTO_INCREMENT, `type` varchar(32) DEFAULT NULL, `server_id` int(11) DEFAULT NULL, `stream_id` int(11) DEFAULT NULL, `pid` int(11) DEFAULT NULL, `added` int(11) DEFAULT NULL, PRIMARY KEY (`id`) USING BTREE) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
$ipTV_db->query("CREATE TABLE IF NOT EXISTS `rtmp_ips` (`id` int(11) NOT NULL AUTO_INCREMENT, `ip` varchar(255) DEFAULT NULL, `password` varchar(128) DEFAULT NULL, `notes` mediumtext, `push` tinyint(1) DEFAULT NULL, `pull` tinyint(1) DEFAULT NULL, PRIMARY KEY (`id`), UNIQUE KEY `ip` (`ip`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
$ipTV_db->query("ALTER TABLE streaming_servers ADD COLUMN `sysctl` mediumtext COLLATE utf8_unicode_ci DEFAULT NULL");
$ipTV_db->query("ALTER TABLE streaming_servers ADD COLUMN `video_devices` mediumtext COLLATE utf8_unicode_ci");
$ipTV_db->query("ALTER TABLE streaming_servers ADD COLUMN `audio_devices` mediumtext COLLATE utf8_unicode_ci");
$ipTV_db->query("ALTER TABLE streaming_servers ADD COLUMN `gpu_info` mediumtext COLLATE utf8_unicode_ci");
$ipTV_db->query("ALTER TABLE streaming_servers ADD COLUMN `limit_requests` INT(11) NULL DEFAULT '0';");
$ipTV_db->query("ALTER TABLE streaming_servers ADD COLUMN `enable_gzip` TINYINT(1) NULL DEFAULT '0';");
$ipTV_db->query("ALTER TABLE streams ADD COLUMN `fps_restart` tinyint(1) DEFAULT '0';");
$ipTV_db->query("ALTER TABLE streams ADD COLUMN `vframes_server_id` int(11) DEFAULT '0';");
$ipTV_db->query("ALTER TABLE streams ADD COLUMN `vframes_pid` int(11) DEFAULT '0';");
$ipTV_db->query("INSERT INTO `settings` (`name`, `value`) VALUES ('restart_php_fpm', '1')");
$ipTV_db->query("INSERT INTO `settings` (`name`, `value`) VALUES ('max_encode_movies', '10')");
$ipTV_db->query("INSERT INTO `settings` (`name`, `value`) VALUES ('max_encode_cc', '1')");
$ipTV_db->query("INSERT INTO `settings` (`name`, `value`) VALUES ('queue_loop', '1')");
$ipTV_db->query("INSERT INTO `settings` (`name`, `value`) VALUES ('redis_password', '')");
$ipTV_db->query("INSERT INTO `settings` (`name`, `value`) VALUES ('redis_handler', '0')");
$ipTV_db->query("INSERT INTO `settings` (`name`, `value`) VALUES ('cache_playlists', '0')");
$ipTV_db->query("INSERT INTO `settings` (`name`, `value`) VALUES ('php_loopback', '1')");



return true;
