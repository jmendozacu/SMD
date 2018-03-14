<?php

/**
 * Customer ERP Account Summary Block
 * 
 * @author gareth.james
 */
class Epicor_Customerconnect_Block_Customer_Account_Summary extends Mage_Customer_Block_Account
{

    private $_erp_customer;

    public function __construct()
    {
        //this is a workaround to allow external scripts that run $layout->generateBlocks to work
        if(Mage::app()->getFrontController()->getAction()){            
            parent::__construct();          // needed in customerconnect dashboard
        }
        if (Mage::registry('customerconnect_dashboard_ok')) {
            $this->setDisplayDashboard(true);
        }
        if ($this->isErpCustomer()) {
            // this section enables the default currency amount to be displayed in the account summary in brackets 
            $this->setBaseCurrencyCode(Mage::app()->getStore()->getBaseCurrencyCode());
            $this->setCurrentCurrencyCode(Mage::app()->getStore()->getCurrentCurrencyCode());
            if ($this->getBaseCurrencyCode() != $this->getCurrentCurrencyCode()) {
                $this->setConversionRequired(true);
                $baseCurrencyCode = $this->getBaseCurrencyCode();
                $currentCurrencyCode = $this->getCurrentCurrencyCode();

                if ($this->getCreditLimit() == $this->__("No Limit"))
                    $this->setConvertedCreditLimit($this->__("No Limit"));
                else {
                    $creditLimitNoCurrency = Mage::helper('customerconnect')->removeCurrencyCodePrefix($this->getCreditLimit());
                    $this->setConvertedCreditLimit(Mage::helper('customerconnect')->getCurrencyConvertedAmount($creditLimitNoCurrency, $this->getCurrentCurrencyCode(), $this->getBaseCurrencyCode()));
                }
                $balanceNoCurrency = Mage::helper('customerconnect')->removeCurrencyCodePrefix($this->getBalance());
                if ($balanceNoCurrency != (0.00 && 0)) {
                    $this->setConvertedBalance(Mage::helper('customerconnect')->getCurrencyConvertedAmount($balanceNoCurrency, $this->getCurrentCurrencyCode(), $this->getBaseCurrencyCode()));
                }
            }
        }
    }

    /**
     * Gets the ERP Account for the current customer
     * 
     * @return Epicor_Comm_Model_Customer_Erpaccount
     */
    public function getErpCustomer()
    {
        if (!$this->_erp_customer) {
            $commHelper = Mage::helper('epicor_comm');
            /* @var $commHelper Epicor_Comm_Helper_Data */
            $this->_erp_customer = $commHelper->getErpAccountInfo();
            /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
        }
        return $this->_erp_customer;
    }

    /**
     * Is current Customer an ERP Customer
     * 
     * @return bool
     */
    public function isErpCustomer()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return (!is_null($customer->getErpaccountId()) || $customer->isSalesRep());
    }

    /**
     * Returns the erp account credit limit in the store base currency code
     * 
     * @return string
     */
    public function getCreditLimit()
    {
        $store_currency = Mage::app()->getStore()->getBaseCurrencyCode();
        $credit_limit = $this->getErpCustomer()->getCreditLimit($store_currency);
        if (is_null($credit_limit)) {
            $displayed_credit = $this->__("No Limit");
        } else {
            $displayed_credit = $this->getLocalisedAmount($credit_limit);
        }
        return $displayed_credit;
    }
    public function getMinOrderValue()
    {
    
        $store_currency = Mage::app()->getStore()->getBaseCurrencyCode();
        $min_order_value = $this->getErpCustomer()->getCurrencyData($store_currency)->getMinOrderAmount();
        if (is_null($min_order_value)) {
            $displayed_mov = $this->__("No Limit");
        } else {
            $displayed_mov = $this->getLocalisedAmount($min_order_value);
        }
        return $displayed_mov;
    }

    /**
     * Returns the erp account balance in the store base currency code
     * 
     * @return string
     */
    public function getBalance()
    {
        $store = Mage::app()->getStore();
        return $this->getLocalisedAmount($this->getErpCustomer()->getBalance($store->getBaseCurrencyCode()));
    }

    /**
     * Localises an amount to the current store currency
     * 
     * @param float $amount - amount to convert
     * 
     * @return string - amount localised to store currency
     */
    public function getLocalisedAmount($amount)
    {

        $store = Mage::app()->getStore();
        if (empty($amount) && $amount !== 0)
            $output = $this->__('N/A');
        else {
            $helper = Mage::helper('customerconnect');

            /* @var $helper Mage_Core_Helper_Abstract */
            $output = $helper->getCurrencyConvertedAmount(
                    $amount, $store->getBaseCurrencyCode(), $store->getCurrentCurrencyCode()
            );
        }
        return $output;
    }

    /**
     * Returns whether to show a field on the page or not
     * 
     * @param string $field - field name to check
     * 
     * @return boolean
     */
    public function showField($field)
    {
        // customer check
        // erp account check
        // global check
        if (Mage::getStoreConfigFlag('customerconnect/customer_account_summary/show_' . $field)) {
            return true;
        }

        return false;
    }

}
