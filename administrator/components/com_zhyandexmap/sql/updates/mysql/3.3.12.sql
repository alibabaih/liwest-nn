CREATE TABLE `#__zhyandexmaps_maptypes` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `title` varchar(250) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `gettileurl` text NOT NULL,
  `tilewidth` int(5) NOT NULL default '256',
  `tileheight` int(5) NOT NULL default '256',
  `opacity` varchar(20) NOT NULL default '',
  `projection` int(3) NOT NULL default '0',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;
