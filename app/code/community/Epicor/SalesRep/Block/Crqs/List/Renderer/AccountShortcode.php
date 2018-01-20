<?php

/**
 * Sales Rep CRQS Account short code Renderer
 * @category   Epicor
 * @package    Epicor_SalesRep
 */
class Epicor_SalesRep_Block_Crqs_List_Renderer_AccountShortcode extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        /* @var $row returns Account number */
        $accountNumber = $row->getData($this->getColumn()->getIndex());
        return $this->checkERPAccountRegister($accountNumber);
    }

    public function checkERPAccountRegister($accountNumber)
    {
       
        $helper = Mage::helper('epicor_comm'); /* @var $helper Epicor_Comm_Helper_Data */
        $company = $helper->getStoreBranding()->getCompany();
        $uomSep = $helper->getUOMSeparator();
        $registerName = $accountNumber . $company . $uomSep;

        if (Mage::registry($registerName) == '') { //check if short code is not in registry, get from collection and add to registry
            $shortCodeObj = Mage::getModel('epicor_comm/customer_erpaccount')
                ->getCollection()
                ->addFieldToFilter('account_number', array('eq' => $accountNumber))
                ->addFieldToFilter('erp_code', array('like' => $company . $uomSep . '%'))
                ->addFieldToSelect('short_code');

            $shortCode = $shortCodeObj->getFirstItem()->getShortCode();
            if ($shortCode != '') { //if shortcode available, adds shortcode to registry
                Mage::register($registerName, $shortCode);
            } else { //if shortcode not available, adds row value to registry
                Mage::register($registerName, $accountNumber);
            }
        }
        return Mage::registry($registerName); // retrun registry
    }

}
