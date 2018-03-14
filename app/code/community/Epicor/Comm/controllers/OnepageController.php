<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
require_once('Mage' . DS . 'Checkout' . DS . 'controllers' . DS . 'OnepageController.php');

/**
 * Shopping cart controller
 */
class Epicor_Comm_OnepageController extends Mage_Checkout_OnepageController {

    private $_current_layout = null;

    /**
     * Shipping method save action
     */
    public function indexAction() {
        if ($this->getRequest()->get('grid')) {

            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('epicor_comm/customer_account_billingaddress_list')->toHtml()
            );
        }
        parent::indexAction();
    }

    public function saveShippingMethodAction() {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            /*
              $result will have erro data if shipping method is empty
             */
            if (!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request' => $this->getRequest(),
                    'quote' => $this->getOnepage()->getQuote()));

                $this->getOnepage()->getQuote()->setBsvCarriageAmount(null);
                $this->getOnepage()->getQuote()->setBsvCarriageAmountInc(null);

                $this->getOnepage()->getQuote()->collectTotals()->save();

                if (Mage::getStoreConfigFlag('epicor_comm_enabled_messages/dda_request/active')) {
                    $result['goto_section'] = 'shipping_dates';
                    $result['update_section'] = array(
                        'name' => 'shipping_dates',
                        'html' => $this->_getShippingDatesHtml()
                    );
                } else {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                }
            } else {
                $this->getOnepage()->getQuote()->collectTotals()->save();
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    protected function _getShippingDatesHtml() {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_shipping_dates');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    public function saveShippingDatesAction() {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_dates', '');
            $result = $this->getOnepage()->saveShippingDates($data);
            /*
              $result will have error data if shipping method is empty
             */
            if (!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_dates', array('request' => $this->getRequest(), 'quote' => $this->getOnepage()->getQuote()));

                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );
            }
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    /**
     * save checkout billing address
     */
    public function saveBillingAction() {
        $saveToErp = $this->getRequest()->getParam('billing');
        if (array_key_exists('save_in_address_book_erp', $saveToErp)) {     // if save address to erp requested, determine if to erp account data on magento or host erp account 
            Mage::register('newBillingAddress', $saveToErp);
            Mage::getModel('customer/session')->setSaveBillingAddressToErp(true);   // pick up in observer
            Mage::getModel('customer/session')->setSaveBillingAddress($saveToErp);   // pick up in observer
            if ($saveToErp['use_for_shipping']) {
                Mage::getModel('customer/session')->setSaveShippingAddressToErp(true);
                Mage::getModel('customer/session')->setSaveShippingAddress($saveToErp);
            }
        } else {
            Mage::getModel('customer/session')->setSaveBillingAddressToErp(false);
        }
        $this->getOnepage()->saveCustomerOrderRef($this->getRequest()->get('po-ref'));
        $this->getOnepage()->saveTaxExemptRef($this->getRequest()->get('tax_exempt_reference'));
        $this->getOnepage()->saveAdditionalRef($this->getRequest()->get('additional_reference'));
        $shipScreenShow = false;
        $storeId = $this->getOnepage()->getQuote()->getStoreId();
        $shipStatus = Mage::helper('epicor_comm')->shipStatus(null, $storeId);
        $shipVisible = $shipStatus['visible'];
        $requiredDate = Mage::helper('epicor_comm')->requiredDate(null, $storeId);
        $requiredDateVisible = $requiredDate['visible'];
        if ($shipVisible || $requiredDateVisible) {
            $shipStatusCollection = Mage::helper('epicor_comm')->getShipStatusCollection($storeId);
            if ($shipVisible && count($shipStatusCollection) > 0) {
                $shipScreenShow = true;
            } elseif ($requiredDateVisible) {
                $shipScreenShow = true;
            }
        }
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1 && !$shipScreenShow) {
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    if (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1 && $shipScreenShow) {
                        $result['duplicateBillingInfo'] = 'true';
                    }
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }


        // parent::saveBillingAction();
    }

    public function savePaymentAction() {
        $payment = $this->getRequest()->getParam('payment');
        Mage::register('send_checkout_bsv', true);
        Mage::dispatchEvent('save_payment_method', array('payment_method' => $payment['method']));
        parent::savePaymentAction();
    }

    public function billingpopupAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function billingPopupGridAction() {
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('epicor_comm/customer_account_billingaddress_list_grid')->toHtml()
        );
    }

    public function shippingPopupGridAction() {
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('epicor_comm/customer_account_shippingaddress_list_grid')->toHtml()
        );
    }

    public function shippingPopupAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function gridAction() {
        
    }

    public function saveShippingAction() {
        $saveToErp = $this->getRequest()->getParam('shipping');
        if (array_key_exists('save_in_address_book_erp', $saveToErp)) {      // if save address to erp requested, determine if to erp account data on magento or host erp account 
            Mage::getModel('customer/session')->setSaveShippingAddress($saveToErp);
            Mage::getModel('customer/session')->setSaveShippingAddressToErp(true);   // pick up in observer
        } else {
            Mage::getModel('customer/session')->setSaveShippingAddressToErp(false);
        }
        $this->getOnepage()->saveShipStatus($this->getRequest()->getParam('ship_status_erpcode'));
        $this->getOnepage()->saveRequiredDate($this->getRequest()->getParam('required_date'));
        parent::saveShippingAction();
    }

    /**
     * Create order action
     */
    public function saveOrderAction() {
        Mage::getModel('checkout/session')->setTrigger909('dont trigger 909 warning');
        Mage::register('checkout_save_order', true);
        parent::saveOrderAction();
        Mage::getModel('checkout/session')->setTrigger909(null);
    }

    /**
     * Create order action
     */
    public function saveArOrderAction() {
        Mage::register('checkout_arpayment_order', true);
        parent::saveOrderAction();
    }

    /**
     * AR Payment Success Page
     */
    public function successArOrderAction() {
        Mage::getSingleton('customer/session')->setData('ecc_arpaymentquote', '');
        $helper = Mage::helper('customerconnect/arpayments');
        /* @var $helper Epicor_Customerconnect_Helper_Arpayments */
        $helper->clearArpaymentsCache();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function getArstepcheckout() {
        return Mage::getModel('customerconnect/arpayments');
    }

    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax() {
        $helper = Mage::helper('customerconnect/arpayments');
        /* @var $helper Epicor_Customerconnect_Helper_Arpayments */
        $checkPage = $helper->checkpage();
        if ($checkPage) {
            return false;
        }
        if (!$this->getOnepage()->getQuote()->hasItems() || $this->getOnepage()->getQuote()->getHasError() || $this->getOnepage()->getQuote()->getIsMultiShipping()
        ) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        $action = strtolower($this->getRequest()->getActionName());
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true) && !in_array($action, array('index', 'progress'))
        ) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        return false;
    }

    /**
     * AR Payment Save AR Payment 
     */
    public function saveArPaymentAction() {
        Mage::register('checkout_arpayment_order', true);
        $pmnt_data = $this->getRequest()->getPost('payment', array());
        $results = $this->getArstepcheckout()->savePayment($pmnt_data);
        $result = array();

        $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl . "?arredirect=true";
        } else {
            $result['goto_section'] = 'review';
            $orderUrl = Mage::getUrl('checkout/onepage/saveArOrder', array('form_key' => Mage::getSingleton('core/session')->getFormKey()));
            $successurl = Mage::getUrl('checkout/onepage/successArOrder');
            $redirectParams = "'$orderUrl'" . "," . "'$successurl'" . ",";
            $result['goto_section'] = 'review';
            //",".'$successurl'","."$('checkout-agreements')";  
            $loadSkin = Mage::getDesign()->getSkinUrl('images/opc-ajax-loader.gif');
            $result['update_section'] = array(
                'name' => 'review',
                'html' => $this->_getReviewsHtml() . "<style>#checkout-review-table { display:none; }  #show_ar_payments_total_grid { display:block !important} .f-left {display:none !important;} .wide {display:none !important;}</style>"
                . "<script>
                    var checktableExist=$('show_ar_payments_total_grid');
                    checkout.changeSection('opc-payment');
                    if (typeof(checktableExist) != 'undefined' && checktableExist != null) {
                        checkout.changeSection('opc-payment');
                        $('opc-review').addClassName('active');
                        $('checkout-step-review').style.display= 'block';                
                        accordion.openSection('opc-review');
                        accordion.closeSection('opc-payment');
                        checktableExist.style.display= 'block';
                    }
                    document.querySelector('.checkout-span').innerHTML = 'Make Payment';
                    $('review-please-wait').innerHTML ='<img src=" . $loadSkin . "  />Submitting Payment information...';
                </script>"
                . "<script type='text/javascript'>
                //<![CDATA[
                    review = new Review(" . $redirectParams . ");
                //]]>
                </script>"
            );
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Load the updated AR Layout Onepage Review block
     */
    protected function _getUpdatedArLayout() {
        $this->_initLayoutMessages('checkout/session');
        if ($this->_current_layout === null) {
            $layout = $this->getLayout();
            $update = $layout->getUpdate();
            $update->load('checkout_onepage_review');
            $layout->generateXml();
            $layout->generateBlocks();
            $this->_current_layout = $layout;
        }
        return $this->_current_layout;
    }

    protected function _getReviewsHtml() {
        $layout = $this->_getUpdatedArLayout();
        return $layout->getBlock('root')->toHtml();
    }

}
