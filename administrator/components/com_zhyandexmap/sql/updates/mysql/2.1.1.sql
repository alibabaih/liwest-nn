ALTER TABLE `#__zhyandexmaps_maps` ADD `minzoom` int(3) NOT NULL default '1';
ALTER TABLE `#__zhyandexmaps_maps` ADD `maxzoom` int(3) NOT NULL default '200';

ALTER TABLE `#__zhyandexmaps_markers` ADD `markercontent` tinyint(1) NOT NULL default '0';