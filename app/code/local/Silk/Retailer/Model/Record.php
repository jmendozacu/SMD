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
 * @date    17-3-15 下午5:12
 * @version 0.1.0
 */
class Silk_Retailer_Model_Record extends Mage_Core_Model_Abstract
{
    /**
     * Record keep time unit is 1 day
     * 1 day = 60s * 60m * 24h;
     */
    const RECORD_KEEP_TIME_UNIT = 86400;

    protected $_eventPrefix = 'silk_retailer_record';

    protected function _construct()
    {
        $this->_init('silk_retailer/record');
    }

    public function deleteByCidAndPid($customerId, $productId)
    {
        $item = $this->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('product_id', $productId)
            ->getFirstItem();
        $item->delete();
    }

    public function currentCustomerRecordCount()
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomerId())
            ->addFieldToFilter('time', array('lt', time()));
        return $collection->getSize();
    }

    /**
     * Clean expired record
     */
    public function cleanExpiredData()
    {
        // @TODO need get customer type code from ecc
        $adapter = Mage::getResourceSingleton('silk_retailer/record');
        $connection = $adapter->getReadConnection();

        // delete B2C record
        $day = Mage::getStoreConfig('sales/sample_product_b2c/keep_day');
        $connection->delete($adapter->getTable('order_sample_record'), array(
            'type=?' => 'guest',
            'time <=?' => time() - $day * self::RECORD_KEEP_TIME_UNIT
        ));

        // delete B2B record
        $day = Mage::getStoreConfig('sales/sample_product_b2b/keep_day');
        $connection->delete($adapter->getTable('order_sample_record'), array(
            'type=?' => 'customer',
            'time <=?' => time() - $day * self::RECORD_KEEP_TIME_UNIT
        ));
    }

    public function updateByQuoteItem($item)
    {
        $this->getResource()->getReadConnection()
            ->update('silk_order_sample_record', array(
            'qty' => $item->getQty()),
            array(
                'quote_id=?' => $item->getQuoteId(),
                'product_id=?' => $item->getProductId()
            ));
    }

    public function getHistorySampleSum()
    {
        $adapt = $this->getResource()->getReadConnection();
        $select = $adapt->select('qty')
                    ->from('silk_order_sample_record')
                    ->where('customer_id=:customer_id');
        $select->reset(Zend_Db_Select::COLUMNS)->columns(array('SUM(qty) AS total'));
        return (int)$adapt->fetchOne($select, array(
            ':customer_id' => Mage::getSingleton('customer/session')->getCustomerId()
        ));
    }
}