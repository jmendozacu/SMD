<?php

/**
 * Locations override of wrapper tab
 *
 * @author Paul.Ketelle
 */
class Epicor_Comm_Block_Adminhtml_Customer_Edit_Tab_Locations_Wrapper extends Epicor_Common_Block_Adminhtml_Widget_Tab_Wrapper
{

    /**
     * 
     * @return Epicor_Comm_Model_Customer
     */
    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = Mage::registry('current_customer');
        }
        return $this->_customer;
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return $this->getCustomer()->isCustomer(false);
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return !$this->getCustomer()->isCustomer(false);
    }

}
