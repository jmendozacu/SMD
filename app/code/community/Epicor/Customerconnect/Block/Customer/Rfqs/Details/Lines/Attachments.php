<?php

/**
 * RFQ Line attachments grid container
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Attachments extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $rfq = Mage::registry('current_rfq_row');
        /* @var $rfq Epicor_Common_Model_Xmlvarien */

        $this->_controller = 'customer_rfqs_details_lines_attachments';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('customerconnect')->__('Attachments');

        if (Mage::registry('rfqs_editable') || Mage::registry('rfqs_editable_partial')) {
            $this->_addButton(
                'submit',
                array(
                'id' => 'add_line_attachment_' . $rfq->getUniqueId(),
                'label' => Mage::helper('customerconnect')->__('Add'),
                'class' => 'save rfq_line_attachment_add',
                ), -100
            );
        }
    }

    protected function _postSetup()
    {
        $this->setBoxed(false);
        parent::_postSetup();
    }

}
