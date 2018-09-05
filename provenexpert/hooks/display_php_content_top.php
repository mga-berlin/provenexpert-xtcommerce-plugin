<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$widget_result = $db->getOne("SELECT `id` FROM ".TABLE_PROVENEXPERT_WIDGETS." WHERE `pe_widgetActive` = 1");
if($widget_result == 2) {
    $file = _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'provenexpert/cache/provenexpert_widget_v' . (string)$widget_result . '.html';
    if (! empty($widget_result) && file_exists($file)) {
        echo file_get_contents($file);
    }
}