<?php

/**
 * RFQ details attachments grid container
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Attachments extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'customer_rfqs_details_attachments';
        $this->_blockGroup = 'customerconnect';
        if (Mage::getStoreConfig('customerconnect_enabled_messages/CRQD_request/attachment_support')) {
            $this->_headerText = Mage::helper('customerconnect')->__('Attachments');
            if (Mage::registry('rfqs_editable') || Mage::registry('rfqs_editable_partial')) {
                $this->_addButton(
                        'submit', array(
                    'id' => 'add_attachment',
                    'label' => Mage::helper('customerconnect')->__('Add'),
                    'class' => 'add',
                        ), -100
                );
            }
        } else {
            $this->_headerText = '';
        }
    }

    protected function _postSetup()
    {
        $this->setBoxed(true);
        parent::_postSetup();
    }

}
