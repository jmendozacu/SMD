<?php

/**
 * Locations admin controller
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 * 
 */
class Epicor_Comm_Adminhtml_Epicorcomm_LocationsController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
                        ->isAllowed('epicor_comm/locations');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Export locations grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'locations.csv';
        $content = $this->getLayout()->createBlock('epicor_comm/adminhtml_locations_list_grid')
                ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export locations grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName = 'locations.xml';
        $content = $this->getLayout()->createBlock('epicor_comm/adminhtml_locations_list_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */

        Mage::register('location', $location);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function loggridAction()
    {

        $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */

        Mage::register('location', $location);

        $block = $this->getLayout()->createBlock('epicor_comm/adminhtml_locations_edit_tab_log')
                ->setUseAjax(true);

        $this->getResponse()->setBody($block->toHtml());
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->getParam('id'));
            /* @var $location Epicor_Comm_Model_Location */
            
            //when creating a new location, it should auto-choose the default country if the country field is empty in admin
            if(empty($data['country'])) {
                $defaultCountry = Mage::getStoreConfig('general/country/default');
                $data['country'] = $defaultCountry;
            }            

            if (isset($data['county_id'])) {
                $region = Mage::getModel('directory/region')->load($data['county_id']);
                /* @var $region Mage_Directory_Model_Region */
                $data['county_code'] = $region->getCode();
            }

            $location->addData($data);
            
            if (!$location->getSource()) {
                $location->setSource('web');
                $location->setDummy(0);
            }

            
            // Handle Stores Tab
            $saveStores = $this->getRequest()->getParam('selected_store');
            if (!is_null($saveStores)) {
                $stores = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['stores']));
                $location->setFullStores($stores);
            }

            $helper = Mage::helper('epicor_comm/locations');
            /* @var $helper Epicor_Comm_Helper_Locations */
            
            // Handle ErpAccounts Tab
            $saveErpAccounts = $this->getRequest()->getParam('selected_erpaccount');

            if (!is_null($saveErpAccounts)) {
                $erpAccounts = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['erpaccounts']));
                $helper->syncErpAccountsToLocation($location->getCode(), $erpAccounts);
            }

            
            // Handle Customers Tab
            $saveCustomers = $this->getRequest()->getParam('selected_customer');

            if (!is_null($saveCustomers)) {
                $customers = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['customers']));
                $helper->syncCustomersToLocation($location->getCode(), $customers);
            }
            
            
            // Handle Products Tab
            $saveProducts = $this->getRequest()->getParam('selected_product');
            
            if (!is_null($saveProducts)) {
                $products = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['products']));
                $helper->syncProductsToLocation($location->getCode(), $products);
            }
            
            $location->save();
            
            // Handle Related Locations Tab
            $saveRelatedlocations = $this->getRequest()->getParam('selected_location');
            
            if (!is_null($saveRelatedlocations)) {
                $relatedlocations = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['relatedlocations']));
                $helper->syncRelatedLocations($location->getId(), $relatedlocations);
            }
            
            $saveGroups = $this->getRequest()->getParam('selected_group');
            if (!is_null($saveGroups)) {
                $groups = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['groups']));
                $helper->updateGroupLocations($groups, $location->getId());
            }
            
            $session = Mage::getSingleton('adminhtml/session');
            /* @var $helper Epicor_Comm_Helper_Data */
            $session->addSuccess($this->__('Location %s Saved Successfully', $location->getErpCode()));
            
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', array('id' => $location->getId()));
            } else {
                $this->_redirect('*/*/');
            }
        } else {
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $this->delete($id);
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $ids = (array) $this->getRequest()->getParam('locationid');
        foreach ($ids as $id) {
            $this->delete($id, true);
        }
        $helper = Mage::helper('epicor_comm');
        $session = Mage::getSingleton('adminhtml/session');
        $session->addSuccess($helper->__(count($ids) . ' Message log entries deleted'));
        $this->_redirect('*/*/');
    }

    public function storesAction()
    {
        $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */

        Mage::register('location', $location);
        $this->loadLayout();
        $this->getLayout()->getBlock('stores_grid')
                ->setSelected($this->getRequest()->getPost('stores', null));
        $this->renderLayout();
    }

    public function storesgridAction()
    {
        $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */

        Mage::register('location', $location);
        $stores = $this->getRequest()->getParam('stores');
        $this->loadLayout();
        $this->getLayout()->getBlock('stores_grid')->setSelected($stores);
        $this->renderLayout();
    }

    public function erpaccountsAction()
    {
        $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */

        Mage::register('location', $location);
        $this->loadLayout();
        $this->getLayout()->getBlock('erpaccounts_grid')
                ->setSelected($this->getRequest()->getPost('erpaccounts', null));
        $this->renderLayout();
    }

    public function erpaccountsgridAction()
    {
        $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */

        Mage::register('location', $location);
        $erpaccounts = $this->getRequest()->getParam('erpaccounts');
        $this->loadLayout();
        $this->getLayout()->getBlock('erpaccounts_grid')->setSelected($erpaccounts);
        $this->renderLayout();
    }
    
    public function productsAction()
    {
        $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */

        $products = $this->getRequest()->getParam('products');
        Mage::register('location', $location);
        $this->loadLayout();
        $this->getLayout()->getBlock('products_grid')->setSelected($products);
        $this->renderLayout();
    }

    public function productsgridAction()
    {
        $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */

        Mage::register('location', $location);
        $products = $this->getRequest()->getParam('products');
        $this->loadLayout();
        $this->getLayout()->getBlock('products_grid')->setSelected($products);
        $this->renderLayout();
    }

    public function customersAction()
    {
        $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */

        Mage::register('location', $location);
        $this->loadLayout();
        $this->getLayout()->getBlock('customers_grid')
                ->setSelected($this->getRequest()->getPost('customers', null));
        $this->renderLayout();
    }

    public function customersgridAction()
    {
        $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */

        Mage::register('location', $location);
        $customers = $this->getRequest()->getParam('customers');
        $this->loadLayout();
        $this->getLayout()->getBlock('customers_grid')->setSelected($customers);
        $this->renderLayout();
    }

    private function delete($id, $mass = false)
    {
        $model = Mage::getModel('epicor_comm/location');
        /* @var $model Epicor_Comm_Model_Location */
        $session = Mage::getSingleton('adminhtml/session');
        /* @var $helper Epicor_Comm_Helper_Data */
        $helper = Mage::helper('epicor_comm');
        if ($id) {
            $model->load($id);
            if ($model->getId()) {
                if ($model->delete()) {
                    if (!$mass) {
                        $session->addSuccess($helper->__('Location deleted'));
                    }
                } else {
                    $session->addError($helper->__('Could not delete Location ' . $id));
                }
            }
        }
    }
    
    public function relatedLocationsAction()
    {
        $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */

        $relatedLocations = $this->getRequest()->getParam('relatedlocations');
        Mage::register('location', $location);
        $this->loadLayout();
        $this->getLayout()->getBlock('relatedlocations_grid')->setSelected($relatedLocations);
        $this->renderLayout();
    }
    
    public function relatedlocationsgridAction()
    {
        $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */

        Mage::register('location', $location);
        $relatedLocations = $this->getRequest()->getParam('relatedlocations');
        $this->loadLayout();
        $this->getLayout()->getBlock('relatedlocations_grid')->setSelected($relatedLocations);
        $this->renderLayout();
    }
    
    public function groupingsAction()
    {
        $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */
        $groups = $this->getRequest()->getParam('groups');
        Mage::register('location', $location);
        $this->loadLayout();
        $this->getLayout()->getBlock('groupings')->setSelected($groups);
        $this->renderLayout();
    }

    public function groupingsgridAction()
    {
        $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */
        $groups = $this->getRequest()->getParam('groups');
        Mage::register('location', $location);
        $this->loadLayout();
        $this->getLayout()->getBlock('groupings')->setSelected($groups);
        $this->renderLayout();
    }
    
    public function groupingspostAction()
    {
        $data = new Varien_Object($this->getRequest()->getParams());
        if ($data->getGroupId() == '') {
            $data->unsId();
        } else {
            $data->setData('id',$data->getGroupId());
        }
        $group = Mage::getModel('epicor_comm/location_groupings')->load($data->getId());
        $group->setData($data->getData())->save();
        if ($group->getGrouplinks()) {
            $groupLinks = explode(',', $group->getGrouplinks());
            $helper = Mage::helper('epicor_comm/locations');
            $helper->syncGroupLocations($group->getId(), $groupLinks);
        }
        return;
    }
    
    public function deletegroupAction()
    {
        $groupId = $this->getRequest()->getParam('id');
        $group = Mage::getModel('epicor_comm/location_groupings')->load($groupId);
        $group->delete();
        return;
    }
    
    public function editGroupAction()
    {
        $data = new Varien_Object($this->getRequest()->getParams());
        $group = Mage::getModel('epicor_comm/location_groupings')->load($data->getGroupId());
        Mage::register('group', $group);
        $response = array(
            'location_groupings_form'   => $this->getLayout()->createBlock('epicor_comm/adminhtml_locations_edit_tab_groupings_form')->toHtml()
        );
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
    
    public function grouplocationsgridAction()
    {
        $location = Mage::getModel('epicor_comm/location')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */
        Mage::register('location', $location);
        $group = Mage::getModel('epicor_comm/location_groupings')->load($this->getRequest()->get('group_id'));
        Mage::register('group', $group);
        
        $groupLocations = Mage::getModel('epicor_comm/location_grouplocations')->getCollection()
                        ->addFieldToFilter('group_id', $this->getRequest()->get('group_id'))
                        ->getAllLocationIds();
        $this->loadLayout();
        $this->getLayout()->getBlock('grouplocations_grid')->setSelected($groupLocations);
        $this->renderLayout();
    }
}
