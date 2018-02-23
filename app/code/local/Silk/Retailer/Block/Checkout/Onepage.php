<?php
class Silk_Retailer_Block_Checkout_Onepage extends Epicor_Comm_Block_Checkout_Onepage
{
  public function getSteps()
    {	
	$steps = array();
	if(Mage::getSingleton('customer/session')->isLoggedIn() && Mage::helper('epicor_comm')->getErpAccountInfo()->getAccountType() == 'B2B'){
		$stepCodes = array('billing', 'shipping', 'shipping_method','shipping_dates', 'payment', 'review');
		//$stepCodes = parent::_getStepCodes();
	}
	else{

        	if (!$this->isCustomerLoggedIn()) {
            		$steps['login'] = $this->getCheckout()->getStepData('login');
        	}

        	$stepCodes = array('billing', 'shipping', 'review');
	}
        foreach ($stepCodes as $step) {
            $steps[$step] = $this->getCheckout()->getStepData($step);
        }
        return $steps;
	
    }
}
