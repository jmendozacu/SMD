<?php

/**
 * 
 * Customer grid for customer selector input
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Customer_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_customer_attribute';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Customers');

        $this->addButton(20, array('label' => 'Cancel', 'onclick' => "accountSelector.closepopup()"), 1);

        parent::__construct();
        $this->removeButton('add');
    }

}
