<?php
class Silk_Retailer_Model_Method_Free extends Mage_Payment_Model_Method_Free{
	public function isAvailable($quote = null)
	{
		return true;
	}
}
