<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$rs_result = $db->getRow("SELECT `pe_rsVersion`, `pe_rsApiScriptVersion` FROM ".TABLE_PROVENEXPERT_RICHSNIPPETS." WHERE `pe_rsActive` = 1");
$file = _SRV_WEBROOT._SRV_WEB_PLUGINS.'provenexpert/cache/provenexpert_richsnippet_v'.(string)$rs_result['pe_rsVersion'].'.html';
$fallbackFile = _SRV_WEBROOT._SRV_WEB_PLUGINS.'provenexpert/library/richsnippet_fallback.php';
if(!empty($rs_result) && file_exists($file)) {
    if(!(time() > (filemtime($cacheRichSnippet) + (30*60)))) {
        echo file_get_contents($file);
    }
    elseif(file_exists($fallbackFile)) {
        include_once $fallbackFile;
        $authData = getApiAuthDataFallback();
        if(!empty($authData)) {
            $answer = getRichSnippetFallback($authData['apiId'], $authData['apiKey'], $rs_result['pe_rsVersion'], $rs_result['rsApiScriptVersion'] = "1.7");
        }
        if(isset($answer['status']) && $answer['status'] == 'success') {
            echo $answer['image'];
        }
    }
}

$widget_result = $db->getOne("SELECT `id` FROM ".TABLE_PROVENEXPERT_WIDGETS." WHERE `pe_widgetActive` = 1");
if($widget_result == 1) {
    $file = _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'provenexpert/cache/provenexpert_widget_v' . (string)$widget_result . '.html';
    if (! empty($widget_result) && file_exists($file)) {
        echo file_get_contents($file);
    }
}