<?php

class Epicor_Comm_Adminhtml_Epicorcomm_Sales_OrderController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    private $_helper;

    protected function _initAction()
    {
        $this->loadLayout()
                ->_setActiveMenu('sales/order')
                ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
                ->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));
        return $this;
    }

    public function massAssignErpstatusAction()
    {
        $orders = $this->getRequest()->getParam('order_ids');
        $status = $this->getRequest()->getParam('erp_status');
        $helper = Mage::helper('epicor_comm');
        $session = Mage::getSingleton('adminhtml/session');
        
        if(empty($orders)) {
            $session->addError($helper->__('no orders selected'));
        } else {
            foreach($orders as $order) {
                $this->changeErpstatus($order, $status);
            }
            $session->addSuccess($helper->__(count($orders).' Order Erp Status changed'));
        }
            
        $this->_redirect('adminhtml/sales_order/index');
    }
    
    public function erpstatusAction()
    {


        $order_id = $this->getRequest()->getParam('order_id');
        $gor_sent = $this->getRequest()->getParam('gor_sent');
        
        $this->changeErpstatus($order_id, $gor_sent);



        $helper = Mage::helper('epicor_comm');
        $session = Mage::getSingleton('adminhtml/session');
        $session->addSuccess($helper->__('Order Erp Status changed'));
        


        $url = $this->getUrl('adminhtml/sales_order/view', array('order_id' => $order_id, 'active_tab' => 'order_design_details'));
        echo json_encode(array('error' => false, 'success' => true, 'ajaxExpired' => true, 'ajaxRedirect' => $url));


    }
    
    private function changeErpstatus($order_id,$status)
    {
        $gor_message = 'Order Not Sent';
        $state = '';
        switch ($status) {
            case 0:
                $gor_message = 'Manually set to : Order Not Sent';
                $state = 'processing';
                break;
            case 1:
                $gor_message = 'Manually set to : Order Sent';
                break;
            case 2:
                $gor_message = 'Manually set to : Never Send';
                break;
            case 3:
                $gor_message = 'Manually set to : Erp Error';
                break;
        }

        $order = Mage::getModel('sales/order')->load($order_id);

        if ($order->getGorSent() != $status) {
            Mage::register("offline_order_{$order->getId()}", true);
            $order->setGorSent($status);
            $order->setGorMessage($gor_message);
            if (!empty($state)) {
                $order->setState($state);
            }
            $order->save();
        }
    }

    /**
     * 
     * @return Epicor_Comm_Helper_Data
     */
    protected function _getHelper()
    {
        if (!$this->_helper)
            $this->_helper = Mage::helper('epicor_comm');
        return $this->_helper;
    }

    public function saveproductsAction()
    {
        $order_id = $this->getRequest()->get('order_id');
        if ($order_id) {
            $confirmed = $this->getRequest()->get('confirmed');
            $product_data = $this->getRequest()->get('products');
            $products = json_decode($product_data);
            
            $order = Mage::getModel('sales/order')->load($order_id);
            /* @var $order Mage_Sales_Model_Order */

            $additional_grand_total = 0;
            foreach ($products as $product_id => $product_data) {
                $qty = $product_data->qty;
                $price = $product_data->custom_price;
                $row_total = $qty * $price;
                $additional_grand_total += $row_total;
            }

            $maxCharge = $this->_getHelper()->getMaxAdditionalCharge($order);
            $maxTotal = $order->getGrandTotal() + $maxCharge;
            $newTotal = $order->getGrandTotal() + $additional_grand_total;

            if (!$confirmed) {
                $return_data = array(
                            'additional_amount' => $additional_grand_total,
                            'orig_grand_total' => $order->getGrandTotal(),
                            'new_grand_total' => $newTotal,
                            'max_grand_total' => $maxTotal,
                            'grand_total_diff' => $maxTotal - $newTotal,
                            'valid_amendment' => ($newTotal - $maxTotal) < 0.01,
                    );
                Mage::register('return_data', $return_data);
                $products_data = array();
                foreach ($products as $product_id => $product_values) {
                    $product = Mage::getModel('catalog/product')->load($product_id);
                    /* @var $product Mage_Catalog_Model_Product */
                    $products_data[$product_id] = new Varien_Object(array(
                        'id' => $product_id,
                        'price' => $product_values->custom_price,
                        'name' => $product->getName(),
                        'sku' => $product->getSku(),
                        'qty' => $product_values->qty,
                        'subtotal' => $product_values->qty * $product_values->custom_price
                    )); 
                }
                sort($products_data);
                Mage::register('products', $products_data);
                
                $return_data['html'] = $this->getLayout()->createBlock('epicor_comm/adminhtml_sales_order_view_addproducts_summary')->toHtml();
                echo json_encode($return_data);
            } else {
//                $this->helper('tax')->getPrice($_product, $_product->getPrice(), true);
                
                foreach ($products as $product_id => $product_data) {

                    $product = Mage::getModel('catalog/product')->load($product_id);
                    /* @var $product Mage_Catalog_Model_Product */

                    $qty = $product_data->qty;
                    $price = $product_data->custom_price;
                    $row_total = $qty * $price;

                    $order_item = Mage::getModel('sales/order_item')
                            ->setStoreId(NULL)
                            ->setQuoteItemId(NULL)
                            ->setQuoteParentItemId(NULL)
                            ->setProductId($product_id)
                            ->setProductType($product->getTypeId())
                            ->setQtyBackordered(NULL)
                            ->setTotalQtyOrdered($qty)
                            ->setQtyOrdered($qty)
                            ->setName($product->getName())
                            ->setSku($product->getSku())
                            ->setPrice($price)
                            ->setBasePrice($price)
                            ->setOriginalPrice($price)
                            ->setRowTotal($row_total)
                            ->setBaseRowTotal($row_total)
                            ->setOrder($order);

                    echo "Saving order item...\n";
                    $order_item->save();
                }
                $order->setBaseSubtotal($order->getBaseSubtotal() + $additional_grand_total);
                $order->setSubtotal($order->getSubtotal() + $additional_grand_total);
                $order->setBaseGrandTotal($order->getBaseGrandTotal() + $additional_grand_total);
                $order->setGrandTotal($order->getGrandTotal() + $additional_grand_total);
                $payment = $order->getPayment();
                $payment->setAmountOrdered($payment->getAmountOrdered() + $additional_grand_total);
                $payment->setBaseAmountOrdered($payment->getBaseAmountOrdered() + $additional_grand_total);
                $payment->save();
                $order->save();
                $order->sendOrderUpdateEmail(true, 'New Item/s have been added to your order');
            }
        }
    }

    public function addproductAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function productsearchgridAction()
    {
        
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('epicor_comm/adminhtml_sales_order_view_addproducts_search_grid')->toHtml()
        );
    }
    public function loggridAction() {
        
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('epicor_comm/adminhtml_sales_order_view_tab_log');   
        $this->getResponse()->setBody($block->toHtml());
    }   
    
}
