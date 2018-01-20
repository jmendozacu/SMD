<?php

require_once('Mage' . DS . 'Checkout' . DS . 'controllers' . DS . 'OnepageController.php');

/**
 * Shopping cart controller
 */
class Epicor_SalesRep_OnepageController extends Mage_Checkout_OnepageController
{

    public function saveContactAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('salesrep_contact', false);

            $salesRepInfo = '';
            $salesRepCustomerId = '';

            if ($data) {
                $salesRepInfo = base64_decode($data);
                $salesRepData = unserialize($salesRepInfo);

                $helper = Mage::helper('epicor_comm');
                /* @var $helper Epicor_Comm_Helper_Data */

                $erpAccount = $helper->getErpAccountInfo();

                if (!empty($salesRepData['ecc_login_id'])) {
                    $collection = Mage::getModel('customer/customer')->getCollection();
                    $collection->addAttributeToFilter('contact_code', $salesRepData['contact_code']);
                    $collection->addAttributeToFilter('erpaccount_id', $erpAccount->getId());
                    $collection->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId());
                    $customer = $collection->getFirstItem();
                    $salesRepCustomerId = $customer->getId();
                }
            }

            $customerSession = Mage::getSingleton('customer/session');
            /* @var $customerSession Mage_Customer_Model_Session */

            $customer = $customerSession->getCustomer();
            /* @var $customer Epicor_Comm_Model_Customer */

            $this->getOnepage()->getQuote()->setEccSalesrepCustomerId($customer->getId());
            $this->getOnepage()->getQuote()->setEccSalesrepChosenCustomerId($salesRepCustomerId);
            $this->getOnepage()->getQuote()->setEccSalesrepChosenCustomerInfo($salesRepInfo);
            $this->getOnepage()->getQuote()->collectTotals()->save();

            $result = array();
            $result['goto_section'] = 'billing';
            $result['update_section'] = array(
                array(
                    'name' => 'billing',
                    'html' => $this->_getBillingHtml()
                ),
                array(
                    'name' => 'shipping',
                    'html' => $this->_getShippingHtml()
                ),
            );

            $this->getOnepage()->getCheckout()
                    ->setStepData('salesrep_contact', 'allow', true)
                    ->setStepData('salesrep_contact', 'complete', true)
                    ->setStepData('billing', 'allow', true);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    protected function _getBillingHtml()
    {
        $layout = Mage::getModel('core/layout');
        /* @var $layout Mage_Core_Model_Layout */
        $update = $layout->getUpdate();
        $update->load('salesrep_checkout_onepage_billing');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        
        unset($update);
        unset($layout);
        return $output;
    }

    protected function _getShippingHtml()
    {
        $layout = Mage::getModel('core/layout');
        /* @var $layout Mage_Core_Model_Layout */
        $update = $layout->getUpdate();
        $update->resetHandles();
        $update->resetUpdates();
        $update->load('salesrep_checkout_onepage_shipping');
        $layout->generateXml();
        $layout->generateBlocks();
        
        $output = $layout->getOutput();
        unset($update);
        unset($layout);
        return $output;
    }

}
