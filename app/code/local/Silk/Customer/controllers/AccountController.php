<?php
include_once("Epicor/Common/controllers/AccountController.php");
class Silk_Customer_AccountController extends  Epicor_Common_AccountController
{


    protected function _loginPostRedirect()
    {

	$customerSession = Mage::getSingleton('customer/session');
	$sessionProduct = Mage::getSingleton('core/session');
	//Mage::log($sessionProduct->getData('addProduct'),null,'json_data.log',true);
	if($customerSession->isLoggedIn()) {
		if($sessionProduct->getData('addProduct') != null) {
			$product = Mage::getModel('catalog/product')
                                ->setStoreId(Mage::app()->getStore()->getId())
                                ->load($sessionProduct->getData('addProduct'));
                        $cart = Mage::getSingleton('checkout/cart');
                        $cart->addProduct($product, 1);
                        $cart->save();
                        Mage::getSingleton('core/session')->unsetData('addProduct');
			
		}
		
	}
        /* @var $customerSession Mage_Customer_Model_Session */
        // This check no longer works as http_referer is not populated, so commented out
        $redirectAjaxCheck = Mage::helper('epicor_common/redirect');
        /* @var $helper Epicor_Common_Helper_Redirect */
        $isAjax       = $redirectAjaxCheck->checkIsAjaxPage();
        if (strpos($customerSession->getBeforeAuthUrl(), 'checkout/onepage') == true && isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) && !$isAjax) {
            $customerSession->setBeforeAuthUrl($_SERVER['HTTP_REFERER']);
        }

        $url = parse_url($customerSession->getBeforeAuthUrl());
        $cmsPageId=NULL;
        if (strpos($url['path'], 'onepage') == false &&
                strpos($url['path'], 'multishipping') == false &&
                strpos($url['path'], '/comm') == false) {
            $lastPageB4Login = $customerSession->getBeforeAuthUrl();
            if (Mage::getStoreConfigFlag('customer/startup/redirect_dashboard')) {
                $customerSession->setBeforeAuthUrl(Mage::helper('customer')->getAccountUrl());
            } else if (Mage::getStoreConfig('epicor_common/login/landing_page') == 'cms_page') {
                $cmsPageId = Mage::getStoreConfig('epicor_common/login/landing_cms_page');
                if ($cmsPageId) {
                    $url = Mage::helper('cms/page')->getPageUrl($cmsPageId);
                    if ($url) {
                        $customerSession->setBeforeAuthUrl($url);
                    }
                }
            }
            Mage::dispatchEvent('add_final_redirect_url_to_redirect_array', array('cms_page_id' => $cmsPageId, 'last_page_before_login'=>$lastPageB4Login));
        }

        if( ! Mage::getSingleton('customer/session')->isLoggedIn()){
            $customerSession->setBeforeAuthUrl(Mage::getUrl('customer/account/login'));
        }


        parent::_loginPostRedirect();

    }

}

