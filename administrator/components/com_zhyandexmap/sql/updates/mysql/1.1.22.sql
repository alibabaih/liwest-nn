ALTER TABLE `#__zhyandexmaps_markers` ADD `markergroup` int(11) NOT NULL default '0';

CREATE TABLE `#__zhyandexmaps_markergroups` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `title` varchar(250) NOT NULL default '',
  `icontype` varchar(50) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;



ALTER TABLE `#__zhyandexmaps_markergroups` ADD `overridemarkericon` tinyint(1) NOT NULL default '0';

ALTER TABLE `#__zhyandexmaps_maps` ADD `markergroupcontrol` tinyint(1) NOT NULL default '0';

ALTER TABLE `#__zhyandexmaps_maps` ADD `markergroupwidth` int(5) NOT NULL default '20';

ALTER TABLE `#__zhyandexmaps_maps` ADD `markergroupshowicon` tinyint(1) NOT NULL default '0';