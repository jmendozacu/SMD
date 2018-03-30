<?php
class Silk_Retailer_Model_Method_Free extends Mage_Payment_Model_Method_Free{
	public function isAvailable($quote = null)
	{
		if(Mage::getSingleton('customer/session')->isLoggedIn() && Mage::helper('epicor_comm')->getErpAccountInfo()->getAccountType() == 'B2B'){
			return false;
		}
		return true;
	}
}
