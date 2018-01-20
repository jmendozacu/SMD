<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Progress
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Checkout_Onepage_Progress extends Mage_Checkout_Block_Onepage_Progress
{

    /**
     * Get checkout steps codes
     *
     * @return array
     */
    protected function _getStepCodes()
    {
        $steps = parent::_getStepCodes();
        if (Mage::getStoreConfigFlag('epicor_comm_enabled_messages/dda_request/active')) {
            $index = array_search('shipping_method', $steps);
            array_splice($steps, $index + 1, 0, 'shipping_dates');
        }

        $transportObject = new Varien_Object;
        $transportObject->setSteps($steps);
        Mage::dispatchEvent('epicor_comm_onepage_get_steps', array('block' => $this, 'steps' => $transportObject));
        $steps = $transportObject->getSteps();

        return $steps;
    }

}
