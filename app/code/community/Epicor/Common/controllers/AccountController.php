<?php

/**
 * Override of customer account controller
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 * 
 */
include_once("Mage/Customer/controllers/AccountController.php");

class Epicor_Common_AccountController extends Mage_Customer_AccountController
{

    public function indexAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $dashboard = 'comm';
        if ($customer->isSalesRep()) {
            $dashboard = 'salesrep';
        } elseif ($customer->isCustomer()) {
            $dashboard = Mage::getStoreConfig('Epicor_Comm/dashboard_priority/dashboard');
        } elseif ($customer->isSupplier()) {
            $dashboard = 'supplierconnect';
        }

        //var_dump($dashboard);exit;
        Mage::app()->getResponse()
                ->setRedirect(Mage::getUrl(Mage::getConfig()->getNode("global/xml_{$dashboard}_dashboard/path")));
        Mage::app()->getResponse()->sendResponse();
    }

    public function dashboardAction()
    {
        parent::indexAction();
    }

    public function errorAction()
    {
        parent::indexAction();
    }

    /**
     * Define target URL and redirect customer after logging in
     */
    protected function _loginPostRedirect()
    {
        $customerSession = Mage::getSingleton('customer/session');
        /* @var $customerSession Mage_Customer_Model_Session */
        if (strpos($customerSession->getBeforeAuthUrl(), 'checkout/onepage') == true) {     //reset setBeforeAuthUrl value to calling url if set by guest or registered user
            $customerSession->setBeforeAuthUrl($_SERVER['HTTP_REFERER']);
        }

        if (strpos($customerSession->getBeforeAuthUrl(), 'onepage') == false &&
                strpos($customerSession->getBeforeAuthUrl(), 'multishipping') == false &&
                strpos($customerSession->getBeforeAuthUrl(), 'comm') == false) {
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
        }



        parent::_loginPostRedirect();
    }

    public function encodedataAction()
    {
        Mage::app()->cleanCache();  // clear cache (chagne to clear message cache)  
        $helper = Mage::helper('epicor_common');
        $data = Mage::app()->getRequest()->getParams();
        $non_json_data = json_decode($data['jsondata']);
        $string_data = implode(",", $non_json_data);
        $encoded_data_array = $helper->urlEncode($string_data);
        $encoded_data = explode(",,", $encoded_data_array);
        echo $encoded_data[0];
    }

    /**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @return string
     */
    protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false)
    {
        $customeWelcomeMessage = $this->__(Mage::getStoreConfig('epicor_b2b/registration/customer_success_message'));
        
        if (empty($customeWelcomeMessage)) {
            return parent::_welcomeCustomer($customer, $isJustConfirmed);
        }
        
        $this->_getSession()->addSuccess($customeWelcomeMessage);
        if ($this->_isVatValidationEnabled()) {
            // Show corresponding VAT message to customer
            $configAddressType =  $this->_getHelper('customer/address')->getTaxCalculationAddressType();
            $userPrompt = '';
            switch ($configAddressType) {
                case Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you shipping address for proper VAT calculation',
                        $this->_getUrl('customer/address/edit'));
                    break;
                default:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you billing address for proper VAT calculation',
                        $this->_getUrl('customer/address/edit'));
            }
            $this->_getSession()->addSuccess($userPrompt);
        }

        $customer->sendNewAccountEmail(
            $isJustConfirmed ? 'confirmed' : 'registered',
            '',
            Mage::app()->getStore()->getId()
        );

        $successUrl = $this->_getUrl('*/*/index', array('_secure' => true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }
   
}
