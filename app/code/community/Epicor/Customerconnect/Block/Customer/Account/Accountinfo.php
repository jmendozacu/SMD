<?php

class Epicor_Customerconnect_Block_Customer_Account_Accountinfo extends Epicor_Customerconnect_Block_Customer_Info
{

    protected $_account_data;
    protected $_helper;

    public function __construct()
    {
        parent::__construct();

        $details = Mage::registry('customer_connect_account_details');

        if ($details) {

            $accountData = $details->getAccount();

            $helper = Mage::helper('customerconnect');
            
            $accountCurrency = Mage::helper('epicor_comm/messaging')->getCurrencyMapping($accountData->getCurrencyCode(),Epicor_Comm_Helper_Messaging::ERP_TO_MAGENTO);
            
            // this section enables the default currency amount to be displayed in the account summary in brackets 
            $this->setBaseCurrencyCode(Mage::app()->getStore()->getBaseCurrencyCode());
            if ($this->getBaseCurrencyCode() != $accountCurrency) {
                $this->setConversionRequired(true);
                $baseCurrencyCode = $this->getBaseCurrencyCode();
                //$accountCurrencyCode = $accountData->getCurrencyCode();
//                $this->setConvertedCreditLimit($helper->getCurrencyConvertedAmount($accountData->getCreditLimit(), $accountData->getCurrencyCode(), $this->getBaseCurrencyCode()));
                if ($accountData->getBalance() != (0.00 && 0)) {
                    $balance = $helper->getCurrencyConvertedAmount($accountData->getBalance(), $accountCurrency);
//                    $this->setConvertedBalance($helper->getCurrencyConvertedAmount($accountData->getBalance(), $accountData->getCurrencyCode(), $this->getBaseCurrencyCode()));
                }
            }
//             if($this->getConvertedBalance()){
//                $this->_infoData = array(
//                    $this->__('Balance :') => $balance." (".$this->getConvertedBalance().")"
//                );
//            }else{
            $this->_infoData = array(
                $this->__('Balance :') => $helper->getCurrencyConvertedAmount($accountData->getBalance(), $accountCurrency)
            );
//            };

            if (is_null($accountData->getCreditLimit())) {
                $credit_limit = $this->__("No Limit");
            } else {
//                if ($this->getConvertedCreditLimit()) {
//                    $credit_limit = $helper->getCurrencyConvertedAmount($accountData->getCreditLimit(), $accountData->getCurrencyCode())
//                            . " (" . $this->getConvertedCreditLimit() . ")";
//                } else {
                $credit_limit = $helper->getCurrencyConvertedAmount($accountData->getCreditLimit(), $accountCurrency);
//                };
            }
            $this->_infoData[$this->__('Credit Limit :')] = $credit_limit;

            $this->_infoData[$this->__('Unallocated Cash :')] = $helper->getCurrencyConvertedAmount($accountData->getUnallocatedCash(), $accountCurrency);

            if ($accountData->getLastPayment()) {
                $this->_infoData[$this->__('Last Payment Date :')] = $accountData->getLastPayment()->getDate() ? $this->getHelper()->getLocalDate($accountData->getLastPayment()->getDate(), Epicor_Common_Helper_Data::DAY_FORMAT_MEDIUM, false) : $this->__('N/A');
                $this->_infoData[$this->__('Last Payment Value :')] = $helper->getCurrencyConvertedAmount($accountData->getLastPayment()->getValue(), $accountCurrency);
            }

            $this->_infoData[$this->__('Period to Date Purchases :')] = $helper->getCurrencyConvertedAmount($accountData->getPeriodToDatePurchases(), $accountCurrency);
            $this->_infoData[$this->__('Current Year Purchases :')] = $helper->getCurrencyConvertedAmount($accountData->getCurrentYearPurchases(), $accountCurrency);
            $this->_infoData[$this->__('Min Order Value :')] = ($accountData->getMinOrderValue() == ('0' || '')) ? 'N/A' : $helper->getCurrencyConvertedAmount($accountData->getMinOrderValue(), $accountCurrency);
        }
        
        $this->setTitle($this->__('Account Information :'));
        $this->setColumnCount(1);
        $this->setOnRight(true);
    }

}
