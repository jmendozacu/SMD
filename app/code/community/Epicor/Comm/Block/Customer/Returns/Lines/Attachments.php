<?php

/**
 * Return Line attachments grid container
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Lines_Attachments extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $line = Mage::registry('current_return_line');
        /* @var $rfq Epicor_Comm_Model_Customer_Return_Line */

        $this->_controller = 'customer_returns_lines_attachments';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Attachments');

        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */
        $allowed = ($return) ? $return->isActionAllowed('Attachments') : true;
        
        if (!Mage::registry('review_display') && $allowed) {
            $this->_addButton(
                'submit',
                array(
                'id' => 'add_return_line_attachments_' . $line->getUniqueId(),
                'label' => Mage::helper('epicor_comm')->__('Add'),
                'class' => 'save return_line_attachment_add attachments_add',
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
