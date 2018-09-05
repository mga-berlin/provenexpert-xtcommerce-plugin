<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

class provenexpert_widgetPreview
{
    protected $_table = TABLE_PROVENEXPERT_WIDGETS;

    function setPosition ($position) {
        $this->position = $position;
    }

    function _getParams() {
        if(!empty((int)$_GET['pe_widgetVersion'])) {
            echo '<div style="padding-top: 12%">';
            $file = _SRV_WEBROOT._SRV_WEB_PLUGINS.'provenexpert/cache/provenexpert_widget_v'.(int)$_GET['pe_widgetVersion'].'.html';
            if(file_exists($file)) {
                if ((int)$_GET['pe_widgetVersion'] != 1) {
                    echo file_get_contents($file);
                }
                else {
                    $htmlcode = file_get_contents($file);
                    $start = strpos($htmlcode, 'src=');
                    $length = strpos($htmlcode, '></script>') - $start - 6;
                    $jsurl = substr($htmlcode, $start + 7, $length - 2);
                    $jsurlclean = str_replace('amp;', '', 'http://'.$jsurl);
                    $jscode = str_replace('position:fixed', 'position:static', file_get_contents($jsurlclean));
                    $htmlcode = str_replace(' src="//'.$jsurl.'"', '', $htmlcode);
                    echo str_replace('></script>', '>'.$jscode.'</script>', $htmlcode);
                }
            }
        }
        echo '</div>';
        die();
    }

    function _get($ID = 0) {
        if ($ID === 'new') {
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        }
        return $obj;
    }
}