<?php

/**
 * RFQ contacts grid container
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Contacts extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'customer_rfqs_details_contacts';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('customerconnect')->__('Contacts');

        if (Mage::registry('rfqs_editable')) {
            $this->_addButton(
                'submit',
                array(
                'id' => 'add_contact',
                'label' => Mage::helper('customerconnect')->__('Add'),
                'class' => 'add',
                ), -100
            );
        }
    }

    protected function _postSetup()
    {
        $this->setBoxed(true);
        parent::_postSetup();
    }

}
