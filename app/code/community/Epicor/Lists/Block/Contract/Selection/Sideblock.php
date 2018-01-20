<?php

/**
 * Quick add block
 * 
 * Displays the quick add to Cart / wishlist block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Contract_Selection_Sideblock extends Mage_Core_Block_Template
{

    private $_contracts;
    /* @var $_helper Epicor_Lists_Helper_Frontend_Contract */
    private $_helper;

    public function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('Contract Selector'));
        $this->_helper = Mage::helper('epicor_lists/frontend_contract');
    }

    public function getContracts()
    {
        if (!$this->_contracts) {
            $contracts = $this->_helper->getActiveContracts();
            $sortedContracts = array();
            foreach ($contracts as $contract) {
                $sortedContracts[$contract->getTitle() . $contract->getId()] = $contract;
            }

            ksort($sortedContracts);
            $this->_contracts = $sortedContracts;
        }

        return $this->_contracts;
    }

    public function getSessionContract()
    {
        $contractHelper = Mage::helper('epicor_lists/frontend_contract');
        /* @var $contractHelper Epicor_Lists_Helper_Frontend_Contract */

        return $contractHelper->getSelectedContract();
    }

    public function getReturnUrl()
    {
        $url = Mage::helper('core/url')->getCurrentUrl();
        return Mage::helper('epicor_comm')->urlEncode($url);
    }

    /**
     * Returns whether the sidebar block can be shown
     * 
     * @return boolean
     */
    public function showSideBarBlock()
    {
        $contractHelper = Mage::helper('epicor_lists/frontend_contract');
        /* @var $contractHelper Epicor_Lists_Helper_Frontend_Contract */

        if ($contractHelper->contractsDisabled()) {
            return false;
        }

        $contracts = $this->getContracts();
        if (count($contracts) <= 1) {
            return false;
        }

        return true;
    }

    /**
     * Returns whether No Contract Selected option should be shown
     * 
     * @return boolean
     */
    public function showNoContractOption()
    {
        $contractHelper = Mage::helper('epicor_lists/frontend_contract');
        /* @var $contractHelper Epicor_Lists_Helper_Frontend_Contract */

        $requiredType = $contractHelper->requiredContractType();

        return in_array($requiredType, array('E', 'O'));
    }

    /**
     * Checks to see if we need an onclick on the change contract button
     * 
     * @return string
     */
    public function addOnClick()
    {
        $onClick = '';
        $quote = Mage::getSingleton('checkout/cart')->getQuote();
        /* @var $quote Epicor_Comm_Model_Quote */
        if ($quote->hasItems()) {
            $message = Mage::helper('epicor_comm')->__('Changing Contract may remove items from the cart that are not valid for the selected Contract. Do you wish to continue?');
            $onClick = ' onclick="return confirm(\'' . $message . '\');" ';
        }

        return $onClick;
    }

}
