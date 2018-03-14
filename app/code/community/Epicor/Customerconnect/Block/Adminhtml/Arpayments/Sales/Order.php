<?php
/**
 * AR Payments Admin Screen
 * @category   Epicor
 * @package    Epicor_ErpSimulator
 * @author     Epicor Websales Team
 * 
 * 
 */
class Epicor_Customerconnect_Block_Adminhtml_Arpayments_Sales_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'customerconnect';
        $this->_controller = 'adminhtml_arpayments_sales_order';
        $this->_headerText = 'AR Payments';
        parent::__construct();
        $this->_removeButton('add');
    }
}