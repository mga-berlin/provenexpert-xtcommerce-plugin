<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

class provenexpert_api {
    protected $_table = TABLE_PROVENEXPERT;
    protected $_table_lang = NULL;
    protected $_table_seo = NULL;
    protected $_master_key = 'id';

    function setPosition ($position) {
        $this->position = $position;
    }

    function _getParams() {

        $params = array();
        $header = array();
        $header[$this->_master_key]     = array('type' => 'hidden');
        $header['pe_apiId']             = array('type' => 'textfield');
        $header['pe_apiKey']            = array('type' => 'password');

        $params['display_deleteBtn']    = false;
        $params['display_searchPanel']  = false;
        $params['display_newBtn']       = false;
        $params['header']               = $header;
        $params['master_key']           = $this->_master_key;

        return $params;
    }

    function _get($ID = 0) {

        if ($this->position != 'admin') return false;

        if ($ID === 'new') {
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        }

        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key);

        if ($this->url_data['get_data']){
            $data = $table_data->getData();
        }elseif($ID){
            $data = $table_data->getData($ID);
        }else{
            $data = $table_data->getHeader();
        }

        if($table_data->_total_count!=0 || !$table_data->_total_count)
            $count_data = $table_data->_total_count;
        else
            $count_data = count($data);

        $obj = new stdClass;
        $obj->totalCount = $count_data;
        $obj->data = $data;

        return $obj;
    }

    function _set($data, $set_type='edit'){

        $obj = new stdClass;
        $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        $obj = $o->saveDataSet();

        $obj->success = true;

        return $obj;
    }
}
?>