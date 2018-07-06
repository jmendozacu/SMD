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
 * @date    17-3-15 下午5:26
 * @version 0.1.0
 */
class Silk_Retailer_Model_Observer
{
    public function addSampleProductRecord($observer)
    {
        $item = $observer->getEvent()->getQuoteItem();
        $product = $item->getProduct();
        if (Mage::helper('silk_retailer')->recordOrDeleteSampleProductRecord($product)) {
            $data = array(
                'customer_id' => Mage::getSingleton('customer/session')->getCustomerId(),
                'type' => Mage::getSingleton('customer/session')->getCustomer()->getEccErpAccountType(),
                'product_id'  => $product->getId(),
                'quote_id' => $item->getQuoteId(),
                'time' => time()
            );
            Mage::getModel('silk_retailer/record')->addData($data)->save();
        }
    }

    public function removeSampleProductRecord($observer)
    {
        $product = $observer->getEvent()->getQuoteItem()->getProduct();
        if (Mage::helper('silk_retailer')->recordOrDeleteSampleProductRecord($product)) {
            Mage::getModel('silk_retailer/record')->deleteByCidAndPid(Mage::getSingleton('customer/session')->getCustomerId(), $product->getId());
        }
    }

    /**
     * If set qty over than allow max number, force change qty to allow max number
     * @param $observer
     */
    public function limitSampleProductQty($observer)
    {
        // Get different role allow max qty
        $retailerHp = Mage::helper('silk_retailer');
        $item = $observer->getEvent()->getItem();
        if ($retailerHp->isSampleProduct($item->getProduct())) {
            $max = $retailerHp->getSampleEachMax();
            $allowSampleNum = $retailerHp->getAllowSampleNumber();

            $oldQty = (int)$item->getOrigData('qty');

            $over = false;

            // bigger than old qty and little than max number
            if (($item->getQty() > $oldQty) && $max >= $item->getQty()) {
                // bigger than old qty, little than max
                $increase = $item->getQty() - $oldQty;
                // if increase bigger than allow sample number
                if ($increase > $allowSampleNum) {
                    $item->setQty($oldQty);
                    $over = true;
                }
            } elseif ($item->getQty() > $max) {
                // bigger than max
                $increase = $max - $oldQty;
                $increase = abs($increase);
                $qty = $increase > $allowSampleNum ? $allowSampleNum : $max ;
                $item->setQty($qty);
                $over = true;
            }

            if ($over) {
                Mage::getSingleton('core/session')->addError($retailerHp->getSampleErrorMsg());
            }

            // if is sample product, set record qty and item id
            Mage::getSingleton('silk_retailer/record')->updateByQuoteItem($item);
        }
    }

    public function cleanExpiredData()
    {
        Mage::getModel('silk_retailer/record')->cleanExpiredData();
    }

    public function sendConfirmationEmail($object)
    {
        if (Mage::getStoreConfig('sales_email/retailer/enabled')) {
            $event = $object->getEvent();
            $order = $event->getOrder();
            $quote = $event->getQuote();

            $storeId = $order->getStore()->getId();
            $orderId = $order->getId();

            //
            $subject = Mage::helper('core')->__('Retailer Confirmation');
            $templateCode = Mage::getStoreConfig('sales_email/retailer/template'); //'sales_email_retailer_template';
            $senderInfo = Mage::getStoreConfig('sales_email/retailer/identity');

            $deliveryAddress = '';
            $deliveryAddress .= $address = $order->getShippingAddress();
            $deliveryAddress .= '<br/>' . $address->getStreetFull();
            $deliveryAddress .= '<br/>' . $address->getCity();
            $deliveryAddress .= '<br/>' . Mage::helper('directory')->__($address->getRegion());
            $deliveryAddress .= '<br/>' . $address->getPostcode();
            $deliveryAddress .= '<br/>' . $address->getCountryModel()->getName();

            $itemsHtml = '';
            $helper = Mage::helper('silk_retailer');
            $items = $quote->getAllVisibleItems();
            foreach ($items as $item) {
                $access = $helper->isSampleProduct($item->getProduct());
                if ($access) {
                    $itemsHtml .= '<li>' . $item->getName() . '</li>';
                }
            }
            $itemsHtml = '<ul>' . $itemsHtml . '</ul>';

            $params = array(
                'order' => $order,
                'quote' => $quote,
                'items' => $itemsHtml,
                'delivery_address' => $deliveryAddress
            );

            $toInfo = array(
                'email' => $order->getCustomerEmail(),
                'name'  => $order->getCustomerName()
            );

            // queue entity
            /** @var $emailQueue Mage_Core_Model_Email_Queue */
            $emailQueue = Mage::getModel('core/email_queue');
            $emailQueue->setEntityId($orderId)
                ->setEntityType('order')
                ->setEventType('retailer');
            $emailTemplate = Mage::getModel('core/email_template');
            $emailTemplate->setQueue($emailQueue);
            $emailTemplate->setTemplateSubject($subject);
            $emailTemplate->sendTransactional($templateCode, $senderInfo, $toInfo['email'], $toInfo['name'], $params, $storeId);
        }
    }

    public function addSilkDeliveryDate($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $quoteItem = $observer->getEvent()->getQuoteItem();
        if ($quoteItem->getQty() > $product->getStockLevel()) {
            $silkDeliveryDate = $product->getData('poqtyonedate');
        } else {
            $leadTime = (int) $product->getLeadTime();
            $silkDeliveryDate = Mage::getModel('core/date')->date('Y-m-d', strtotime("+{$leadTime}day"));
        }
        $quoteItem->setSilkDeliveryDate($silkDeliveryDate);
    }

    public function addSilkDeliveryDateToOrder($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $comments = Mage::app()->getRequest()->getPost('cart');
        if (!empty($comments)) {
            foreach ($comments as $itemId => $itemInfo) {
                $item = $order->getItemByQuoteItemId($itemId);
                if (isset($itemInfo['silk_delivery_date'])) {
                    $item->setSilkDeliveryDate($itemInfo['silk_delivery_date']);
                }
                if ($item->getId()) {
                    $item->save();
                }
            }
        }
    }
}
