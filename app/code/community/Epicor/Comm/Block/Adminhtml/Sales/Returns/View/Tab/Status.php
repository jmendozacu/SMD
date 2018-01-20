<?php

class Epicor_Comm_Block_Adminhtml_Sales_Returns_View_Tab_Status extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function __construct() {
        parent::_construct();
        $this->_title = 'Status';
        $this->setTemplate('epicor_comm/sales/returns/view/status.phtml');
    }

    /**
     * 
     * @return Epicor_Comm_Model_Customer_Return
     */
    public function getReturn() {
        return Mage::registry('return');
    }

    public function getErpSyncStatus() {
        $return = $this->getReturn();
        $status = '';
        if ($return->getErpSyncStatus() == 'N') {
            $status = $this->__('Not Sent to ERP');
        } else if ($return->getErpSyncStatus() == 'E') {
            $status = $this->__('Error Sending to ERP');
        } else if ($return->getErpSyncStatus() == 'S') {
            $status = $this->__('Sent to ERP Successfully');
        }
        
        return $status;
    }

    public function canShowTab() {
        return true;
    }

    public function getTabLabel() {
        return $this->_title;
    }

    public function getTabTitle() {
        return $this->_title;
    }

    public function isHidden() {
        return false;
    }

}
