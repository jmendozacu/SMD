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
class Epicor_Comm_Block_Customer_Account_Masquerade extends Mage_Core_Block_Template
{

    private $_actions;
    private $_childAccounts;

    /**
     *
     * @var Epicor_Comm_Model_Customer_Erpaccount 
     */
    private $_erpAccount;

    /**
     *
     * @var Epicor_Comm_Model_Customer_Erpaccount 
     */
    private $_masqueradeAccount;

    /**
     *
     * @var Epicor_Comm_Helper_Data 
     */
    private $_helper;

    public function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('Account Selector'));
        $this->_helper = Mage::helper('epicor_comm');
    }

    public function getActualAccount()
    {
        if (is_null($this->_erpAccount)) {
            $this->_erpAccount = $this->_helper->getErpAccountInfo(null, 'customer', null, false);
        }

        return $this->_erpAccount;
    }

    public function getMasqueradeAccount()
    {
        if (is_null($this->_masqueradeAccount)) {
            $this->_masqueradeAccount = $this->_helper->getErpAccountInfo();
        }

        return $this->_masqueradeAccount;
    }

    public function isMasquerading()
    {
        return $this->_helper->isMasquerading();
    }

    public function isAllowed()
    {
        $customerSession = Mage::getSingleton('customer/session');
        /* @var $customerSession Mage_Customer_Model_Session */
        $customer = $customerSession->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */

        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
            $erpAccount = $this->getActualAccount();
        
        $allowed = false;

        if (($customer->isMasqueradeAllowed() && !$erpAccount->isDefaultForStore())) {

            $children = $this->getChildAccounts();
            if (count($children) > 0) {
                $allowed = true;
            }
        }
        
        return $allowed;
    }

    public function showCartOptions()
    {
        $actions = $this->getCartActions();
        return count($actions) > 0;
    }

    public function getChildAccounts()
    {
        if (is_null($this->_childAccounts)) {
            $this->_childAccounts = array();

            /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
            $erpAccount = $this->getActualAccount();
            $this->_childAccounts = $erpAccount->getChildAccounts('', true);
        }

        return $this->_childAccounts;
    }

    public function getCartActions()
    {
        if (is_null($this->_actions)) {

            $this->_actions = array();

            $customerSession = Mage::getSingleton('customer/session');
            /* @var $customerSession Mage_Customer_Model_Session */
            $customer = $customerSession->getCustomer();
            /* @var $customer Epicor_Comm_Model_Customer */

            if ($customer->isMasqueradeAllowed()) {
                if ($customer->isMasqueradeCartClearAllowed()) {
                    $this->_actions['clear'] = $this->__('Clear');
                }

                if ($customer->isMasqueradeCartRepriceAllowed()) {
                    $this->_actions['reprice'] = $this->__('Reprice');
                }
            }

            if (empty($this->_actions)) {
                $action = Mage::getStoreConfig('epicor_comm_erp_accounts/masquerade/default_cart_action');

                $this->_actions[$action] = ($action == 'clear') ? $this->__('Clear') : $this->__('Reprice');
            }
        }

        return $this->_actions;
    }

    public function getReturnUrl()
    {
        $url = Mage::helper('core/url')->getCurrentUrl();
        return Mage::helper('epicor_comm')->urlEncode($url);
    }

}
