<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Pricebreaks extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'customer_rfqs_details_pricebreaks';
        $this->_blockGroup = 'supplierconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Quantity Price Breaks');

        if (Mage::registry('rfq_editable')) {
            $this->_addButton('submit', array(
                'id' => 'add_price_break',
                'label' => Mage::helper('supplierconnect')->__('Add'),
                'class' => 'save',
                    ), -100);
        }
    }

    protected function _postSetup()
    {
        $this->setBoxed(true);
        parent::_postSetup();
    }

}
