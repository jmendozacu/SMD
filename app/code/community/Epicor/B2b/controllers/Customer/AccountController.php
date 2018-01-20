<?php

include_once("Mage/Customer/controllers/AccountController.php");

class Epicor_B2b_Customer_AccountController extends Mage_Customer_AccountController {

    public function loadLayout($handles = null, $generateBlocks = true, $generateXml = true) {
        if ((!$this->_getSession()->isLoggedIn()) && Mage::getStoreConfigFlag('epicor_b2b/registration/reg_portaltype')) {
            $customHandle = 'use_portal';
            parent::loadLayout($handles, false);
            $update = $this->getLayout()->getUpdate();
            $update->addHandle($customHandle);
            if (Mage::app()->useCache('layout')) {
                $cacheId = $update->getCacheId() . $customHandle;
                $update->setCacheId($cacheId);

                if (!Mage::app()->loadCache($cacheId)) {
                    foreach ($update->getHandles() as $handle) {
                        $update->merge($handle);
                    }
                    $update->saveCache();
                } else {
                    //load updates from cache
                    $update->load();
                }
            } else {
                //load updates
                $update->load();
            }

            $this->loadLayoutUpdates();
            if ($generateXml)
                $this->generateLayoutXml();
            if ($generateBlocks)
                $this->generateLayoutBlocks();
        }
        else
            parent::loadLayout($handles, $generateBlocks, $generateXml);
    }

    /**
     * Customer login form page
     */
    public function loginAction() {
        if (Mage::getStoreConfigFlag('epicor_b2b/registration/reg_portal')) {
            // $this->_redirect('b2b/portal/login');
            $this->_forward('login', 'portal', 'b2b');
        } else {
            if ($this->getRequest()->getParam('access') != 'denied') {
                parent::loginAction();
            }
        }
    }

    public function createAction() {
        parent::createAction();
    }

    protected function _redirectSuccess($defaultUrl) {
        parent::_redirectSuccess($defaultUrl);
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
