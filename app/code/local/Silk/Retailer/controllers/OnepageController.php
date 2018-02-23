<?php
require_once('Epicor' . DS . 'Comm' . DS . 'controllers' . DS . 'OnepageController.php');
class Silk_Retailer_OnepageController extends Epicor_Comm_OnepageController
{
	public function saveBillingAction()
	{
		if(Mage::getSingleton('customer/session')->isLoggedIn() && Mage::helper('epicor_comm')->getErpAccountInfo()->getAccountType() == 'B2B')
		{
			parent::saveBillingAction();
		}
	     else{
		if ($this->_expireAjax()) {
			return;
		}
		if ($this->getRequest()->isPost()) {
			//            $postData = $this->getRequest()->getPost('billing', array());
			//            $data = $this->_filterPostData($postData);
			$data = $this->getRequest()->getPost('billing', array());
			$customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
			$result = $this->getOnepage()->saveBilling($data, $customerAddressId);
			if (!isset($result['error'])) {
				if ($this->getOnepage()->getQuote()->isVirtual() || (isset($data['use_for_shipping']) && $data['use_for_shipping']==1) ) {
					//$data['use_for_shipping'] == 1
					if(!$this->getOnepage()->getQuote()->isVirtual()){
						//$method = 'freeshipping_freeshipping';
						$method = 'flatrate_flatrate';
						$result = $this->getOnepage()->saveShippingMethod($method);
					}
					if (!isset($result['error'])) {
						try{
							$data = array('method'=>'free');
							$result = $this->getOnepage()->savePayment($data);
							$redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
							if (empty($result['error']) && !$redirectUrl) {
								$this->loadLayout('checkout_onepage_review');
								$result['goto_section'] = 'review';
								$result['update_section'] = array(
				                    'name' => 'review',
				                    'html' => $this->_getReviewHtml()
								);
							}
							if ($redirectUrl) {
								$result['redirect'] = $redirectUrl;
							}
							if(!$this->getOnepage()->getQuote()->isVirtual()){
								$result['allow_sections'] = array('shipping');
								$result['duplicateBillingInfo'] = 'true';
							}
						} catch (Mage_Payment_Exception $e) {
							if ($e->getFields()) {
								$result['fields'] = $e->getFields();
							}
							$result['error'] = $e->getMessage();
						} catch (Mage_Core_Exception $e) {
							$result['error'] = $e->getMessage();
						} catch (Exception $e) {
							Mage::logException($e);
							$result['error'] = $this->__('Unable to set Payment Method.');
						}
					}
				} else {
					$result['goto_section'] = 'shipping';
				}
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
		}
	}

    public function saveShippingAction()
    {
	if (Mage::getSingleton('customer/session')->isLoggedIn() && Mage::helper('epicor_comm')->getErpAccountInfo()->getAccountType() == 'B2B'){
		parent::saveShippingAction();
	}
	else{
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data              = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result            = $this->getOnepage()->saveShipping($data, $customerAddressId);
            if (!isset($result['error'])) {
                $method = 'flatrate_flatrate';
		//$method = 'freeshipping_freeshipping';
                $result = $this->getOnepage()->saveShippingMethod($method);
                if (!isset($result['error'])) {
			$data = array('method'=>'free');
                    //$data        = array('method' => 'pay' );
                    $result      = $this->getOnepage()->savePayment($data);
                    // get section and redirect data
                    $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
                    if (!isset($result['error']) && !$redirectUrl) {
                        $this->loadLayout('checkout_onepage_review');
                        $result['goto_section']   = 'review';
                        $result['update_section'] = array(
                            'name' => 'review',
                            'html' => $this->_getReviewHtml()
                        );
                    }
                    if ($redirectUrl) {
                        $result['redirect'] = $redirectUrl;
                    }
                }
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
	}
    }
}
