<?php

/**
 * Grid controller, handles generic gird functions
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Lists_GridController extends Epicor_Lists_Controller_Abstract
{

    /**
     * Clear action - clears the cache for the specified grid
     */
    public function clearAction()
    {

        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();

        $grid = Mage::app()->getRequest()->getParam('grid');
        $location = Mage::helper('core/url')->urlDecode(Mage::app()->getRequest()->getParam('location'));

        $tags = array('CUSTOMER_' . $customerId . '_CUSTOMERCONNECT_' . strtoupper($grid));

        $cache = Mage::app()->getCacheInstance();
        /* @var $cache Mage_Core_Model_Cache */
        $cache->clean($tags);

        $this->_redirectUrl($location);
    }
//
//    /*     * *
//     * ajax search for page with two grids
//     * used in customerconnect/account/index for shipping and contacts 
//     */
//
//    public function shippingsearchAction()
//    {
//        $this->recreateCCCD();
//        $this->loadLayout();
//        $this->getResponse()->setBody(
//            $this->getLayout()->createBlock('epicor_lists/customer_account_contracts_shippingaddress_list_grid')->toHtml()    // location of grid block
//        );
//    }
//
//    public function partssearchAction()
//    {
//        $this->recreateCCCD();
//        $this->loadLayout();
//        $this->getResponse()->setBody(
//            $this->getLayout()->createBlock('epicor_lists/customer_account_contracts_parts_list_grid')->toHtml()    // location of grid block
//        );
//    }
//
//    protected function recreateCCCD()
//    {
//        $helper = Mage::helper('epicor_lists/messaging');
//        /* @var $helper Epicor_Lists_Helper_Messaging */
//        $erp_account_number = $helper->getErpAccountNumber();
//        $message = Mage::getSingleton('epicor_lists/message_request_cccd');
//        $error = false;
//        $messageTypeCheck = $message->getHelper("epicor_list/messaging")->getMessageType('CCCD');
//        if ($message->isActive() && $messageTypeCheck) {
//            $message->setAccountNumber($erp_account_number)
//                ->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));
//
//            if ($message->sendMessage()) {
//                Mage::register('epicor_lists_contracts_details', $message->getResponse());
//            } else {
//                $error = true;
//                Mage::getSingleton('core/session')->addError($helper->__('Failed to retrieve Customer Contract Details'));
//            }
//        } else {
//            $error = true;
//            Mage::getSingleton('core/session')->addError($helper->__('Customer Contract Details not available'));
//        }
//    }

}
