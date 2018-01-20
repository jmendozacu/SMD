<?php

/**
 * Customer Returns list
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Returns_List
        extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'customer_returns_list';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('customerconnect')->__('Returns');
        
        $accessHelper = Mage::helper('epicor_common/access');
        /* @var $accessHelper Epicor_Common_Helper_Access */
        
        $returnHelper = Mage::helper('epicor_comm/returns');
        /* @var $returnHelper Epicor_Comm_Helper_Returns */
        
        $allowed = $returnHelper->checkConfigFlag('allow_create');
       
        if ($allowed && $accessHelper->customerHasAccess('Epicor_Comm', 'Returns', 'index', '', 'Access')) {
            $url = Mage::getUrl('epicor_comm/returns/');
            $this->_addButton(
                'new',
                array(
                'label' => Mage::helper('customerconnect')->__('New Return'),
                'onclick' => 'setLocation(\'' . $url . '\')',
                'class' => 'save',
                ), 10
            );
        }
    }

}
