<?php

/**
 * Copyright (c) 2017, SILK Software
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. All advertising materials mentioning features or use of this software
 *    must display the following acknowledgement:
 *    This product includes software developed by the SILK Software.
 * 4. Neither the name of the SILK Software nor the
 *    names of its contributors may be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY SILK Software ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL SILK Software BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @authors daniel (daniel.luo@silksoftware.com)
 * @date    17-3-4 上午7:32
 * @version 0.1.0
 */
require_once 'Epicor' . DS . 'Comm' . DS . 'controllers' . DS . 'OnepageController.php';

class Silk_Retailer_OnepageController extends Epicor_Comm_OnepageController
{
    /**
     * save checkout billing address
     */
    public function saveBillingAction()
    {
        if (Mage::helper('silk_retailer')->accessRetailerStep()) {
            $this->_saveBilling();
        } else {
            parent::saveBillingAction();
        }
    }

    protected function _saveBilling()
    {
        if ($this->_expireAjax()) {
            return;
        }

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
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                        $result['goto_section'] = Silk_Retailer_Model_Retailer::STEP_CODE;
                        $result['update_section'] = array(
                            'name' => Silk_Retailer_Model_Retailer::STEP_NAME,
                            'html' => $this->_getShippingRetailerHtml()
                        );
/*
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );
//**/
                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Shipping address save action
     */
    public function saveShippingAction()
    {
        if (Mage::helper('silk_retailer')->accessRetailerStep()) {
            $this->_saveShipping();
        } else {
            parent::saveShippingAction();
        }
    }

    protected function _saveShipping()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                $result['goto_section'] = Silk_Retailer_Model_Retailer::STEP_CODE;
                $result['update_section'] = array(
                    'name' => Silk_Retailer_Model_Retailer::STEP_NAME,
                    'html' => $this->_getShippingRetailerHtml()
                );
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    protected function _getShippingRetailerHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_' . Silk_Retailer_Model_Retailer::STEP_CODE);
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    /**
     * Shipping address save action
     */
    public function saveShippingRetailerAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();

            if (isset($params[Silk_Retailer_Model_Retailer::STEP_CODE])) {
                $label = Mage::getModel('silk_retailer/retailer')->getRetailerLabelById($params[Silk_Retailer_Model_Retailer::STEP_CODE]);
                $this->getOnepage()->getQuote()
                    ->setRetailerId($params[Silk_Retailer_Model_Retailer::STEP_CODE])
                    ->setRetailer($label)
                    ->save();
            }

            if (!isset($result['error'])) {
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
            }

            $this->getOnepage()->getCheckout()
                ->setStepData(Silk_Retailer_Model_Retailer::STEP_CODE, 'allow', true)
                ->setStepData(Silk_Retailer_Model_Retailer::STEP_CODE, 'complete', true)
                ->setStepData('shipping_method', 'allow', true);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
}