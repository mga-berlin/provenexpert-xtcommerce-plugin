<?php

global $db;

$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (text, icon, url_i, url_d, sortorder, parent, type, navtype)
			  VALUES ('provenexpert', '../plugins/provenexpert/images/provenexpert.png', NULL, NULL, 6000, 'shop', 'G', 'W')
			  ON DUPLICATE KEY UPDATE type = 'I'");

$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (text, icon, url_i, url_d, sortorder, parent, type, navtype)
			  VALUES ('provenexpert_api_hl', '../plugins/provenexpert/images/icons/api.png', '&plugin=provenexpert&load_section=provenexpert_api&edit_id=1', 'adminHandler.php', 1000, 'provenexpert', 'I', 'W')
			  ON DUPLICATE KEY UPDATE type = 'I'");

$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (text, icon, url_i, url_d, sortorder, parent, type, navtype)
			  VALUES ('provenexpert_rs_hl', '../plugins/provenexpert/images/icons/rs.png', '&plugin=provenexpert&load_section=provenexpert_richsnippets', 'adminHandler.php', 3000, 'provenexpert', 'I', 'W')
			  ON DUPLICATE KEY UPDATE type = 'I'");

$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (text, icon, url_i, url_d, sortorder, parent, type, navtype)
			  VALUES ('provenexpert_widgets_hl', '../plugins/provenexpert/images/icons/seals.png', '&plugin=provenexpert&load_section=provenexpert_widgets', 'adminHandler.php', 4000, 'provenexpert', 'I', 'W')
			  ON DUPLICATE KEY UPDATE type = 'I'");

$db->Execute("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."_provenexpert` (
                                                                    `id`                        int(10)         NOT NULL AUTO_INCREMENT,
                                                                    `pe_apiId`                  varchar(50)     DEFAULT NULL,
                                                                    `pe_apiKey`                 varchar(50)     DEFAULT NULL,
                                                                    PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

$db->Execute("INSERT INTO `".DB_PREFIX."_provenexpert` (`pe_apiId`, `pe_apiKey`)
			                                    VALUES ('',         '')
			  ON DUPLICATE KEY UPDATE `pe_apiId` = ''");

$db->Execute("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."_provenexpert_richsnippets` (
                                                                    `id`                    int(11)         NOT NULL,
                                                                    `pe_rsActive`           int(3)          NOT NULL DEFAULT '0',
                                                                    `pe_rsVersion`          int(3)          DEFAULT NULL,
                                                                    `pe_rsApiScriptVersion` varchar(3)      DEFAULT NULL,
                                                                    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

$db->Execute("INSERT INTO `xt_provenexpert_richsnippets` (`id`,   `pe_rsActive`,  `pe_rsApiScriptVersion`,    `pe_rsVersion`)
                                    VALUES               (1,      0,              '1.7',                      1),
                                                         (2,      0,              '1.7',                      2),
                                                         (3,      0,              '1.7',                      3),
                                                         (4,      0,              '1.7',                      4)
                                                         ON DUPLICATE KEY UPDATE `pe_rsApiScriptVersion` = '1.7';");

$db->Execute("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."_provenexpert_widgets` (
																	`id`                        int(10)         NOT NULL AUTO_INCREMENT,
																	`pe_widgetActive`           int(3)          NOT NULL DEFAULT '0',
																	`pe_type`                   varchar(30)     DEFAULT NULL UNIQUE,
																	`pe_style`                  varchar(20)     DEFAULT NULL,
																	`pe_width`                  int(11)         DEFAULT NULL,
																	`pe_feedback`               int(11)         DEFAULT NULL,
																	`pe_slider`                 int(11)         DEFAULT NULL,
																	`pe_fixed`                  int(11)         DEFAULT NULL,
																	`pe_origin`                 varchar(20)     DEFAULT NULL,
																	`pe_position`               int(11)         DEFAULT NULL,
																	`pe_side`                   varchar(20)     DEFAULT NULL,
																	`pe_viewport`               int(11)         DEFAULT NULL,
																	`pe_avatar`                 int(11)         DEFAULT NULL,
																	`pe_competence`             int(11)         DEFAULT NULL,
																	PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

$db->Execute("INSERT INTO `".DB_PREFIX."_provenexpert_widgets`
            (`pe_widgetActive`, `pe_type`,      `pe_width`, `pe_feedback`,  `pe_style`, `pe_avatar`,    `pe_competence`)
    VALUES  (0,                 'bar',          NULL,       0,              'white',    NULL,           NULL),
            (0,                 'landing',      NULL,       1,              'black',    1,              1)
            ON DUPLICATE KEY UPDATE `pe_position` = NULL;");