<?php

/**
 *  RFQ sales rep grid holder
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Salesreps extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'customer_rfqs_details_salesreps';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('customerconnect')->__('Salesreps');

        $editable = false;

        if ($editable && Mage::registry('rfqs_editable')) {
            $this->_addButton('submit',
                array(
                'id' => 'add_salesrep',
                'label' => Mage::helper('customerconnect')->__('Add'),
                'class' => 'add',
                ), -100);
        }
    }

    protected function _postSetup()
    {
        $this->setBoxed(true);
        parent::_postSetup();
    }

}
