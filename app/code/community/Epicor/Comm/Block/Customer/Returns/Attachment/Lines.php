<?php

/**
 * Customer Orders list
 */
class Epicor_Comm_Block_Customer_Returns_Attachment_Lines extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'customer_returns_attachment_lines';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Attachments');

        if (!Mage::registry('review_display')) {
            $this->_addButton(
                'submit',
                array(
                'id' => 'add_customer_returns_attachment_lines',
                'label' => Mage::helper('customerconnect')->__('Add'),
                'class' => 'save return_attachments_add',
                ), -100
            );
        }
    }

    protected function _postSetup()
    {
        if (Mage::registry('details_display')) {
            $this->setBoxed(true);
        }
        parent::_postSetup();
    }

}
