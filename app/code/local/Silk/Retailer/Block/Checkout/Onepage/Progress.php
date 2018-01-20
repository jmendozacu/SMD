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
 * @date    17-3-4 上午7:50
 * @version 0.1.0
 */
class Silk_Retailer_Block_Checkout_Onepage_Progress extends Epicor_Comm_Block_Checkout_Onepage_Progress
{
    /**
     * Get checkout steps codes
     *
     * @return array
     */
    protected function _getStepCodes()
    {
        $steps = parent::_getStepCodes();
        $index = array_search(Silk_Retailer_Model_Retailer::STEP_CODE, $steps);
        array_splice($steps, $index + 1, 0, 'shipping_dates');

        $transportObject = new Varien_Object;
        $transportObject->setSteps($steps);
        Mage::dispatchEvent('epicor_comm_onepage_get_steps', array('block' => $this, 'steps' => $transportObject));
        $steps = $transportObject->getSteps();

        return $steps;
    }

    public function getRetailerLabel()
    {
        return $this->getQuote()->getRetailer();
        // @TODO NEED MODIFY
//        $retailer = Mage::getModel('silk_retailer/retailer')->loadByRetailerId($this->getQuote()->getRetailerId());
//        return $retailer->getAddress1() . '<br/>' . $retailer->getTown() . '<br/>' . $retailer->getCity() . '<br/>' . $retailer->getState();
    }
}