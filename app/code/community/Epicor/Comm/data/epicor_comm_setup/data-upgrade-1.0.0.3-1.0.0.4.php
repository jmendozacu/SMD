<?php


        $erp_accounts = Mage::getModel('epicor_comm/customer_erpaccount')->getCollection();

        foreach ($erp_accounts->getItems() as $erp_account) {
            /* @var $erp_account Epicor_Comm_Model_Customer_Erpaccount */

            $currency_code = $erp_account->getData('currency_code') ?: Mage::app()->getBaseCurrencyCode();
            $erp_account->addCurrency($currency_code);
            $erp_account->setOnstop($erp_account->getData('onstop'), $currency_code);
            $erp_account->setBalance($erp_account->getData('balance'), $currency_code);
            $erp_account->setCreditLimit($erp_account->getData('credit_limit'), $currency_code);
            $erp_account->setUnallocatedCash($erp_account->getData('unallocated_cash'), $currency_code);
            $erp_account->setLastPaymentDate($erp_account->getData('last_payment_date'), $currency_code);
            $erp_account->setLastPaymentValue($erp_account->getData('last_payment_value'), $currency_code);
            $erp_account->setMinOrderAmount($erp_account->getData('min_order_amount'), $currency_code);
            $erp_account->setIsDefault(true, $currency_code);
            
            $erp_account->save();
        }
