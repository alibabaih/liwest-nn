CREATE TABLE `#__zhyandexmaps_paths` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `title` varchar(250) NOT NULL default '',
  `path` text NOT NULL,
  `mapid` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;
