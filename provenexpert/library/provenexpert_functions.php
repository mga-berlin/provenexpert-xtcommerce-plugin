<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

function peCURL($url, $api_id, $api_key, $data) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERPWD, $api_id.':'.$api_key);
    curl_setopt($ch, CURLOPT_POST, 1);
    if(is_array($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    $result = json_decode(curl_exec($ch));
    curl_close($ch);

    return $result;
}

function cacheHTML($text, $cacheUrl) {

    if(is_writable('../plugins/provenexpert/cache/')) {
        file_put_contents($cacheUrl, $text);
    }
    else {
        echo TEXT_PE_CACHE_UNWRITABLE;
    }
}

function keyAllowed($key, $widgetVersion) {
    switch($widgetVersion) {
        case 1:             //bar
            if(in_array($key, array('pe_type', 'pe_style', 'pe_feedback'))) {
                return true;
            } else {
                return false;
            }
            break;
        case 2:             //landing
            if(in_array($key, array('pe_type', 'pe_style', 'pe_feedback', 'pe_avatar', 'pe_competence'))) {
                return true;
            } else {
                return false;
            }
            break;
        default:
            return false;
    }
}

function getRichSnippet($apiId, $apiKey, $rsVersion = 1) {

    global $db;
    $cacheRichSnippet  = '../plugins/provenexpert/cache/provenexpert_richsnippet_v'.(int)$rsVersion.'.html';

    $result = $db->getArray("SELECT `pe_rsActive`, `pe_rsApiScriptVersion`, `pe_rsVersion` FROM ".TABLE_PROVENEXPERT_RICHSNIPPETS." WHERE `pe_rsVersion` = '".$rsVersion."' LIMIT 1");
    if((int)$rsVersion > 0) {
        $result[0]['pe_rsVersion'] = $rsVersion;
    }
        if((!file_exists($cacheRichSnippet)) || time() > (filemtime($cacheRichSnippet) + (60*60))) {

            $domain = 'www.provenexpert.com';
            $url = 'https://'.$domain.'/api_rating_v'.((int)$result[0]['pe_rsVersion'] + 1).'.json?v='.$result[0]['pe_rsApiScriptVersion'];

            $apiAnswer = peCURL($url, $apiId, $apiKey, '');
            if($apiAnswer->status == 'success') {
                $html = str_replace("#pe_rating{display:inline-block;", "#pe_rating{display:block;", (string)$apiAnswer->aggregateRating);
                cacheHTML($html, $cacheRichSnippet);
                $answer['image'] = $cacheRichSnippet;
                $answer['status'] = 'success';
            }
            else {
                $answer['status'] = 'error';
                $answer['errors'] = $apiAnswer->errors[0];
            }
        }
        else {
            $answer['image'] = $cacheRichSnippet;
            $answer['status'] = 'success';
        }
    return $answer;
}

function getWidget($apiId, $apiKey, $widgetVersion = 0, $usecache = false) {

    global $db;
    $cacheWidget       = '../plugins/provenexpert/cache/provenexpert_widget_v'.$widgetVersion.'.html';

    $result = $db->getAll("SELECT * FROM ".TABLE_PROVENEXPERT_WIDGETS." WHERE `id` = ".(int)$widgetVersion." LIMIT 1");

        if(!$usecache || (!file_exists($cacheWidget)) || time() > (filemtime($cacheWidget) + (24*60*60))) {

            $data = array('data' => array());

            foreach($result[0] as $key => $value) {
                if($value != NULL && keyAllowed($key, $widgetVersion)) {
                    $data['data'][substr($key, 3)] = $value;
                }
            }
            $domain = 'www.provenexpert.com';
            $url = 'https://'.$domain.'/api/v1/widget/create';

            $apiAnswer = peCURL($url, $apiId, $apiKey, $data);
            if($apiAnswer->status == 'success') {
                $answer['status'] = 'success';
                $html = (string)$apiAnswer->html;

                if ($widgetVersion == 1 && !strpos($html, '<style')) {
                    $html = str_replace(
                        '<!-- ProvenExpert Bewertungssiegel -->',
                        '<!-- ProvenExpert Bewertungssiegel --><style type="text/css">@media(max-width:767px){#ProvenExpert_widgetbar_container {display:none;}}@media(min-width:768px){html {padding-bottom: 44px; box-sizing: border-box;}}</style>',
                        $html
                    );
                }
                $html = str_replace('<style type="text/css">', '<style type="text/css">{literal}', $html);
                $html = str_replace('</style>', '{/literal}</style>', $html);
                if ($widgetVersion != 1 || strpos($html, '<style') > 0) {
                    cacheHTML($html, $cacheWidget);
                }
            }
            else {
                $answer['status'] = 'error';
                $answer['errors'] = $apiAnswer->errors[0];
            }
        }
        else {
            $answer['status'] = 'success';
        }
    return $answer;
}

function getApiAuthData() {
    global $db;
    $result = $db->getArray("SELECT `pe_apiId`, `pe_apiKey` FROM ".TABLE_PROVENEXPERT." LIMIT 1");
    if($result[0]['pe_apiId'] == '' || $result[0]['pe_apiKey'] == '' || strlen($result[0]['pe_apiId']) < 30 || strlen($result[0]['pe_apiKey']) < 40) {
        return false;
    }
    else {
        return array('apiId' => $result[0]['pe_apiId'], 'apiKey' => $result[0]['pe_apiKey']);
    }
}