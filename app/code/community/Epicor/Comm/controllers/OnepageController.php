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
class Epicor_Comm_OnepageController extends Mage_Checkout_OnepageController
{

    /**
     * Shipping method save action
     */
    public function indexAction()
    {
         if ($this->getRequest()->get('grid')) {
             
           $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('epicor_comm/customer_account_billingaddress_list')->toHtml()
            );
        }   
        parent::indexAction();
    }       
    public function saveShippingMethodAction()
    {
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

    protected function _getShippingDatesHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_shipping_dates');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    public function saveShippingDatesAction()
    {
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
    public function saveBillingAction()
    {
        $saveToErp = $this->getRequest()->getParam('billing');
        if(array_key_exists('save_in_address_book_erp', $saveToErp)){     // if save address to erp requested, determine if to erp account data on magento or host erp account 
            Mage::register('newBillingAddress', $saveToErp);
            Mage::getModel('customer/session')->setSaveBillingAddressToErp(true);   // pick up in observer
            Mage::getModel('customer/session')->setSaveBillingAddress($saveToErp);   // pick up in observer
            if($saveToErp['use_for_shipping']){
                Mage::getModel('customer/session')->setSaveShippingAddressToErp(true);
                Mage::getModel('customer/session')->setSaveShippingAddress($saveToErp);
            }
        }else{
            Mage::getModel('customer/session')->setSaveBillingAddressToErp(false);
        }
//                      
        $this->getOnepage()->saveCustomerOrderRef($this->getRequest()->get('po-ref'));
        parent::saveBillingAction();
    }

    public function savePaymentAction()
    {
        $payment = $this->getRequest()->getParam('payment');
        Mage::register('send_checkout_bsv', true);
        Mage::dispatchEvent('save_payment_method', array('payment_method' => $payment['method']));
        parent::savePaymentAction();
    }
    public function billingpopupAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function billingPopupGridAction()
    {
           $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('epicor_comm/customer_account_billingaddress_list_grid')->toHtml()
            );
    }
    public function shippingPopupGridAction()
    {
           $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('epicor_comm/customer_account_shippingaddress_list_grid')->toHtml()
            );
    }

public function shippingPopupAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function gridAction(){
        
    }
    public function saveShippingAction()
    {     
        $saveToErp = $this->getRequest()->getParam('shipping');
        if(array_key_exists('save_in_address_book_erp', $saveToErp)){      // if save address to erp requested, determine if to erp account data on magento or host erp account 
             Mage::getModel('customer/session')->setSaveShippingAddress($saveToErp);
             Mage::getModel('customer/session')->setSaveShippingAddressToErp(true);   // pick up in observer
        }else{
              Mage::getModel('customer/session')->setSaveShippingAddressToErp(false);
        }
        parent::saveShippingAction();     
    } 
     /**
     * Create order action
     */
    public function saveOrderAction()
    {
        Mage::register('checkout_save_order', true);   
        parent::saveOrderAction();
    }
    
}
