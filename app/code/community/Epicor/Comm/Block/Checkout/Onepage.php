<?php

class Epicor_Comm_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage
{    
    /**
     * Get checkout steps codes
     *
     * @return array
     */
    protected function _getStepCodes()
    {
        $steps = parent::_getStepCodes();
        if(Mage::getStoreConfigFlag('epicor_comm_enabled_messages/dda_request/active')){
            $index = array_search('shipping_method', $steps);
            array_splice($steps, $index+1, 0, 'shipping_dates');
        }
        
        $transportObject = new Varien_Object;
        $transportObject->setSteps($steps);
        Mage::dispatchEvent('epicor_comm_onepage_get_steps', array('block' => $this, 'steps' => $transportObject));
        $steps = $transportObject->getSteps();
        
        return $steps;
    }
    
    /**
     * Get active step
     *
     * @return string
     */
    public function getActiveStep()
    {
        $step = parent::getActiveStep();
        
        $transportObject = new Varien_Object;
        $transportObject->setStep($step);
        Mage::dispatchEvent('epicor_comm_onepage_get_active_step', array('block' => $this, 'step' => $transportObject));
        $step = $transportObject->getStep();
        
        return $step;
    }
}
