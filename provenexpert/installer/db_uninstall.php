<?php

global $db;

$db->Execute("DELETE FROM `".TABLE_ADMIN_NAVIGATION."` WHERE `text` LIKE 'provenexpert%'");

$db->Execute("DELETE FROM `".TABLE_CRON."` WHERE `cron_note` LIKE 'provenexpert%'");

$db->Execute("DROP TABLE IF EXISTS `".DB_PREFIX."_provenexpert_richsnippets`;");

$db->Execute("DROP TABLE IF EXISTS `".DB_PREFIX."_provenexpert_widgets`;");