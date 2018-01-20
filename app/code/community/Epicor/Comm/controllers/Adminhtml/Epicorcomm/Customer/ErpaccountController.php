<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ErpaccountController
 *
 * @author David.Wylie
 */
class Epicor_Comm_Adminhtml_Epicorcomm_Customer_ErpaccountController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    protected $_aclId = 'customer/erpaccount';

    protected function _isAllowed()
    {

        if ($this->getRequest()->getActionName() == 'listerpaccounts') {
            return true;
        } else {
            return Mage::getSingleton('admin/session')
                            ->isAllowed('customer/erpaccount');
        }
    }

    protected function _initAction()
    {
        $this->loadLayout()
                ->_setActiveMenu('customer/erpaccount')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Erp Accounts'), Mage::helper('adminhtml')->__('Erp Accounts'));
        return $this;
    }

    public function listerpaccountsAction()
    {
        if ($this->getRequest()->get('grid')) {
            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_attribute_grid')->toHtml()
            );
        } else {
            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_attribute')->toHtml()
            );
        }
    }

    public function listskuproductsAction()
    {
        if ($this->getRequest()->get('grid')) {
            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_edit_tab_sku_products_grid')->toHtml()
            );
        } else {
            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_edit_tab_sku_products')->toHtml()
            );
        }
    }

    /**
     * Export customer grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'erpaccounts.csv';
        $content = $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_grid')
                ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export customer grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName = 'erpaccounts.xml';
        $content = $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function customersAction()
    {
        $this->_initErpAccount();
        $this->loadLayout();
        $this->getLayout()->getBlock('erp_customer_grid')
                ->setSelected($this->getRequest()->getPost('customers', null));
        $this->renderLayout();
    }

    public function customersgridAction()
    {
        $this->_initErpAccount();
        $customers = $this->getRequest()->getParam('customers');
        $this->loadLayout();
        $this->getLayout()->getBlock('erp_customer_grid')->setSelected($customers);
        $this->renderLayout();
    }

    public function locationsAction()
    {
        $this->_initErpAccount();
        $this->loadLayout();
        $this->getLayout()->getBlock('erp_locations_grid')
                ->setSelected($this->getRequest()->getPost('locations', null));
        $this->renderLayout();
    }

    public function locationsgridAction()
    {
        $this->_initErpAccount();
        $customers = $this->getRequest()->getParam('locations');
        $this->loadLayout();
        $this->getLayout()->getBlock('erp_locations_grid')->setSelected($customers);
        $this->renderLayout();
    }

    public function storesAction()
    {
        $this->_initErpAccount();
        $this->loadLayout();
        $this->getLayout()->getBlock('stores_grid')
                ->setSelected($this->getRequest()->getPost('stores', null));
        $this->renderLayout();
    }

    public function storesgridAction()
    {
        $this->_initErpAccount();
        $stores = $this->getRequest()->getParam('stores');
        $this->loadLayout();
        $this->getLayout()->getBlock('stores_grid')->setSelected($stores);
        $this->renderLayout();
    }

    public function salesrepsAction()
    {
        $this->_initErpAccount();
        $this->loadLayout();
        $this->getLayout()->getBlock('salesreps_grid')
                ->setSelected($this->getRequest()->getPost('salesreps', null));
        $this->renderLayout();
    }

    public function salesrepsgridAction()
    {
        $this->_initErpAccount();
        $salesreps = $this->getRequest()->getParam('salesreps');
        $this->loadLayout();
        $this->getLayout()->getBlock('salesreps_grid')->setSelected($salesreps);
        $this->renderLayout();
    }

    public function logsgridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_edit_tab_log')->toHtml()
        );
    }

    public function skugridAction()
    {
        $this->_initErpAccount();
        $this->loadLayout(false)->renderLayout();
    }

    public function skutabAction()
    {
        $this->_initErpAccount();
        $this->loadLayout(false)->renderLayout();
    }

    private function _initErpAccount()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('epicor_comm/customer_erpaccount');
        if ($id) {
            $model->load($id);
            if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            }
        }

        Mage::register('customer_erp_account', $model);
    }

    public function indexAction()
    {
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction()
    {

        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('epicor_comm/customer_erpaccount');
        if ($id) {
            $model->load($id);
            if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_comm')->__('Customer Erp Account not found'));
                $this->_redirect('*/*/');
            }
        }

        Mage::register('customer_erp_account', $model);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function newAction()
    {
        $model = Mage::getModel('epicor_comm/customer_erpaccount');
        Mage::register('customer_erp_account', $model);

        $this->loadLayout()->renderLayout();
    }

    public function createAction()
    {

        if ($data = $this->getRequest()->getPost()) {
            try {
                $cnc = Mage::getModel('epicor_comm/message_request_cnc');
                /* @var $cnc Epicor_Comm_Model_Message_Request_Cnc */

                if ($cnc->isActive()) {

                    $helper = Mage::helper('epicor_comm/messaging');
                    /* @var $helper Epicor_Comm_Helper_Messaging */

                    if (strpos($data['store'], 'store_') !== false) {
                        $storeId = str_replace('store_', '', $data['store']);
                        $brand = $helper->getStoreBranding($storeId);
                    } else {
                        $webId = str_replace('website_', '', $data['store']);
                        $brand = $helper->getWebsiteBranding($webId);
                    }

                    $cnc->setBranding($brand);

                    $erpAccount = Mage::getModel('epicor_comm/customer_erpaccount');
                    /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
                    $erpAccount->setName($data['name']);
                    $erpAccount->setEmail($data['email']);

                    $erpAccount->addAddress('registered', 'registered', $data['registered']);
                    $erpAccount->addAddress('delivery', 'delivery', $data['delivery']);
                    $erpAccount->addAddress('invoice', 'invoice', $data['invoice']);

                    $cnc->setAccount($erpAccount);
                    if ($cnc->sendMessage()) {
                        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('ERP Account created'));
                        Mage::getSingleton('adminhtml/session')->setFormData(false);
                    } else {
                        $error = $this->__('ERP Account creation failed. Error - %s', $cnc->getStatusDescriptionText());
                    }
                } else {
                    $error = $this->__('ERP Account creation failed. CNC Message not Active');
                }
            } catch (Exception $e) {
                $error = $this->__('ERP Account creation failed. Error  - %s', $e->getMessage());
            }
        } else {
            $error = $this->__('No data found to save');
        }

        if ($error) {
            $this->_getSession()->setFormData($data);
            $this->_getSession()->addError($error);
            $this->_redirect('*/*/new');
        } else {
            $this->_redirect('*/*/index');
        }
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id != null) {
            $this->delete($id);
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the ERP Account to delete.'));
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $ids = (array) $this->getRequest()->getParam('accounts');

        foreach ($ids as $id) {
            $this->delete($id, true);
        }
        $helper = Mage::helper('epicor_comm');
        $session = Mage::getSingleton('adminhtml/session');
        $session->addSuccess($helper->__(count($ids) . ' ERP Accounts deleted'));
        $this->_redirect('*/*/');
    }

    private function delete($id, $mass = false)
    {
        try {
            $model = Mage::getModel('epicor_comm/customer_erpaccount')->load($id);

            if ($model->delete()) {
                if (!$mass) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_comm')->__('The ERP Account has been deleted from the site.'));
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError('Could not delete ERP Account ' . $model->getErpCode());
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

    public function saveAction()
    {

        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('epicor_comm/customer_erpaccount');
            /* @var $model Epicor_Comm_Model_Customer_Erpaccount */
            if ($id) {
                $model->load($id);
            }
            $data['name'] = $model->getName();
            $model->addData($data);
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setId($id);
                }
                $customAddressAllowed = $this->getRequest()->getParam('custom_address_allowed');
                $updateValue = $customAddressAllowed == '' ? null : $customAddressAllowed;
                $model->setCustomAddressAllowed($updateValue);
                if (isset($data['allow_masquerade'])) {
                    $allowed = $data['allow_masquerade'] == '' ? null : $data['allow_masquerade'];
                    $model->setAllowMasquerade($allowed);
                }

                if (isset($data['allow_masquerade_cart_clear'])) {
                    $allowed = $data['allow_masquerade_cart_clear'] == '' ? null : $data['allow_masquerade_cart_clear'];
                    $model->setAllowMasqueradeCartClear($allowed);
                }

                if (isset($data['allow_masquerade_cart_reprice'])) {
                    $allowed = $data['allow_masquerade_cart_reprice'] == '' ? null : $data['allow_masquerade_cart_reprice'];
                    $model->setAllowMasqueradeCartReprice($allowed);
                }

                if (isset($data['newparent']) && isset($data['newparent']['account']) && !empty($data['newparent']['account'])) {
                    $model->addParent($data['newparent']['account'], $data['newparent']['type']);
                }

                if (isset($data['newchild']) && isset($data['newchild']['account']) && !empty($data['newchild']['account'])) {
                    $model->addChild($data['newchild']['account'], $data['newchild']['type'], true);
                }

                if (isset($data['deleted_parents']) && !empty($data['deleted_parents'])) {
                    foreach ($data['deleted_parents'] as $type) {
                        $model->removeParentByType($type);
                    }
                }

                if (isset($data['deleted_children']) && !empty($data['deleted_children'])) {
                    foreach ($data['deleted_children'] as $child) {
                        $data = unserialize(base64_decode($child));
                        $model->removeChild($data['id'], $data['type']);
                    }
                }


                if (isset($data['is_warranty_customer'])) {
                    $model->setIsWarrantyCustomer(true);
                } else {
                    $model->setIsWarrantyCustomer(false);
                }

                if (isset($data['allow_backorders'])) {
                    $model->setAllowBackorders(true);
                } else {
                    $model->setAllowBackorders(false);
                }

                if (isset($data['cpn_editing'])) {
                    if ($data['cpn_editing'] == '') {
                        $model->setCpnEditing(null);
                    } else {
                        $model->setCpnEditing($data['cpn_editing']);
                    }
                }

                if (isset($data['po_mandatory'])) {
                    if ($data['po_mandatory'] == '') {
                        $model->setPoMandatory(null);
                    } else {
                        $model->setPoMandatory($data['po_mandatory']);
                    }
                }

                if (isset($data['is_branch_pickup_allowed'])) {
                    if ($data['is_branch_pickup_allowed'] == '') {
                        $model->setIsBranchPickupAllowed(null);
                    } else {
                        $model->setIsBranchPickupAllowed($data['is_branch_pickup_allowed']);
                    }
                }

                //process Contracts if lists enabled
                if (Mage::getStoreConfigFlag('epicor_lists/global/enabled')) {
                    $this->processContracts($model, $data);
                }

                //default location
                $erp_default_location = $this->getRequest()->getParam('erp_default_location');
                $model->setDefaultLocationCode($erp_default_location);

                $saveLocations = $this->getRequest()->getParam('in_location');
                if (!is_null($saveLocations)) {
                    $this->saveLocations($model, $data);
                }
                $saveLists = $this->getRequest()->getPost('links');
                if (!is_null($saveLists)) {
                    $this->saveLists($model, $data);
                }
                if (!is_null($this->getRequest()->getPost('links'))) {
                    $this->saveMasterShoppers($model, $data);
                }

                $model->save();

                $saveCustomer = $this->getRequest()->getParam('in_customer');
                if (!is_null($saveCustomer)) {
                    $this->saveCustomers($model, $data);
                }

                $saveStores = $this->getRequest()->getParam('selected_store');
                if (!is_null($saveStores)) {
                    $this->saveStores($model, $data);
                }

                $saveSalesRep = $this->getRequest()->getParam('selected_salesreps');
                if (!is_null($saveSalesRep)) {
                    $this->saveSalesReps($model, $data);
                }

                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('epicor_comm')->__('Error saving Erp Account'));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_comm')->__('ERP Account was successfully saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($model && $model->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            }

            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_comm')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

    private function saveCustomers($erpAccount, $data)
    {
        $customers = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['customers']));

        // load current and check if any need to be removed

        $collection = Mage::getResourceModel('customer/customer_collection');

        if ($erpAccount->isTypeSupplier()) {
            $collection->addFieldToFilter('supplier_erpaccount_id', $erpAccount->getId());
        } else if ($erpAccount->isTypeCustomer()) {
            $collection->addFieldToFilter('erpaccount_id', $erpAccount->getId());
        }

        $existing = array();
        /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        foreach ($collection->getItems() as $customer) {
            if (!in_array($customer->getId(), $customers)) {

                if ($erpAccount->isTypeSupplier()) {
                    $customer->setSupplierErpaccountId(false);
                } else if ($erpAccount->isTypeCustomer()) {
                    $customer->setErpaccountId(false);
                }

                $customer->save();
            } else {
                $existing[] = $customer->getId();
            }
        }

        // loop through passed values and only update customers who are new
        foreach ($customers as $customerId) {
            if (!in_array($customerId, $existing)) {
                $customerModel = Mage::getModel('customer/customer')->load($customerId);
                if (!$customerModel->isObjectNew()) {
                    if ($erpAccount->isTypeSupplier()) {
                        $customerModel->setSupplierErpaccountId($erpAccount->getId());
                    } else if ($erpAccount->isTypeCustomer()) {
                        $customerModel->setErpaccountId($erpAccount->getId());
                    }
                    $customerModel->save();
                }
            }
        }
    }

    /**
     * Saves locations for this erp account
     * 
     * @param Epicor_Comm_Model_Customer_Erpaccount $erpAccount
     * @param array $data
     */
    private function saveLocations($erpAccount, $data)
    {
        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */

        $locations = array_keys($helper->decodeGridSerializedInput($data['links']['locations']));
        $erpAccount->updateLocations($locations);
    }

    /**
     * Saves lists for this erp account
     * 
     * @param Epicor_Comm_Model_Customer_Erpaccount $erpAccount
     * @param array $data
     */
    private function saveLists($erpAccount, $data)
    {
        $lists = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['lists']));
        $erpAccount->removeLists($erpAccount->getLists());
        $erpAccount->addLists($lists);
    }

    /**
     * Saves master shoppers for this erp account
     *
     * @param Epicor_Comm_Model_Customer_Erpaccount $erpAccount
     * @param array $data
     */
    public function saveMasterShoppers($erpAccount, $data)
    {
        if (array_key_exists('ecc_master_shopper', $data['links'])) {
            $masters = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['ecc_master_shopper']));
        } else {
            $masters = array();
        }
        $customers = $erpAccount->getCustomers();
        foreach ($customers as $customer) {
            /* @var $customer Epicor_Comm_Model_Customer */
            if (in_array($customer->getId(), $masters)) {
                $customer->setEccMasterShopper(1);
            } else {
                $customer->setEccMasterShopper(0);
            }
            $customer->getResource()->saveAttribute($customer, 'ecc_master_shopper');
        }
    }

    private function saveStores($erpAccount, $data)
    {
        if (!Mage::getStoreConfigFlag('Epicor_Comm/brands/erpaccount')) {
            $stores = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['stores']));

            // load current and check if any need to be removed

            $storeCollection = Mage::getModel('epicor_comm/customer_erpaccount_store')->getCollection();
            /* @var $storeCollection Epicor_Comm_Model_Mysql4_Customer_Erpaccount_Store_Collection */
            $storeCollection->addFieldToFilter('erp_customer_group', $erpAccount->getId());

            $existing = array();

            foreach ($storeCollection->getItems() as $store) {
                if (!in_array($store->getStore(), $stores)) {
                    $store->delete();
                } else {
                    $existing[] = $store->getStore();
                }
            }

            if (!empty($stores)) {
                foreach ($stores as $store) {
                    if (!in_array($store, $existing)) {
                        $erp_group_store = Mage::getModel('epicor_comm/customer_erpaccount_store');
                        /* @var $erp_group_store Epicor_Comm_Model_Customer_Erpaccount_Store */
                        $erp_group_store->setErpCustomerGroup($erpAccount->getId());
                        $erp_group_store->setStore($store);
                        $erp_group_store->save();
                    }
                }
            }
        }
    }

    private function saveSalesReps($erpAccount, $data)
    {

        if (Mage::helper('epicor_comm')->isModuleEnabled('Epicor_SalesRep')) {//!Mage::getStoreConfigFlag('Epicor_Comm/brands/erpaccount')) {
            //echo "here1";die;
            $salesreps = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['salesreps']));
//echo '<pre>';print_r($data);
//print_r($salesreps);die;
            // load current and check if any need to be removed

            $salesrepsCollection = Mage::getModel('epicor_salesrep/erpaccount')->getCollection();
            /* @var $salesrepsCollection Epicor_Salesrep_Model_Salesrep_Erpaccount */
            $salesrepsCollection->addFieldToFilter('erp_account_id', $erpAccount->getId());

            $existing = array();

            foreach ($salesrepsCollection->getItems() as $salesrep) {
                if (!in_array($salesrep->getSalesRepAccountId(), $salesreps)) {
                    $salesrep->delete();
                } else {
                    $existing[] = $salesrep->getSalesRepAccountId();
                }
            }

            if (!empty($salesreps)) {
                foreach ($salesreps as $salesrep) {
                    if (!in_array($salesrep, $existing)) {
                        $erp_group_store = Mage::getModel('epicor_salesrep/erpaccount');
                        /* @var $erp_group_store Epicor_Salesrep_Model_Salesrep_Erpaccount */
                        $erp_group_store->setErpAccountId($erpAccount->getId());
                        $erp_group_store->setSalesRepAccountId($salesrep);
                        $erp_group_store->save();
                    }
                }
            }
        }
    }

    public function massGroupassignAction()
    {
        $accountIds = (array) $this->getRequest()->getParam('accounts');
        $groupId = $this->getRequest()->getParam('customerGroup');
        foreach ($accountIds as $accountId) {
            $model = Mage::getModel('epicor_comm/customer_erpaccount')->load($accountId);
            $model->setmagentoId($groupId);
            $model->save();
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('b2b')->__('The Customer Groups have been assigned.'));
        $this->_redirect('*/*/');
    }

    public function getRegionsAction()
    {
        return trim(Mage::helper('directory')->getRegionJson());
    }

    public function massCpnEditingAction()
    {
        $accountIds = (array) $this->getRequest()->getParam('accounts');
        $cpnEditing = $this->getRequest()->getParam('cpnEditing');
        $cpnEditing = $cpnEditing == '' ? null : $cpnEditing;

        foreach ($accountIds as $accountId) {
            $model = Mage::getModel('epicor_comm/customer_erpaccount')->load($accountId);
            $model->setCpnEditing($cpnEditing);
            $model->save();
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('b2b')->__('The CPN Editing values have been changed.'));
        $this->_redirect('*/*/');
    }

    public function customerskupostAction()
    {

        $response = array();
        $response['type'] = 'success-msg';
        $response['message'] = Mage::helper('epicor_comm')->__('SKU was successfully saved.');

        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('entity_id');
            $model = Mage::getModel('epicor_comm/customer_sku');
            /* @var $model Epicor_Comm_Model_Customer_Sku */

            try {
                if ($id) {
                    $model->load($id);
                }

                $model->setProductId($this->getRequest()->getParam('product_id'));
                $model->setSku($this->getRequest()->getParam('sku'));
                $model->setDescription($this->getRequest()->getParam('description'));
                $model->setCustomerGroupId($this->getRequest()->getParam('customer_group_id'));

                $model->save();

                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('epicor_comm')->__('Error saving SKU'));
                }
            } catch (Exception $e) {
                $response['type'] = 'error-msg';
                $response['message'] = $e->getMessage();
            }
        } else {
            $response['type'] = 'error-msg';
            $response['message'] = Mage::helper('epicor_comm')->__('No data found to save');
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    public function customerskudeleteAction()
    {

        $response = array();
        $response['type'] = 'success-msg';
        $response['message'] = Mage::helper('epicor_comm')->__('SKU was successfully deleted.');

        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('entity_id');
            $model = Mage::getModel('epicor_comm/customer_sku');
            /* @var $model Epicor_Comm_Model_Customer_Sku */

            try {

                $model->load($id);

                if (!$id || !$model->getId()) {
                    Mage::throwException(Mage::helper('epicor_comm')->__('No data found to delete'));
                }

                $model->delete();
            } catch (Exception $e) {
                $response['type'] = 'error-msg';
                $response['message'] = $e->getMessage();
            }
        } else {
            $response['type'] = 'error-msg';
            $response['message'] = Mage::helper('epicor_comm')->__('No data found to delete');
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    public function listsAction()
    {
        $this->_initErpAccount();
        $this->loadLayout();
        $this->getLayout()->getBlock('erp_lists_grid')
                ->setSelected($this->getRequest()->getPost('in_list', null));
        $this->renderLayout();
    }

    public function listsgridAction()
    {
        $this->_initErpAccount();
        $customers = $this->getRequest()->getParam('in_list');
        $this->loadLayout();
        $this->getLayout()->getBlock('erp_lists_grid')->setSelected($customers);
        $this->renderLayout();
    }

    public function mastershopperAction()
    {
        $this->_initErpAccount();
        $this->loadLayout();
        $this->getLayout()->getBlock('erp_mastershopper_grid')
                ->setSelected($this->getRequest()->getPost('ecc_master_shopper', null));
        $this->renderLayout();
    }

    public function mastershoppergridAction()
    {
        $this->_initErpAccount();
        $customers = $this->getRequest()->getParam('ecc_master_shopper');
        $this->loadLayout();
        $this->getLayout()->getBlock('erp_mastershopper_grid')->setSelected($customers);
        $this->renderLayout();
    }

    protected function processContracts($model, $data)
    {
        $contractArray = array('allowed_contract_type', 'required_contract_type', 'allow_non_contract_items', 'contract_shipto_default'
            , 'contract_shipto_date', 'contract_shipto_prompt', 'contract_header_selection', 'contract_header_prompt', 'contract_header_always'
            , 'contract_line_selection', 'contract_line_prompt', 'contract_line_always');

        foreach ($contractArray as $contract) {

            if (isset($data[$contract])) {
                $data[$contract] = $data[$contract] == '' ? null : $data[$contract];
                $model->setData($contract, $data[$contract]);
            }
        }
    }
   
}
