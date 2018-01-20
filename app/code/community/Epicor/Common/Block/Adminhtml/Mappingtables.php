<?php
class Epicor_Common_Block_Adminhtml_Mappingtables extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
         $this->setTemplate('epicor_common/comm_settings_backup/mappingTables.phtml');
         $_mappingTables = array('payment' => array('module' => 'epicor_comm', 'entity' => 'erp_mapping_payment', 'key' =>  array('magento_code'=>''), 'id' => 'id'),
                'country' => array('module' => 'epicor_comm', 'entity' => 'erp_mapping_country', 'key' => array('erp_code'=>''), 'id' => 'id'),
                'currency' => array('module' => 'epicor_comm', 'entity' => 'erp_mapping_currency', 'key' => array('erp_code'=>''), 'id' => 'id'),
                'orderstatus' => array('module' => 'epicor_comm', 'entity' => 'erp_mapping_orderstatus', 'key' => array('code'=>''), 'id' => 'id'),
                'shippingmethod' => array('module' => 'epicor_comm', 'entity' => 'erp_mapping_shippingmethod', 'key' => array('shipping_method'=>''), 'id' => 'id'),
                'cardtype' => array('module' => 'epicor_comm', 'entity' => 'erp_mapping_cardtype', 'key' => array('payment_method'=>''), 'id' => 'id'),
                'languages' => array('module' => 'epicor_common', 'entity' => 'erp_mapping_language', 'key' => array('erp_code'=>''), 'id' => 'id'),
                'erporderstatus' => array('module' => 'customerconnect', 'entity' => 'erp_mapping_erporderstatus', 'key' => array('code'=>''), 'id' => 'id'),
                'invoicestatus' => array('module' => 'customerconnect', 'entity' => 'erp_mapping_invoicestatus', 'key' => array('code'=>''), 'id' => 'id'),
                'rmastatus' => array('module' => 'customerconnect', 'entity' => 'erp_mapping_rmastatus', 'key' => array('code'=>''), 'id' => 'id'),
                'servicecallstatus' => array('module' => 'customerconnect', 'entity' => 'erp_mapping_servicecallstatus', 'key' => array('code'=>''), 'id' => 'id'),
                'quotestatus' => array('module' => 'customerconnect', 'entity' => 'erp_mapping_erpquotestatus', 'key' => array('code'=>''), 'id' => 'id'),
                'languages' => array('module' => 'epicor_common', 'entity' => 'erp_mapping_language', 'key' => array('erp_code'=>''), 'id' => 'id'),
                'taxclass' => array('module' => 'tax', 'entity' => 'class', 'key' => array('class_name' =>'' , 'class_type'=>''), 'id' => 'class_id'),

                'configdata' => array('module' => 'core', 'entity' => 'config_data', 'key' => array('scope' =>'' , 'scope_id'=>'', 'path' =>''), 'id' => 'config_id')
            );
         $this->setMappingTables(json_encode($_mappingTables));
    }
 
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Import / Export Comm Settings');
    }
}