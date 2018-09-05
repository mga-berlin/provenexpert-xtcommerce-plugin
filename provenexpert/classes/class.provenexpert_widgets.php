<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'provenexpert/library/provenexpert_functions.php';

class provenexpert_widgets {
    protected $_table = TABLE_PROVENEXPERT_WIDGETS;
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

                $params['exclude'] = array(
                    'pe_width',
                    'pe_feedback',
                    'pe_slider',
                    'pe_fixed',
                    'pe_origin',
                    'pe_position',
                    'pe_side',
                    'pe_viewport',
                    'pe_style',
                    'pe_avatar',
                    'pe_competence'
                );
                $header['pe_widgetActive'] = array('type' => 'status');
                $header['pe_type'] = array('type' => 'textfield', 'readonly' => true);
            } else {

                $rowActions[] = array('iconCls' => 'provenexpert', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_PE_PREVIEW);
                if ($this->url_data['edit_id']) {
                    $js = "var edit_id = " . $this->url_data['edit_id'] . ";\n";
                }
                else {
                    $js = "var edit_id = record.id;";
                }
                $js .= "addTab('adminHandler.php?load_section=provenexpert_widgetPreview&plugin=provenexpert&pg=overview&pe_widgetVersion='+edit_id,'".TEXT_PE_PREVIEW."')";
                $rowActionsFunctions['pe_preview'] = $js;
                $params['rowActions']             = $rowActions;
                $params['rowActionsFunctions']    = $rowActionsFunctions;

                $apianswer = getWidget($authData['apiId'], $authData['apiKey'], $this->url_data['edit_id'], true); 
                if($apianswer['status'] == 'error') {
                    echo '<div style ="text-align: center; padding-top: 12%; color:red; font-size: 200%;";>';
                    echo TEXT_PE_API_UNREACHABLE;
                    echo '</div>';
                    die();
                }
                $header['pe_widgetActive'] = array('type' => 'status');
                $header['pe_type'] = array('type' => 'textfield', 'readonly'=>true);
                $params['exclude'] = array(
                    'pe_width',
                    'pe_slider',
                    'pe_fixed',
                    'pe_origin',
                    'pe_position',
                    'pe_side',
                    'pe_viewport'
                );
                $header['pe_feedback'] =  array('type' => 'status');
                $header['pe_style']    =  array('type' => 'dropdown', 'url' => 'DropdownData.php?get=pe_styles');

                if($this->url_data['edit_id'] == 1) {
                    $params['exclude'] = array_merge($params['exclude'], array('pe_avatar', 'pe_competence'));
                } else {
                    $header['pe_avatar']        =  array('type' => 'status');
                    $header['pe_competence']    =  array('type' => 'status');
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

        $params['display_deleteBtn']  = false;
        $params['display_searchPanel']  = false;
        $params['display_newBtn'] = false;
        $params['header']         = $header;
        $params['master_key']     = $this->_master_key;
        
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

        foreach(array('pe_width', 'pe_feedback', 'pe_slider', 'pe_fixed', 'pe_origin', 'pe_position', 'pe_side', 'pe_viewport', 'pe_style', 'pe_avatar', 'pe_competence') as $columns) {
            if(!isset($data[$columns])) {
                $data[$columns] = 0;
            }
        }
        if(!isset($data['pe_widgetActive'])) {
            $data['pe_widgetActive'] = 0;
        } else {
            $active_version_in_db = $db->getOne("SELECT `id` FROM ".TABLE_PROVENEXPERT_WIDGETS." WHERE `pe_widgetActive` = 1");
            if(!empty($active_version_in_db) && (int)$data['id'] != (int)$active_version_in_db) {
                $db->Execute("UPDATE ".TABLE_PROVENEXPERT_WIDGETS." set `pe_widgetActive` = 0 WHERE `id` = ".$active_version_in_db);
            }
        }

        $obj = new stdClass;
        $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        $obj = $o->saveDataSet();

        $obj->success = true;

        $authData   = getApiAuthData();
        if($authData != false) {
            getWidget($authData['apiId'], $authData['apiKey'], $this->url_data['edit_id'], false);
        }
        return $obj;
    }
}
?>