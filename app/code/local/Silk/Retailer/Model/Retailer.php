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
 * @date    17-3-4 上午7:23
 * @version 0.1.0
 */
class Silk_Retailer_Model_Retailer extends Mage_Core_Model_Abstract
{
    const STEP_CODE = 'shipping_retailer';

    const STEP_NAME = 'shipping-retailer';

    protected function _construct()
    {
        $this->_init('silk_retailer/retailer');
    }

    public function loadByRetailerId($retailerId)
    {
        return $this->getCollection()->addFieldToFilter('retailer_id', $retailerId)->getFirstItem();
    }

    public function getRetailerLabelById($retailerId)
    {
        $retailer = $this->loadByRetailerId($retailerId);
        if ($retailer->getId()) {
            return $retailer->getAddress1() . '<br/>' . $retailer->getTown() . '<br/>' . $retailer->getCity() . '<br/>' . $retailer->getState();
        }
        return '';
    }

    protected function _beforeSave()
    {
        // isObjectNew
        if ($this->isObjectNew() && $this->isObjectNew('retailer_id')) {
            $this->setRetailerId($this->_getRetailerId());
        }

        return parent::_beforeSave();
    }

    protected function _getRetailerId()
    {
        return Mage::helper('silk_retailer')->getHash(
            $this->getCity(),
            $this->getState(),
            $this->getZip(),
            $this->getTown()
        );
    }
}