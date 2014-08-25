ALTER TABLE `#__zhyandexmaps_maps` ADD `markergroupdesc1` text NOT NULL;
ALTER TABLE `#__zhyandexmaps_maps` ADD `markergroupdesc2` text NOT NULL;
ALTER TABLE `#__zhyandexmaps_maps` ADD `markergrouptitle` varchar(255) NOT NULL default '';
ALTER TABLE `#__zhyandexmaps_maps` ADD `markergroupsep1` tinyint(1) NOT NULL default '0';
ALTER TABLE `#__zhyandexmaps_maps` ADD `markergroupsep2` tinyint(1) NOT NULL default '0';
