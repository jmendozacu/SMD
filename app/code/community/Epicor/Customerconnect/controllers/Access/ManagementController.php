<?php

/**
 * 
 * Customer Access Groups management controller
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Access_ManagementController extends Epicor_Common_Controller_Access_Management_Abstract {

    /**
     * Loads the erp account for this customer
     * 
     * @return Epicor_Comm_Model_Customer_Erpaccount
     */
    protected function loadErpAccount() {

        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */

        $erpAccount = $helper->getErpAccountInfo();
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
        
        Mage::register('access_erp_account',$erpAccount);

        return $erpAccount;
    }

}
