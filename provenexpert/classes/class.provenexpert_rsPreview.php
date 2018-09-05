<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

class provenexpert_rsPreview
{
    protected $_table = TABLE_PROVENEXPERT_RS;
    protected $_table_lang = NULL;
    protected $_table_seo = NULL;
    protected $_master_key = 'id';

    function setPosition ($position) {
        $this->position = $position;
    }

    function _getParams() {

        if(!empty((int)$_GET['edit_id'])) {
            $file = _SRV_WEBROOT._SRV_WEB_PLUGINS.'provenexpert/cache/provenexpert_richsnippet_v'.(int)$_GET['edit_id'].'.html';
            if(file_exists($file)) {
                echo '<div style="position: absolute; top: 43%; left: 40%;">';
                echo file_get_contents($file);
                echo '</div>';
            }
        }
        die();
    }

    function _get($ID = 0) {
        if ($this->position != 'admin') return false;

        if ($ID === 'new') {
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        }
        return $obj;

    }
}