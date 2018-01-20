<?php

/**
 * Generic grid list for use with  messages
 * 
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Generic_List_Search extends Epicor_Common_Block_Generic_List_Grid {

    protected $_configLocation = 'grid_config';
    
    public function __construct() {
        parent::__construct();

        $this->setSaveParametersInSession(true);
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
    }

    protected function initColumns() {

        $columnConfig = unserialize(Mage::getStoreConfig($this->getMessageBase() . '_enabled_messages/' . strtoupper($this->getMessageType()) . '_request/'.$this->_configLocation));

        $columns = array();

        foreach ($columnConfig as $column) {
            if ($column['filter_by'] == 'none') {
                $column['filter'] = false;
            } else {
                unset($column['filter']);
            }

            if ($column['type'] == 'options' && !empty($column['options'])) {
                $column['options'] = Mage::getModel($column['options'])->toGridArray();
            } else if (isset($column['options'])) {
                unset($column['options']);
            }
            
            if($column['type'] == 'number'){
                $column['align'] = 'right';
            }

            $columns[$column['index']] = $column;
        }
        $this->setCustomColumns($columns);
    }

}