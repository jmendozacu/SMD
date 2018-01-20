<?php

/**
 * Supplierconnect abstract controller
 *
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Controller_Abstract extends Mage_Core_Controller_Front_Action {

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch() {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

}

