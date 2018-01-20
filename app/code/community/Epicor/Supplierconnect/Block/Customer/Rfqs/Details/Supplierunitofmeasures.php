<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Supplierunitofmeasures extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'customer_rfqs_details_supplierunitofmeasures';
        $this->_blockGroup = 'supplierconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Supplier Unit Of Measure');

        if (Mage::registry('rfq_editable') && Mage::registry('allow_conversion_override')) {
            $this->_addButton('submit', array(
                'id' => 'add_suom',
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
