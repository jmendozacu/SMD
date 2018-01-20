<?php

class Epicor_Comm_Block_Sales_Order_Returnlink extends Mage_Sales_Block_Order_Info_Buttons {

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('epicor_comm/sales/order/returnlink.phtml');
    }

    public function canReturn() {

        $canReturn = false;
        $order = $this->getOrder();

        if ($order->getErpOrderNumber()) {
            $helper = Mage::helper('epicor_common/access');
            /* @var $helper Epicor_Common_Helper_Access */

            $returnsHelper = Mage::helper('epicor_comm/returns');
            /* @var $helper Epicor_Comm_Helper_Returns */



            if ($returnsHelper->isReturnsEnabled() && $returnsHelper->checkConfigFlag('allow_create')) {
                $canReturn = $helper->customerHasAccess(
                        'Epicor_Comm', 'Returns', 'createReturnFromDocument', '', 'Access'
                );
            }
        }
        return $canReturn;
    }

    /**
     * Get url for return action
     *
     * @param Mage_Sales_Order $order
     * @return string
     */
    public function getReturnUrl() {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        $order = $this->getOrder();

        return $helper->getCreateReturnUrl('order', array(
                    'order_number' => $order->getErpOrderNumber()
                        )
        );
    }

}
