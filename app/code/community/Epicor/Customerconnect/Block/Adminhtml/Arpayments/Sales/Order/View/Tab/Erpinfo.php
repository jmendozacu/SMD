<?php
/**
 * AR Payments Admin Screen 
 * @category   Epicor
 * @package    Epicor_ErpSimulator
 * @author     Epicor Websales Team
 * 
 * 
 */
class Epicor_Customerconnect_Block_Adminhtml_Arpayments_Sales_Order_View_Tab_Erpinfo extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_chat = null;
    
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('epicor_common/arpayments/sales/order/view/tab/erpinfo.phtml');
    }
    
    public function getTabLabel()
    {
        return $this->__('ERP Payment Information');
    }
    
    public function getTabTitle()
    {
        return $this->__('ERP Payment Information');
    }
    
    public function canShowTab()
    {
        return true;
    }
    
    public function isHidden()
    {
        return false;
    }
    
    public function getOrder()
    {
        return Mage::registry('current_order');
    }
    
    public function getErpOrderNumber()
    {
        return $this->getOrder()->getErpOrderNumber() ? $this->getOrder()->getErpOrderNumber() : "-";
    }
    
    public function getManuallySet()
    {
        return strpos($this->getOrder()->getGorMessage(), 'Manually set to :') !== false;
    }
    
    public function getStatuses()
    {
        return array(
            '0' => 'Payment Not Sent',
            '1' => 'Payment Sent',
            '3' => 'Erp Error'
        );
    }
}