ALTER TABLE `#__zhyandexmaps_maps` ADD `clusterdisableclickzoom` tinyint(1) NOT NULL default '0';
ALTER TABLE `#__zhyandexmaps_maps` ADD `clustersynchadd` tinyint(1) NOT NULL default '0';
ALTER TABLE `#__zhyandexmaps_maps` ADD `clusterorderalphabet` tinyint(1) NOT NULL default '0';
ALTER TABLE `#__zhyandexmaps_maps` ADD `clustergridsize` int(5) NOT NULL default '64';
ALTER TABLE `#__zhyandexmaps_maps` ADD `clusterminclustersize` int(5) NOT NULL default '1';
