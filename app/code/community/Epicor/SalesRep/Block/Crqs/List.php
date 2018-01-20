<?php

/**
 * Customer RFQ list
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Crqs_List extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'crqs_list';
        $this->_blockGroup = 'epicor_salesrep';
        $this->_headerText = Mage::helper('customerconnect')->__('RFQs');

        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        $msgHelper = Mage::helper('epicor_comm/messaging');
        /* @var $msgHelper Epicor_Comm_Helper_Messaging */
        $enabled = $msgHelper->isMessageEnabled('customerconnect', 'crqu');
        
        if ($enabled &&  $msgHelper->isMasquerading() && $helper->customerHasAccess('Epicor_Customerconnect', 'Rfqs', 'new', '', 'Access')) {
            $url = Mage::getUrl('customerconnect/rfqs/new/');
            $this->_addButton(
                'new',
                array(
                'label' => Mage::helper('customerconnect')->__('New Quote'),
                'onclick' => 'setLocation(\'' . $url . '\')',
                'class' => 'save',
                ), 10
            );
        }
    }

}
