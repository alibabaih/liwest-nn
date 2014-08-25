ALTER TABLE `#__zhyandexmaps_maptypes` ADD `overlay` tinyint(1) NOT NULL default '0';

UPDATE `#__zhyandexmaps_maps` SET `clusterminclustersize`=2 WHERE `clusterminclustersize`=1;
UPDATE `#__zhyandexmaps_maps` SET `clusterzoom`=10 WHERE `clusterzoom` IN (0, 23);

