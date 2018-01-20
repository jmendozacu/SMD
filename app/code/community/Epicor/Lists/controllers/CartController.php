<?php

/**
 * Contract frontend actions
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_CartController extends Epicor_Customerconnect_Controller_Abstract
{

    /**
     * Contract Select Page
     *
     * @return void
     */
    public function contractselectgridAction()
    {
        $itemId = $this->getRequest()->getParam('itemid');
        $cart = Mage::getSingleton('checkout/cart');
        /* @var $cart Mage_Checkout_Model_Cart */

        $cartItem = $cart->getQuote()->getItemById($itemId);
        /* @var $cartItem Mage_Sales_Model_Quote_Item */
        Mage::register('ecc_line_contract_item', $cartItem);

        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('epicor_lists/cart_contract_select_grid')->toHtml()
        );
    }

    /**
     * Contract Select Page
     *
     * @return void
     */
    public function applycontractselectAction()
    {
        $itemId = $this->getRequest()->getParam('itemid');
        $contractId = $this->getRequest()->getParam('contract');
        $urlReturn = $this->getRequest()->getParam('return_url');

        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */
        
        $cart = Mage::getSingleton('checkout/cart');
        /* @var $cart Mage_Checkout_Model_Cart */

        $cartItem = $cart->getQuote()->getItemById($itemId);
        /* @var $cartItem Mage_Sales_Model_Quote_Item */

        $save = false;
        if (empty($contractId)) {
            $cartItem->setEccContractCode(null);
            $save = true;
        } else {
            $contract = Mage::getModel('epicor_lists/list')->load($contractId);
            /* @var $contract Epicor_Lists_Model_List */
            if ($cartItem->getEccContractCode() != $contract->getErpCode()) {
                $save = true;
                $cartItem->setEccContractCode($contract->getErpCode());
            }
        }

        if ($save) {
            $cart->save();
        }
        
        $urlReturn = $helper->urlDecode(base64_decode($urlReturn)) ?: Mage::getUrl('checkout/cart');
        Mage::app()->getResponse()->setRedirect($urlReturn);
        die(Mage::app()->getResponse());
    }

}
