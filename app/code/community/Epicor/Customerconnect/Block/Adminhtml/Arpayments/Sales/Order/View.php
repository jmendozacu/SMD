<?php
/**
 * AR Payments Admin Screen sales order view
 * @category   Epicor
 * @package    Epicor_ErpSimulator
 * @author     Epicor Websales Team
 * 
 * 
 */

class Epicor_Customerconnect_Block_Adminhtml_Arpayments_Sales_Order_View extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId    = 'order_id';
        $this->_controller  = 'sales_order';
        $this->_mode        = 'view';
        parent::__construct();
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->setId('sales_order_view');
        $order = $this->getOrder();
        $coreHelper = Mage::helper('core');
    }

    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('sales_order');
    }

    /**
     * Retrieve Order Identifier
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getOrder()->getId();
    }

    public function getHeaderText()
    {
        if ($_extOrderId = $this->getOrder()->getExtOrderId()) {
            $_extOrderId = '[' . $_extOrderId . '] ';
        } else {
            $_extOrderId = '';
        }
        return Mage::helper('sales')->__('AR Payment Reference # %s %s | %s', $this->getOrder()->getRealOrderId(), $_extOrderId, $this->formatDate($this->getOrder()->getCreatedAtDate(), 'medium', true));
    }

    public function getUrl($params='', $params2=array())
    {
        $params2['order_id'] = $this->getOrderId();
        return parent::getUrl($params, $params2);
    }

    public function getEditUrl()
    {
        return $this->getUrl('*/sales_order_edit/start');
    }

    public function getEmailUrl()
    {
        return $this->getUrl('*/*/email');
    }

    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel');
    }

    public function getInvoiceUrl()
    {
        return $this->getUrl('*/sales_order_invoice/start');
    }

    public function getCreditmemoUrl()
    {
        return $this->getUrl('*/sales_order_creditmemo/start');
    }

    public function getHoldUrl()
    {
        return $this->getUrl('*/*/hold');
    }

    public function getUnholdUrl()
    {
        return $this->getUrl('*/*/unhold');
    }

    public function getShipUrl()
    {
        return $this->getUrl('*/sales_order_shipment/start');
    }

    public function getCommentUrl()
    {
        return $this->getUrl('*/*/comment');
    }

    public function getReorderUrl()
    {
        return $this->getUrl('*/sales_order_create/reorder');
    }

    /**
     * Payment void URL getter
     */
    public function getVoidPaymentUrl()
    {
        return $this->getUrl('*/*/voidPayment');
    }

    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/' . $action);
    }

    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getOrder()->getBackUrl()) {
            return $this->getOrder()->getBackUrl();
        }

        return $this->getUrl('*/*/');
    }

    public function getReviewPaymentUrl($action)
    {
        return $this->getUrl('*/*/reviewPayment', array('action' => $action));
    }

}
