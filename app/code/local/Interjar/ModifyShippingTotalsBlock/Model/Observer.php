<?php
/**
 * @package Interjar_ModifyShippingTotalsBlock
 * @author Interjar Ltd
 * @author Andy Burns <andy@interjar.com>
 */
class Interjar_ModifyShippingTotalsBlock_Model_Observer
{
    /**
     * Observer method to modify shipping method title
     * @param Varien_Event_Observer $observer
     */
    public function changeShippingMethodOnBasket(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $block = $event->getBlock();
        /** @var Mage_Core_Controller_Request_Http $request */
        $request = Mage::app()->getRequest();
        if ($request->getControllerName() == 'cart' && $request->getActionName() == 'index') {
            if ($block instanceof Mage_Tax_Block_Checkout_Shipping) {
                $transport = $observer->getTransport();
                $html = $transport->getHtml();
                $newHtml = preg_replace("/\([^)]+\)/","", $html);
                $transport->setHtml(trim($newHtml));
            }
        }
        if ($request->getModuleName() == 'quickorderpad' && $request->getControllerName() == 'form' &&
            $request->getActionName() == 'index') {
            if ($block instanceof Mage_Tax_Block_Checkout_Shipping) {
                $transport = $observer->getTransport();
                $html = $transport->getHtml();
                $newHtml = preg_replace("/\([^)]+\)/","", $html);
                $transport->setHtml(trim($newHtml));
            }
        }
    }
}
