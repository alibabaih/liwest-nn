ALTER TABLE `#__zhyandexmaps_routers` ADD `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__zhyandexmaps_routers` ADD `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__zhyandexmaps_paths` ADD `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__zhyandexmaps_paths` ADD `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__zhyandexmaps_markergroups` ADD `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__zhyandexmaps_markergroups` ADD `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';


