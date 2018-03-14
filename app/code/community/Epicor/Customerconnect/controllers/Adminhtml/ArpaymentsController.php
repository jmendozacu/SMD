<?php
/**
 * AR Payments Admin controller Actions
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Adminhtml_ArpaymentsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('AR Payments'));
        $this->loadLayout();
        $this->_setActiveMenu('sales/sales');
        $this->_addContent($this->getLayout()->createBlock('customerconnect/adminhtml_arpayments_sales_order'));
        $this->renderLayout();
    }
    
    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrder()
    {
        $id    = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);
        
        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This payment no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        
        if (!$order->getEccArpaymentsInvoice()) {
            $this->_redirect('*/sales_order/view', array(
                'order_id' => $order->getId()
            ));
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }
    
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('customerconnect/adminhtml_arpayments_sales_order_grid')->toHtml());
    }
    
    
    /**
     * Init layout, menu and breadcrumb
     *
     * @return Mage_Adminhtml_Sales_OrderController
     */
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('sales/order')->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));
        return $this;
    }
    
    /**
     * View order details
     */
    public function viewAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Orders'));
        
        $order = $this->_initOrder();
        if ($order) {
            
            $isActionsNotPermitted = $order->getActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_PRODUCTS_PERMISSION_DENIED);
            if ($isActionsNotPermitted) {
                $this->_getSession()->addError($this->__('You don\'t have permissions to manage this order because of one or more products are not permitted for your website.'));
            }
            
            $this->_initAction();
            
            $this->_title(sprintf("#%s", $order->getRealOrderId()));
            
            $this->renderLayout();
        }
    }
    
    /**
     * Export AR Payment Details
     */    
    
    public function exportArpaymentsCsvAction()
    {
        $fileName = 'epicor_arpayments.csv';
        $grid     = $this->getLayout()->createBlock('customerconnect/adminhtml_arpayments_sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    
    /**
     * Export AR Payment Details
     */        
    public function exportArpaymentsExcelAction()
    {
        $fileName = 'epicor_arpayments.xml';
        $grid     = $this->getLayout()->createBlock('customerconnect/adminhtml_sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
    
    /**
     * AR Payment Log Grid
     */        
    public function loggridAction()
    {
        
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('customerconnect/adminhtml_arpayments_sales_order_view_tab_log');
        $this->getResponse()->setBody($block->toHtml());
    }
    
    /**
     * ERP Status Actions
     */        
    public function erpstatusAction()
    {
        $order_id  = $this->getRequest()->getParam('order_id');
        $caap_sent = $this->getRequest()->getParam('caap_sent');
        $this->changeErpstatus($order_id, $caap_sent);
        $helper  = Mage::helper('epicor_comm');
        $session = Mage::getSingleton('adminhtml/session');
        $session->addSuccess($helper->__('Payment Erp Status changed'));
        $url = $this->getUrl('adminhtml/arpayments/view', array(
            'order_id' => $order_id,
            'active_tab' => 'arorder_design_details'
        ));
        echo json_encode(array(
            'error' => false,
            'success' => true,
            'ajaxExpired' => true,
            'ajaxRedirect' => $url
        ));
    }
    
    private function changeErpstatus($order_id, $status)
    {
        $caap_message = 'Payment Not Sent';
        $state        = '';
        switch ($status) {
            case 0:
                $caap_message = 'Manually set to : Payment Not Sent';
                $state        = 'processing';
                break;
            case 1:
                $caap_message = 'Manually set to : Payment Sent';
                break;
            case 3:
                $caap_message = 'Manually set to : Erp Error';
                break;
        }
        
        $order = Mage::getModel('sales/order')->load($order_id);
        
        if ($order->getEccCaapSent() != $status) {
            Mage::register("offline_order_{$order->getId()}", true);
            $order->setEccCaapSent($status);
            $order->setEccCaapMessage($caap_message);
            if (!empty($state)) {
                $order->setState($state);
            }
            $order->save();
        }
    }
    
}