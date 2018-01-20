<?php

/**
 * Customerconnect abstract controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    
    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    
    private $_allowedActions = array('changeshippingaddress', 'changeshippingaddressajax', 'changebillingaddressajax', 'changebillingaddressajax', 'selectaddressajax', 'cartpopup', 'removeitemsincart');
    
    public function preDispatch()
    {
        // a brute-force protection here would be nice
        parent::preDispatch();
        $actionName    = Mage::app()->getRequest()->getActionName();
        $allowedAction = $this->_allowedActions;
        $allowedOrNot  = in_array($actionName, $allowedAction);
        if (!$allowedOrNot) {
            if (!Mage::getSingleton('customer/session')->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        }
    }
}