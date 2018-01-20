<?php

class Epicor_SalesRep_Block_Adminhtml_Customer_Salesrep_Edit_Tab_Hierarchy extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {//Mage_Adminhtml_Block_Widget_Grid{

    public function __construct() {
        parent::__construct();
        $this->setId('salesrepGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
    }
    
    public function canShowTab() {
        return true;
    }

    public function getTabLabel() {
        return 'Hierarchy';
    }

    public function getTabTitle() {
        return 'Hierarchy';
    }

    public function isHidden() {
        return false;
    }
    
    protected function _toHtml(){
        $html = parent::_toHtml();
        $html .= Mage::app()->getLayout()->createBlock('epicor_salesrep/adminhtml_customer_salesrep_edit_tab_hierarchy_parents');
        
        return $html;
    }

}
