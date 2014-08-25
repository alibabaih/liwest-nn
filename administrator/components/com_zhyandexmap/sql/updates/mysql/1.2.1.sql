ALTER TABLE `#__zhyandexmaps_maps` ADD `markermanager` tinyint(1) NOT NULL default '0';

ALTER TABLE `#__zhyandexmaps_markergroups` ADD `markermanagerminzoom` int(3) NOT NULL default '1';
ALTER TABLE `#__zhyandexmaps_markergroups` ADD `markermanagermaxzoom` int(3) NOT NULL default '17';

