CREATE TABLE IF NOT EXISTS `#__jsecurelog` (
	`id` int(11) NOT NULL auto_increment,
	`date` datetime NOT NULL,
	`ip` varchar(16) NOT NULL,
	`userid` int(11) NOT NULL default '0',
	`code` varchar(255) NOT NULL,
	`change_variable` text NOT NULL,
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;