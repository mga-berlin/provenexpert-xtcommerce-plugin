<?php
//Fallback falls Cronjob zum Aktualisieren des RichSnippet fehlschlaegt


function getApiAuthDataFallback() {
    global $db;
    $result = $db->getArray("SELECT `pe_apiId`, `pe_apiKey` FROM ".TABLE_PROVENEXPERT." LIMIT 1");
    if($result[0]['pe_apiId'] == '' || $result[0]['pe_apiKey'] == '' || strlen($result[0]['pe_apiId']) < 30 || strlen($result[0]['pe_apiKey']) < 40) {
        return false;
    }
    else {
        return array('apiId' => $result[0]['pe_apiId'], 'apiKey' => $result[0]['pe_apiKey']);
    }
}

function peCURLFallback($url, $api_id, $api_key, $data) {

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

function getRichSnippetFallback($apiId, $apiKey, $rsVersion = 1, $rsApiScriptVersion = "1.7") {

        if((int)$rsVersion > 0) {
        $result[0]['pe_rsVersion'] = $rsVersion;
    }
        $domain = 'www.provenexpert.com';
        $url = 'https://'.$domain.'/api_rating_v'.((int)$rsVersion + 1).'.json?v='.$rsApiScriptVersion;


        $apiAnswer = peCURLFallback($url, $apiId, $apiKey, '');
        if($apiAnswer->status == 'success') {
            $html = str_replace("#pe_rating{display:inline-block;", "#pe_rating{display:block;", (string)$apiAnswer->aggregateRating);
            $answer['image'] = $html;
            $answer['status'] = 'success';
        }
        else {
            $answer['status'] = 'error';
            $answer['errors'] = $apiAnswer->errors[0];
        }
    return $answer;
}