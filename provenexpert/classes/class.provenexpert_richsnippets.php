<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'provenexpert/library/provenexpert_functions.php';

class provenexpert_richsnippets {
    protected $_table = TABLE_PROVENEXPERT_RS;
    protected $_table_lang = NULL;
    protected $_table_seo = NULL;
    protected $_master_key = 'id';

    function setPosition ($position) {
        $this->position = $position;
    }

    function _getParams() {

        $params = array();
        $header = array();
        $header[$this->_master_key] = array('type' => 'hidden');

        $authData   = getApiAuthData();
        if($authData != false) {

            if (!$this->url_data['edit_id']) {
                $header['pe_rsActive']          = array('type' => 'status');
                $params['exclude']              = array('pe_rsApiScriptVersion', 'pe_rsVersion');
            } else {
                $rsData = getRichSnippet($authData['apiId'], $authData['apiKey'], $this->url_data['edit_id']);
                if(isset($rsData['status']) && $rsData['status'] == 'success') {


                    $rowActions[] = array('iconCls' => 'provenexpert', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_PE_PREVIEW);
                    if ($this->url_data['edit_id']) {
                        $js = "var edit_id = " . $this->url_data['edit_id'] . ";\n";
                    }
                    else {
                        $js = "var edit_id = record.id;";
                    }
                    $js .= "addTab('adminHandler.php?load_section=provenexpert_rsPreview&plugin=provenexpert&pg=overview&edit_id='+edit_id,'".TEXT_PE_PREVIEW."')";
                    $rowActionsFunctions['pe_preview'] = $js;
                    $params['rowActions']              = $rowActions;
                    $params['rowActionsFunctions']     = $rowActionsFunctions;
                    
                    $header['pe_rsActive']                  = array('type' => 'status');
                    $header['pe_rsApiScriptVersion']        = array('type' => 'textfield');
                    $header['pe_rsVersion']                 = array('type' => 'textfield', 'readonly' => true);
                }
                else {
                    echo '<div style ="text-align: center; padding-top: 12%; color:red; font-size: 200%;";>';
                    echo TEXT_PE_API_UNREACHABLE;
                    echo '</div>';
                    $params['exclude']              = array('id', 'pe_rsActive', 'pe_rsApiScriptVersion', 'pe_rsVersion');
                    $params['display_editBtn']      = false;
                    die();
                }
            }

        } else {
            echo '<div style ="text-align: center; padding-top: 12%; color:red; font-size: 200%;";>';
            echo TEXT_PE_API_AUTH_MISSING;
            echo '</div>';
            $params['exclude']              = array('id', 'pe_rsActive', 'pe_rsApiScriptVersion', 'pe_rsVersion');
            $params['display_editBtn']      = false;
            die();
        }

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
        global $db;

        foreach(array('pe_rsApiScriptVersion') as $columns) {
            if(!isset($data[$columns])) {
                $data[$columns] = NULL;
            }
        }

        $data['pe_rsVersion'] = $this->url_data['edit_id'];


        if(!isset($data['pe_rsActive'])) {
            $data['pe_rsActive'] = 0;
        } else {
            $active_version_in_db = $db->getOne("SELECT `pe_rsVersion` FROM ".TABLE_PROVENEXPERT_RICHSNIPPETS." WHERE `pe_rsActive` = 1");
            if(!empty($active_version_in_db) && (int)$data['pe_rsVersion'] != (int)$active_version_in_db) {
                $db->Execute("UPDATE ".TABLE_PROVENEXPERT_RICHSNIPPETS." set `pe_rsActive` = 0 WHERE `pe_rsVersion` = ".$active_version_in_db);
            }
        }
        
        $obj = new stdClass;
        $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        $obj = $o->saveDataSet();

        $obj->success = true;

        $authData = getApiAuthData();
        if($authData != false) {
            getRichSnippet($authData['apiId'], $authData['apiKey'], $data['pe_rsVersion']);
        }

        return $obj;
    }
}
?>