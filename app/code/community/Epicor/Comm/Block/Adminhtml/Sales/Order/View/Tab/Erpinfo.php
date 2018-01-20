<?php
 
class Epicor_Comm_Block_Adminhtml_Sales_Order_View_Tab_Erpinfo
extends Mage_Adminhtml_Block_Template
implements Mage_Adminhtml_Block_Widget_Tab_Interface {
 protected $_chat = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('epicor_comm/sales/order/view/tab/erpinfo.phtml');
    }

    public function getTabLabel() {
        return $this->__('ERP Order Information');
    }

    public function getTabTitle() {
        return $this->__('ERP Order Information');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

    public function getOrder(){
        return Mage::registry('current_order');
    }
	
	public function getErpOrderNumber(){
		return $this->getOrder()->getErpOrderNumber() ? $this->getOrder()->getErpOrderNumber() : "-";
	}
	
	public function getManuallySet(){
		return strpos($this->getOrder()->getGorMessage(), 'Manually set to :') !== false;
	}
	
	public function getStatuses(){
		return array(
			'0' => 'Order Not Sent',
			'1' => 'Order Sent',
			'3' => 'Erp Error',
		);
	}
}