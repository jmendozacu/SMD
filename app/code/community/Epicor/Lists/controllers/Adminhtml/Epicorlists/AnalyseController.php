<?php

/**
 * List analyse actions
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Adminhtml_Epicorlists_AnalyseController extends Epicor_Comm_Controller_Adminhtml_Abstract
{
    /**
     * List list page
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function analyseAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $data = new Varien_Object($data);
            
            
            $collection = Mage::getModel('epicor_lists/list')->getCollection();
            /* @var $collection Epicor_Lists_Model_Resource_List_Collection */

            $collection->filterActive();
            
            if ($data->getErpaccountId()) {
                $filter = Mage::getModel('epicor_lists/list_filter_Erpaccount');
                $filter->setErpAccountId($data->getErpaccountId());
                $filter->filter($collection);
            }
            
            if ($data->getStoreId()) {
                $mixed = explode('_', $data->getStoreId());
                if ($mixed[0] == 'website') {
                    $filter = Mage::getModel('epicor_lists/list_filter_website');
                    $filter->setWebsiteId($mixed[1]);
                    $filter->filter($collection);
                } elseif ($mixed[0] == 'store') {
                    $filter = Mage::getModel('epicor_lists/list_filter_store');
                    $filter->setStoreGroupId($mixed[1]);
                    $filter->filter($collection);
                }
            }

            if ($data->getCustomerId()) {
                $filter = Mage::getModel('epicor_lists/list_filter_customer');
                $filter->setCustomerId($data->getCustomerId());
                $filter->filter($collection);
            }

            if ($data->getCustomerType() && in_array($data->getCustomerType(), array('B', 'C'))) {
                $filter = Mage::getModel('epicor_lists/list_filter_erpaccounttype');
                $filter->setTypeFilter($data->getCustomerType());
                $filter->filter($collection);
            }
            
            $collection->addOrder('priority', 'DESC')->addOrder('created_date', 'DESC');
            
            $collection->groupById();
            
            $listIds = array();
            foreach ($collection->getItems() as $item) {
                if (!isset($listIds[$item->getPriority()])) {
                    $listIds[$item->getPriority()] = $item->getId();
                }
            }
            
            Mage::register('epicor_lists_analyse_ids', $listIds);
            
            
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_redirect('*/*/index');
        }
    }
    
    public function listproductsAction()
    {
        if ($this->getRequest()->get('grid')) {
            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('epicor_lists/adminhtml_list_analyse_products_grid')->toHtml()
            );
        } else {
            $data = $this->getRequest()->getPost('data');
            $data = json_decode(base64_decode($data), true);
            if ($data) {
                Mage::getSingleton('admin/session')->setAnalyseProductsData($data);
                $this->getResponse()->setBody(
                        $this->getLayout()->createBlock('epicor_lists/adminhtml_list_analyse_products')->toHtml()
                );
            }
        }
    }
    public function listAllproductsAction()
    {
        if ($this->getRequest()->get('grid')) {
            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('epicor_lists/adminhtml_list_analyse_allproducts_grid')->toHtml()
            );
        } else {
            $data = $this->getRequest()->getPost('data');
            $data = json_decode(base64_decode($data), true);
            if ($data) {
                Mage::getSingleton('admin/session')->setAnalyseProductsData($data);
                $this->getResponse()->setBody(
                        $this->getLayout()->createBlock('epicor_lists/adminhtml_list_analyse_allproducts')->toHtml()
                );
            }
        }
    }

}
