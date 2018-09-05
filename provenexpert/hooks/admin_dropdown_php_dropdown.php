<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($request['get']=='pe_styles') {
    if (!isset($result)) $result = array();
    $result[] = array('id' => 'black', 'name' => 'black');
    $result[] = array('id' => 'white', 'name' => 'white');
}

?>