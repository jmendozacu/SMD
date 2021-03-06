<?php

/**
 * Orders controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Common_Sales_OrderController extends Mage_Core_Controller_Front_Action
{

    /**
     * Check order view availability
     *
     * @param   Epicor_Comm_Model_Order $order
     * @return  bool
     */
    protected function _canViewOrder($order)
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $availableStates = Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId) && in_array($order->getState(), $availableStates, $strict = true)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Try to load valid order by order_id and register it
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadValidOrder($orderId = null)
    {
        if (null === $orderId) {
            $orderId = (int) $this->getRequest()->getParam('order_id');
        }
        if (!$orderId) {
            $this->_forward('noRoute');
            return false;
        }

        $order = Mage::getModel('sales/order')->load($orderId);

        if ($this->_canViewOrder($order)) {
            return $order;
        } else {
            $this->_redirect('*/*/history');
        }
        return false;
    }

    /**
     * Action for reorder
     */
    public function reorderAction()
    {
        $order = $this->_loadValidOrder();
        /* @var $order Epicor_Comm_Model_Order */
        if (!$order) {
            return;
        }

        if ($order->getErpOrderNumber()) {
            $this->_reorderErp($order);
        } else {
            $this->_reorderLocal($order);
        }
    }

    protected function _reorderErp($order)
    {

        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $erp_account_number = $helper->getErpAccountNumber();

        $result = $helper->sendOrderRequest($erp_account_number, $order->getErpOrderNumber(), $helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));

        $cartHelper = Mage::helper('epicor_common/cart');
        /* @var $cartHelper Epicor_Common_Helper_Cart */

        if (empty($result['order']) || !$cartHelper->processReorder($result['order'])) {

            if (!empty($result['error'])) {
                Mage::getSingleton('core/session')->addError($result['error']);
            }

            if (!Mage::getSingleton('core/session')->getMessages()->getItems()) {
                Mage::getSingleton('core/session')->addError('Failed to build cart for Re-Order request');
            }

            $this->_redirect('checkout/cart/');

            $location = Mage::helper('core/url')->urlDecode(Mage::app()->getRequest()->getParam('return'));
            if (empty($location)) {
                $location = Mage::getUrl('sales/order/history');
            }

            $this->_redirectUrl($location);
        } else {
            $this->_redirect('checkout/cart/');
        }
    }

    protected function _reorderLocal($order)
    {
        $cart = Mage::getSingleton('checkout/cart');
        /* @var $cart Mage_Checkout_Model_Cart */

        $quote = $cart->getQuote();
        $items = $order->getItemsCollection();
        $helper = Mage::helper('epicor_common/cart');

        foreach ($items as $item) {
            /* @var $item Mage_Sales_Model_Order_Item */
            try {
                $product = $item->getProduct();
                /* @var $helper Epicor_Common_Helper_Cart */

                $options = array(
                    'qty' => $item->getQtyOrdered(),
                    'location_code' => $item->getEccLocationCode()
                );

                $quote->addLine($product, $options);
            } catch (Mage_Core_Exception $e) {
                if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                    Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                } else {
                    Mage::getSingleton('checkout/session')->addError($e->getMessage());
                }
                $this->_redirect('*/*/history');
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException($e, Mage::helper('checkout')->__('Cannot add the item to shopping cart.')
                );
                $this->_redirect('checkout/cart');
            }
        }

        $cart->save();
        $this->_redirect('checkout/cart');
    }

}
