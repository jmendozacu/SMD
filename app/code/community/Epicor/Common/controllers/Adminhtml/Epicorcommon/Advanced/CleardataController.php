<?php

/**
 * Common Clear Data Controller
 *
 * This controls the ability to clear data from the system
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 * 
 */
class Epicor_Common_Adminhtml_Epicorcommon_Advanced_CleardataController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    protected function _initAction()
    {
        $this->loadLayout()
                ->_setActiveMenu('epicor_common/advanced/cleardata')
                ->_addBreadcrumb(Mage::helper('epicor_common')->__('Clear Data'), Mage::helper('epicor_common')->__('Clear Data'));
        return $this;
    }

    public function indexAction()
    {
        Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('epicor_common')->__('Using this feature will delete data permanently from this system. Use with caution'));
        $this->_initAction()
                ->renderLayout();
    }

    public function clearAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            $helper = Mage::helper('epicor_common/advanced_cleardata');
            /* @var $helper Epicor_Common_Helper_Advanced_Cleardata */
            

            if (isset($data['locations'])) {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_common')->__('Locations cleared from system'));
                $helper->clearLocations();
            }
            
            if (isset($data['products'])) {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_common')->__('Products cleared from system'));
                $helper->clearProducts();
            }

            if (isset($data['categories'])) {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_common')->__('Categories cleared from system'));
                $helper->clearCategories();
            }

            if (isset($data['erpaccounts'])) {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_common')->__('ERP Accounts cleared from system'));
                $helper->clearErpaccounts();
            }

            if (isset($data['customers'])) {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_common')->__('Customers cleared from system'));
                $helper->clearCustomers();
            }

            if (isset($data['quotes'])) {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_common')->__('Quotes cleared from system'));
                $helper->clearQuotes();
            }

            if (isset($data['orders'])) {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_common')->__('Orders cleared from system'));
                $helper->clearOrders();
            }

            if (isset($data['returns'])) {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_common')->__('Returns cleared from system'));
                $helper->clearReturns();
            }
            
            if (isset($data['lists'])) {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_common')->__('Lists cleared from system'));
                $helper->clearLists();
            }
        }

        $this->_redirect('*/*/index');
    }

}
