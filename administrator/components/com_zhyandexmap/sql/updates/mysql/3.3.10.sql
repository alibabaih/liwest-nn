ALTER TABLE `#__zhyandexmaps_paths` ADD `descriptionhtml` text NOT NULL;
ALTER TABLE `#__zhyandexmaps_paths` ADD `opacity` varchar(20) NOT NULL default '';
ALTER TABLE `#__zhyandexmaps_paths` ADD `infowincontent` tinyint(1) NOT NULL default '0';
ALTER TABLE `#__zhyandexmaps_paths` ADD `actionbyclick` tinyint(1) NOT NULL default '0';
ALTER TABLE `#__zhyandexmaps_paths` ADD `objecttype` tinyint(1) NOT NULL default '0';
ALTER TABLE `#__zhyandexmaps_paths` ADD `fillcolor` varchar(250) NOT NULL default '';
ALTER TABLE `#__zhyandexmaps_paths` ADD `fillopacity` varchar(20) NOT NULL default '';
ALTER TABLE `#__zhyandexmaps_paths` ADD `radius` varchar(250) NOT NULL default '';
ALTER TABLE `#__zhyandexmaps_paths` ADD `geodesic` tinyint(1) NOT NULL default '0';

