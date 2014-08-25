ALTER TABLE `#__zhyandexmaps_routers` ADD `routebymarker` text NOT NULL;
ALTER TABLE `#__zhyandexmaps_routers` ADD `showpanel` tinyint(1) NOT NULL default '0';
ALTER TABLE `#__zhyandexmaps_routers` ADD `showpaneltotal` tinyint(1) NOT NULL default '0';
ALTER TABLE `#__zhyandexmaps_routers` ADD `showdescription` tinyint(1) NOT NULL default '0';
ALTER TABLE `#__zhyandexmaps_routers` ADD `descriptionhtml` text NOT NULL;
