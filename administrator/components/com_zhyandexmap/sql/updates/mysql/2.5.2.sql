ALTER TABLE `#__zhyandexmaps_markers` CHANGE `icontype` `icontype` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `#__zhyandexmaps_markergroups` CHANGE `icontype` `icontype` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `#__zhyandexmaps_maps` ADD `preseticontype` varchar(250) NOT NULL default '';
ALTER TABLE `#__zhyandexmaps_markers` ADD `preseticontype` varchar(250) NOT NULL default '';
