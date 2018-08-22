<?php
class Silk_Retailer_Model_Method_Free extends Mage_Payment_Model_Method_Free{
	public function isAvailable($quote = null)
	{
		if(Mage::getSingleton('customer/session')->isLoggedIn() && Mage::helper('epicor_comm')->getErpAccountInfo()->getAccountType() == 'B2B'){
			//return true;
			$quote = Mage::getModel('checkout/session')->getQuote();
			$quoteData= $quote->getData();
			$grandTotal=$quoteData['grand_total'];
			if($grandTotal == 0){
				return true;
			}
			else{
				return false;
			} 
		}
		return true;
	}
}
