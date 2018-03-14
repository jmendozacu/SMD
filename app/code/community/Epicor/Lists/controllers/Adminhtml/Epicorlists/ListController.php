<?php

/**
 * List admin actions
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Teamf
 */
class Epicor_Lists_Adminhtml_Epicorlists_ListController extends Epicor_Comm_Controller_Adminhtml_Abstract
{
    protected $_currentCustomersInErpAccounts = array();
    protected $_erpaccounts = array();
    /**
     * Admin ACL method
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
                        ->isAllowed('epicor_lists/list');
    }

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

    /**
     * Export list grid to CSV format
     *
     * @return void
     */
    public function exportCsvAction()
    {
        $fileName = 'list.csv';
        $content = $this->getLayout()->createBlock('epicor_lists/adminhtml_list_list_grid')
                ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Displays form to upload CSV file
     *
     * @return void
     */
    public function addbycsvAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Uploads CSV file
     *
     * @return void
     */
    public function csvuploadAction()
    {
        if ($this->getRequest()->isPost()) {
            $listsHelper = Mage::helper('epicor_lists');
            /* @var $listsHelper Epicor_Lists_Helper_Data */

            $result = $listsHelper->importListFromCsv($_FILES['csv_file']['tmp_name']);

            if (isset($result['errors']['errors']) && is_array($result['errors']['errors'])) {
                foreach ($result['errors']['errors'] as $error) {
                    Mage::getSingleton('adminhtml/session')->addError($error);
                }
            }

            if (isset($result['errors']['warnings']) && is_array($result['errors']['warnings'])) {
                foreach ($result['errors']['warnings'] as $error) {
                    Mage::getSingleton('adminhtml/session')->addWarning($error);
                }
            }

            if (isset($result['list']) && $result['list']->getId()) {
                $this->_redirect('*/*/edit', array('id' => $result['list']->getId()));
            } else {
                $this->_redirect('*/*/addbycsv');
            }
        } else {
            $this->_redirect('*/*/addbycsv');
        }
    }

    /**
     * Export list grid to XML format
     * 
     * @return void
     */
    public function exportXmlAction()
    {
        $fileName = 'list.xml';
        $content = $this->getLayout()->createBlock('epicor_lists/adminhtml_list_list_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * new List action
     *
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * List edit action
     *
     * @return void
     */
    public function editAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * List create action
     *
     * @return void
     */
    public function createAction()
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        if ($data = $this->getRequest()->getPost()) {
            $list = $this->loadEntity();
            /* @var $list Epicor_Lists_Model_List */

            $this->processDetailsSave($list, $data);

            $valid = $list->validate();
            $session = Mage::getSingleton('adminhtml/session');

            if ($valid === true) {
                $importProductErrors = $this->importProducts($list);
                $importAddressesErrors = $this->importAddresses($list);
                $list->save();
                $this->processContractFieldSave($list, $data);
                $session->addSuccess($this->__('List Saved Successfully'));
                if ($importProductErrors || $this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $list->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            } else {
                $session->addError($this->__('The Following Error(s) occurred on Save:'));
                foreach ($valid as $error) {
                    $session->addError($error);
                }
                $session->setFormData($data);
                $this->_redirect('*/*/new');
            }
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * saves contract specific fields
     *
     * @param Epicor_Lists_Model_List $list
     * @param array $data
     *
     */
    protected function processContractFieldSave($list, $data)
    {
        if ($list->getTypeInstance()->isContract()) {
            $model = Mage::getModel('epicor_lists/contract');
            $model->load($list->getId(), 'list_id');
            $model->setListId($list->getId());
//            $model->setSalesRep($data['sales_rep']);
//            $model->setContactName($data['contact_name']);
            $poNumber = isset($data['po_number']) ? $data['po_number'] : null;
            if($poNumber){                
                $model->setPurchaseOrderNumber($poNumber);
            }
            //$model->setContractStatus(isset($data['active']) ? 'A' : 'I');
            $model->save();
        }
    }

    /**
     * Saves details for the list
     *
     * @param Epicor_Lists_Model_List $list
     * @param array $data
     *
     */
    protected function processDetailsSave($list, $data)
    {
        $list->setTitle($data['title']);
        $list->setNotes($data['notes']);
        $list->setDescription($data['description']);
        $list->setPriority(isset($data['priority']) ? $data['priority'] : 0);
        $list->setActive(isset($data['active']) ? 1 : 0);
        
            if ($list->isObjectNew()) {
                $list->setErpCode($data['erp_code']);
                $list->setType($data['type']);
                $label = empty($data['label']) ? $data['title'] : $data['label'];
                $list->setLabel($label);
            } else {
                $this->processErpOverrideSave($list, $data);
            }
            if ($list->getType() != "Co") {
                if (empty($data['start_date']) == false) {
                    if (!array_key_exists('select_start_time', $data)) {
                        $data['start_time'] = array('00', '00', '00');
                    }
                    $time = implode(':', $data['start_time']);
                    $dateTime = $data['start_date'] . ' ' . $time;

                    $dateObj = Mage::getSingleton('core/date');
                    /* @var $dateObj Mage_Core_Model_Date */

                    $list->setStartDate($dateObj->gmtDate('Y-m-d H:i:s', $dateTime));
                } else {
                    $list->setStartDate(false);
                }

                if (empty($data['end_date']) == false) {
                    if (!array_key_exists('select_end_time', $data)) {
                        $data['end_time'] = array('23', '59', '59');
                    }
                    $time = implode(':', $data['end_time']);
                    $dateTime = $data['end_date'] . ' ' . $time;
                    $dateObj = Mage::getSingleton('core/date');
                    /* @var $dateObj Mage_Core_Model_Date */

                    $list->setEndDate($dateObj->gmtDate('Y-m-d H:i:s', $dateTime));
                } else {
                    $list->setEndDate(false);
                }
            }

        $this->processSettingsSave($list, $data);
    }

    /**
     * Checks if Settings Information needs to be saved
     *
     * @param Epicor_Lists_Model_List $list
     * @param array $data
     *
     */
    protected function processSettingsSave($list, $data)
    {
        $settings = isset($data['settings']) ? $data['settings'] : array();
        $productVals = isset($data['links']['products'])? $data['links']['products'] : null;
        $excludeExist =  (isset($productVals)) ? 1 : 0 ;
        //Product tab values are present
        if($excludeExist) {
            $excludeSelectedProducts = isset($data['exclude_selected_products']) ? $data['exclude_selected_products'] : false;
            if($excludeSelectedProducts){            
                $settings[] = 'E';           
            }        
        } else {
            //Product tab is not loaded
            $exclusion = in_array('E', $list->getSettings()) ? true : false;
            if($exclusion) {
                $settings[] = 'E';  
            }            
        }
        $list->setSettings($settings);
    }

    /**
     * Checks if ERP Override Information needs to be saved
     *
     * @param Epicor_Lists_Model_List $list
     * @param array $data
     *
     */
    protected function processErpOverrideSave($list, $data)
    {
        $overrides = isset($data['erp_override']) ? $data['erp_override'] : array();
        $list->setErpOverride($overrides);
    }

    /**
     * List save action
     *
     * @return void
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $list = $this->loadEntity();
            /* @var $list Epicor_Lists_Model_List */

            $this->processDetailsSave($list, $data);
            $this->processContractFieldSave($list, $data);

            $typeInstance = $list->getTypeInstance();
            if ($typeInstance->isSectionEditable('labels')) {
                $this->processLabelsSave($list, $data);
            }
            if ($typeInstance->isSectionEditable('erpaccounts')) {
                $this->processERPAccountsSave($list, $data);
            }
            if ($typeInstance->isSectionEditable('websites')) {
                $this->processWebsitesSave($list, $data);
            }
            if ($typeInstance->isSectionEditable('stores')) {
                $this->processStoresSave($list, $data);
            }
            if ($typeInstance->isSectionEditable('customers')) {
                $this->processCustomersSave($list, $data);
            }
            if ($typeInstance->isSectionEditable('products')) {
                $this->processProductsSave($list, $data);
            }
            if ($typeInstance->isSectionEditable('pricing')) {
                $this->processProductsPricingSave($list, $data);
            }
            if ($typeInstance->isSectionEditable('addresses')) {
                $this->processAddressesSave($list, $data);
            }
            $this->processConditionsSave($list, $data);

            $list->orphanCheck();
            $valid = $list->validate();
            $session = Mage::getSingleton('adminhtml/session');

            if ($valid === true) {
                $importProductErrors = false; //$this->importProducts($list);
                $list->save();

                $session = Mage::getSingleton('adminhtml/session');
                $session->addSuccess($this->__('List Saved Successfully'));

                if ($importProductErrors || $this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $list->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            } else {
                $session->addError($this->__('The Following Error(s) occurred on Save:'));
                foreach ($valid as $error) {
                    $session->addError($error);
                }
                $session->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $list->getId()));
            }
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Labels tab loader
     *
     * @return void
     */
    public function labelsAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Checks if Labels Information needs to be saved
     *
     * @param Epicor_Lists_Model_List $list
     *
     * @param array $data
     */
    protected function processLabelsSave($list, $data)
    {
        if (isset($data['label'])) {
            $list->setLabel($data['label']);
        }

        if (isset($data['labels'])) {
            $labels = $data['labels'];
            $this->processWebsiteLabelsSave($list, $labels);
        }
    }

    /**
     * Saves Website Specific label information
     *
     * @param Epicor_Lists_Model_List $list
     * @param array $labels
     */
    protected function processWebsiteLabelsSave($list, $labels)
    {
        foreach ($labels as $websiteId => $webLabel) {
            if (empty($webLabel['default'])) {
                $list->removeWebsiteLabel($websiteId);
            } else {
                $list->addWebsiteLabel($websiteId, $webLabel['default']);
            }

            if (empty($webLabel['groups'])) {
                continue;
            }

            $this->processStoreGroupLabelsSave($list, $webLabel['groups']);
        }
    }

    /**
     * Processes store Group Label information
     *
     * @param Epicor_Lists_Model_List $list
     * @param array $labels
     */
    protected function processStoreGroupLabelsSave($list, $labels)
    {
        foreach ($labels as $groupId => $groupLabel) {
            if (empty($groupLabel['default'])) {
                $list->removeStoreGroupLabel($groupId);
            } else {
                $list->addStoreGroupLabel($groupId, $groupLabel['default']);
            }

            if (empty($groupLabel['stores'])) {
                continue;
            }

            $this->processStoreLabelsSave($list, $groupLabel['stores']);
        }
    }

    /**
     * Processes Store label information
     *
     * @param Epicor_Lists_Model_List $list
     * @param array $labels
     */
    protected function processStoreLabelsSave($list, $labels)
    {
        foreach ($labels as $storeId => $label) {
            if (empty($label)) {
                $list->removeStoreLabel($storeId);
            } else {
                $list->addStoreLabel($storeId, $label);
            }
        }
    }

    /**
     * ERP Accounts initial grid tab load
     *
     * @return void
     */
    public function erpaccountsAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $this->getLayout()->getBlock('erpaccounts_grid')
                ->setSelected($this->getRequest()->getPost('erpaccounts', null));
        $this->renderLayout();
    }

    /**
     * ERP Accounts ajax reload of grid tab
     *
     * @return void
     */
    public function erpaccountsgridAction()
    {
        $this->loadEntity();
        $erpaccounts = $this->getRequest()->getParam('erpaccounts');
        $this->loadLayout();
        $this->getLayout()->getBlock('erpaccounts_grid')->setSelected($erpaccounts);
        $this->renderLayout();
    }

    public function erpAccountSessionSetAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data['linkTypeValue']) {
            $selectedErpAccount = $data['linkTypeValue'];
            Mage::getSingleton('admin/session')->setlinkTypeValue($selectedErpAccount);
        }
    }

    public function restrictionsessionsetAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data['linkTypeValue']) {
            $selectedRestricionType = $data['linkTypeValue'];
            Mage::getSingleton('admin/session')->setRestrictionTypeValue($selectedRestricionType);
        }
    }

    /**
     * Checks if ERP Accounts Information needs to be saved
     *
     * @param Epicor_Lists_Model_List $list
     *
     * @param array $data
     */
    protected function processERPAccountsSave($list, $data)
    {
        $erpaccounts = $this->getRequest()->getParam('selected_erpaccounts');
        if (!is_null($erpaccounts)) {
            $this->saveERPAccounts($list, $data);
            // if erp_account_link_type = 'N', save erp account exclusion indicator as 'N', else save value
            $linkType = $data['erp_account_link_type'];
            $dataExclusion = isset($data['erp_accounts_exclusion']) ? 'Y' : 'N';
            $exclusion = $linkType == 'N' ? 'N' : $dataExclusion;
            $list->setErpAccountLinkType($linkType);
            $list->setErpAccountsExclusion($exclusion);
        }
    }

    /**
     * Save ERP Accounts Information
     *
     * @param Epicor_Lists_Model_List $list
     * @param array $data
     *
     * @return void
     */
    protected function saveERPAccounts(&$list, $data)
    {
        $erpaccounts = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['erpaccounts']));
        $list->removeErpAccounts($list->getErpAccounts());
        $list->addErpAccounts($erpaccounts);
    }

    /**
     * Brands initial grid tab load
     *
     * @return void
     */
    public function brandsAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $this->getLayout()->getBlock('brands_grid')
                ->setSelected($this->getRequest()->getPost('brands', null));
        $this->renderLayout();
    }

    /**
     * Brands ajax reload of grid tab
     *
     * @return void
     */
    public function brandsgridAction()
    {
        $this->loadEntity();
        $brands = $this->getRequest()->getParam('brands');
        $this->loadLayout();
        $this->getLayout()->getBlock('brands_grid')->setSelected($brands);
        $this->renderLayout();
    }

    /**
     * Brands ajax post
     *
     * @return void
     */
    public function brandpostAction()
    {
        $response = array();
        $response['type'] = 'success-msg';
        $response['message'] = Mage::helper('epicor_lists')->__('Brand was successfully saved.');

        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('brand_id');
            $model = Mage::getModel('epicor_lists/list_brand');
            /* @var $model Epicor_Lists_Model_List_Brand */

            try {
                if ($id) {
                    $model->load($id);
                }

                $model->setListId($this->getRequest()->getParam('list_id'));
                $model->setCompany($this->getRequest()->getParam('company'));
                $model->setSite($this->getRequest()->getParam('site'));
                $model->setWarehouse($this->getRequest()->getParam('warehouse'));
                $model->setGroup($this->getRequest()->getParam('group'));

                $model->save();

                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('epicor_lists')->__('Error saving Brand'));
                }
            } catch (Exception $e) {
                $response['type'] = 'error-msg';
                $response['message'] = $e->getMessage();
            }
        } else {
            $response['type'] = 'error-msg';
            $response['message'] = Mage::helper('epicor_lists')->__('No data found to save');
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Brands ajax delete
     *
     * @return void
     */
    public function branddeleteAction()
    {
        $response = array();
        $response['type'] = 'success-msg';
        $response['message'] = Mage::helper('epicor_lists')->__('Brand was successfully deleted.');

        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('epicor_lists/list_brand');
            /* @var $model Epicor_Lists_Model_List_Brand */

            try {
                $model->load($id);

                if (!$id || !$model->getId()) {
                    Mage::throwException(Mage::helper('epicor_lists')->__('No data found to delete'));
                }

                $model->delete();
            } catch (Exception $e) {
                $response['type'] = 'error-msg';
                $response['message'] = $e->getMessage();
            }
        } else {
            $response['type'] = 'error-msg';
            $response['message'] = Mage::helper('epicor_lists')->__('No data found to delete');
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Websites initial grid tab load
     *
     * @return void
     */
    public function websitesAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $this->getLayout()->getBlock('websites_grid')
                ->setSelected($this->getRequest()->getPost('websites', null));
        $this->renderLayout();
    }

    /**
     * Websites ajax reload of grid tab
     *
     * @return void
     */
    public function websitesgridAction()
    {
        $this->loadEntity();
        $websites = $this->getRequest()->getParam('websites');
        $this->loadLayout();
        $this->getLayout()->getBlock('websites_grid')->setSelected($websites);
        $this->renderLayout();
    }

    /**
     * Checks if Websites Information needs to be saved
     *
     * @param Epicor_Lists_Model_List $list
     *
     * @param array $data
     */
    protected function processWebsitesSave($list, $data)
    {
        $websites = $this->getRequest()->getParam('selected_websites');
        if (!is_null($websites)) {
            $this->saveWebsites($list, $data);
        }
    }

    /**
     * Save Websites Information
     *
     * @param Epicor_Lists_Model_List $list
     * @param array $data
     *
     * @return void
     */
    protected function saveWebsites(&$list, $data)
    {
        $websites = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['websites']));
        $list->removeWebsites($list->getWebsites());
        $list->addWebsites($websites);
    }

    /**
     * Stores initial grid tab load
     *
     * @return void
     */
    public function storesAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $this->getLayout()->getBlock('stores_grid')
                ->setSelected($this->getRequest()->getPost('stores', null));
        $this->renderLayout();
    }

    /**
     * Stores ajax reload of grid tab
     *
     * @return void
     */
    public function storesgridAction()
    {
        $this->loadEntity();
        $stores = $this->getRequest()->getParam('stores');
        $this->loadLayout();
        $this->getLayout()->getBlock('stores_grid')->setSelected($stores);
        $this->renderLayout();
    }

    /**
     * Checks if Stores Information needs to be saved
     *
     * @param Epicor_Lists_Model_List $list
     *
     * @param array $data
     */
    protected function processStoresSave($list, $data)
    {
        $stores = $this->getRequest()->getParam('selected_stores');
        if (!is_null($stores)) {
            $this->saveStores($list, $data);
        }
    }

    /**
     * Save Stores Information
     *
     * @param Epicor_Lists_Model_List $list
     * @param array $data
     *
     * @return void
     */
    protected function saveStores(&$list, $data)
    {
        $stores = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['stores']));
        $list->removeStoreGroups($list->getStoreGroups());
        $list->addStoreGroups($stores);
    }

    /**
     * Customers initial grid tab load
     *
     * @return void
     */
    public function customersAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $this->getLayout()->getBlock('customers_grid')
                ->setSelected($this->getRequest()->getPost('customers', null));
        $this->renderLayout();
    }

    /**
     * Customers ajax reload of grid tab
     *
     * @return void
     */
    public function customersgridAction()
    {
        $this->loadEntity();
        $customers = $this->getRequest()->getParam('customers');
        $this->loadLayout();
        $this->getLayout()->getBlock('customers_grid')->setSelected($customers);
        $this->renderLayout();
    }

    /**
     * Checks if Customers Information needs to be saved
     *
     * @param Epicor_Lists_Model_List $list
     *
     * @param array $data
     */
    protected function processCustomersSave($list, $data)
    {
        $customers = $this->getRequest()->getParam('selected_customers');
        if (!is_null($customers)) {
            $this->saveCustomers($list, $data);
        }
    }

    /**
     * Save Customers Information
     *
     * @param Epicor_Lists_Model_List $list
     * @param array $data
     *
     * @return void
     */
    protected function saveCustomers(&$list, $data)
    {
        
        $customers = isset($data['links']['customers']) ? array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['customers'])) : array();
        $list->removeCustomers($list->getCustomers());
        $list->addCustomers($customers);
    }

    /**
     * Products initial grid tab load
     *
     * @return void
     */
    public function productsAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $this->getLayout()->getBlock('products_grid')
                ->setSelected($this->getRequest()->getPost('products', null));
        $this->renderLayout();
    }

    /**
     * Products ajax reload of grid tab
     *
     * @return void
     */
    public function productsgridAction()
    {
        $this->loadEntity();
        $products = $this->getRequest()->getParam('products');
        $this->loadLayout();
        $this->getLayout()->getBlock('products_grid')->setSelected($products);
        $this->renderLayout();
    }

    /**
     * Checks if Products Information needs to be saved
     *
     * @param Epicor_Lists_Model_List $list
     *
     * @param array $data
     */
    protected function processProductsSave($list, $data)
    {
        $products = $this->getRequest()->getParam('selected_products');
        if (!is_null($products)) {
            $this->saveProducts($list, $data);
        }
    }

    /**
     * Save Products Information
     *
     * @param Epicor_Lists_Model_List $list
     * @param array $data
     *
     * @return void
     */
    protected function saveProducts(&$list, $data)
    {
        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */

        $products = array_keys($helper->decodeGridSerializedInput($data['links']['products']));

        $list->removeProducts($list->getProducts());
        $list->addProducts($products);
    }

    /**
     * Checks if ProductsPricing Information needs to be saved
     *
     * @param Epicor_Lists_Model_List $list
     * @param array $data
     * @param array $products
     */
    protected function processProductsPricingSave($list, $data)
    {
        if (isset($data['json_pricing'])) {
            $pricing = json_decode($data['json_pricing'], true);
            $list->addPricing($pricing);
        }
    }

    protected function importProducts($list)
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        $failed = false;

        if (!empty($_FILES['import']['tmp_name'])) {
            $errors = $helper->importCsvProducts($list, $_FILES['import']['tmp_name']);
            if (isset($errors['errors'])) {
                foreach ($errors['errors'] as $error) {
                    $failed = true;
                    Mage::getSingleton('adminhtml/session')->addError($error);
                }
            }

            if (isset($errors['warnings'])) {
                foreach ($errors['warnings'] as $error) {
                    Mage::getSingleton('adminhtml/session')->addWarning($error);
                }
            }
        }

        return $failed;
    }

    protected function importAddresses($list)
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        $failed = false;

        if (!empty($_FILES['import']['tmp_name'])) {
            $errors = $helper->importCsvAddresses($list, $_FILES['import']['tmp_name']);
            if (isset($errors['errors'])) {
                foreach ($errors['errors'] as $error) {
                    $failed = true;
                    Mage::getSingleton('adminhtml/session')->addError($error);
                }
            }

            if (isset($errors['warnings'])) {
                foreach ($errors['warnings'] as $error) {
                    Mage::getSingleton('adminhtml/session')->addWarning($error);
                }
            }
        }

        return $failed;
    }

    /**
     * Addresses initial grid tab load
     *
     * @return void
     */
    public function addressesAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $this->getLayout()->getBlock('addresses_grid')
                ->setSelected($this->getRequest()->getPost('addresses', null));
        $this->renderLayout();
    }

    /**
     * Addresses ajax reload of grid tab
     *
     * @return void
     */
    public function addressesgridAction()
    {
        $this->loadEntity();
        $addresses = $this->getRequest()->getParam('addresses');
        $this->loadLayout();
        $this->getLayout()->getBlock('addresses_grid')->setSelected($addresses);
        $this->renderLayout();
    }

    /**
     * Checks if Addresses Information needs to be saved
     *
     * @param Epicor_Lists_Model_List $list
     *
     * @param array $data
     */
    protected function processAddressesSave($list, $data)
    {
        $addresses = $this->getRequest()->getParam('selected_addresses');
        if (!is_null($addresses)) {
            $this->saveAddresses($list, $data);
        }
    }

    /**
     * Save Addresses Information
     *
     * @param Epicor_Lists_Model_List $list
     * @param array $data
     *
     * @return void
     */
    protected function saveAddresses(&$list, $data)
    {
        $addresses = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['addresses']));

        // TODO: load current Addresses and check if any need to be removed
        // TODO: loop through passed values and only add new Addresses
    }

    /**
     * Address ajax post
     *
     * @return void
     */
    public function addresspostAction()
    {
        $response = array();
        $response['type'] = 'success-msg';
        $response['message'] = Mage::helper('epicor_lists')->__('Address was successfully saved.');
        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('address_id');
            $model = Mage::getModel('epicor_lists/list_address');
            /* @var $model Epicor_Lists_Model_List_Address */

            try {
                if ($id) {
                    $model->load($id);
                }

                $model->setListId($this->getRequest()->getParam('list_id'));
                $model->setAddressCode($this->getRequest()->getParam('address_code'));
                $model->setName($this->getRequest()->getParam('address_name'));
                $model->setAddress1($this->getRequest()->getParam('address1'));
                $model->setAddress2($this->getRequest()->getParam('address2'));
                $model->setAddress3($this->getRequest()->getParam('address3'));
                $model->setCity($this->getRequest()->getParam('city'));
                $model->setCounty($this->getRequest()->getParam('county'));
                $model->setCountry($this->getRequest()->getParam('country'));
                $model->setPostcode($this->getRequest()->getParam('postcode'));
                $model->setTelephoneNumber($this->getRequest()->getParam('telephone_number'));
                $model->setMobileNumber($this->getRequest()->getParam('mobile_number'));
                $model->setFaxNumber($this->getRequest()->getParam('fax_number'));
                $model->setEmailAddress($this->getRequest()->getParam('email_address'));

                $model->save();

                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('epicor_lists')->__('Error saving Address'));
                }
            } catch (Exception $e) {
                $response['type'] = 'error-msg';
                $response['message'] = $e->getMessage();
            }
        } else {
            $response['type'] = 'error-msg';
            $response['message'] = Mage::helper('epicor_lists')->__('No data found to save');
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Address ajax delete
     *
     * @return void
     */
    public function addressdeleteAction()
    {
        $response = array();
        $response['type'] = 'success-msg';
        $response['message'] = Mage::helper('epicor_lists')->__('Address was successfully deleted.');

        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('epicor_lists/list_address');
            /* @var $model Epicor_Lists_Model_List_Address */

            try {
                $model->load($id);

                if (!$id || !$model->getId()) {
                    Mage::throwException(Mage::helper('epicor_lists')->__('No data found to delete'));
                }

                $model->delete();
            } catch (Exception $e) {
                $response['type'] = 'error-msg';
                $response['message'] = $e->getMessage();
            }
        } else {
            $response['type'] = 'error-msg';
            $response['message'] = Mage::helper('epicor_lists')->__('No data found to delete');
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Message Log initial grid tab load
     *
     * @return void
     */
    public function messagelogAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $this->getLayout()->getBlock('messagelog_grid')
                ->setSelected($this->getRequest()->getPost('messagelog', null));
        $this->renderLayout();
    }

    /**
     * Message Log ajax reload of grid tab
     *
     * @return void
     */
    public function messageloggridAction()
    {
        $this->loadEntity();
        $messagelog = $this->getRequest()->getParam('messagelog');
        $this->loadLayout();
        $this->getLayout()->getBlock('messagelog_grid')->setSelected($messagelog);
        $this->renderLayout();
    }

    /**
     * Deletes single List
     *
     * @return void
     */
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $this->delete($id);
        $this->_redirect('*/*/');
    }

    /**
     * Deletes array of given List
     *
     * @return void
     */
    public function massDeleteAction()
    {
        $ids = (array) $this->getRequest()->getParam('listid');
        foreach ($ids as $id) {
            $this->delete($id, true);
        }
        $helper = Mage::helper('epicor_lists');
        $session = Mage::getSingleton('adminhtml/session');
        $session->addSuccess($helper->__(count($ids) . ' Lists deleted'));
        $this->_redirect('*/*/');
    }

    /**
     * Deletes the given List by id
     *
     * @param integer $id
     * @param boolean $mass
     *
     * @return void
     */
    protected function delete($id, $mass = false)
    {
        $model = Mage::getModel('epicor_lists/list');
        /* @var $list Epicor_Lists_Model_List */
        $session = Mage::getSingleton('adminhtml/session');
        $helper = Mage::helper('epicor_lists');
        if ($id) {
            $model->load($id);
            if ($model->getId()) {
                if ($model->delete()) {
                    if (!$mass) {
                        $session->addSuccess($helper->__('List deleted'));
                    }
                } else {
                    $session->addError($helper->__('Could not delete List ' . $id));
                }
            }
        }
    }

    /**
     * Assign ERP Account Lists
     *
     * @return void
     */
    public function massAssignErpAccountAction()
    {
        $ids = (array) $this->getRequest()->getParam('listid');
        $erpAccountId = $this->getRequest()->getParam('assign_erp_account');

        $helper = Mage::helper('epicor_lists');
        $session = Mage::getSingleton('adminhtml/session');

        $erpAccount = Mage::getModel('epicor_comm/customer_erpaccount')->load($erpAccountId);
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */

        if ($erpAccount->isObjectNew()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select an Erp Account.'));
        } else {
            $returnvalues = Mage::helper('epicor_lists/admin')->assignErpAccountListsCheck($ids, $erpAccount);
            if ($returnvalues) {
                if (!empty($returnvalues['success']['id'])) {
                    $erpAccount->addLists($returnvalues['success']['values']);
                    $erpAccount->save();
                    $session->addSuccess($helper->__('ERP Account assigned to ' . count(array_keys($returnvalues['success']['values'])) . ' Lists. ' . "List Id: (" . $returnvalues['success']['id'] . ")"));
                }
                if (!empty($returnvalues['error']['id'])) {
                    $session->addError($helper->__('ERP Account not assigned to ' . count(array_keys($returnvalues['error']['values'])) . ' Lists. ' . "List Id: (" . $returnvalues['error']['id'] . ")"));
                }
            }
        }

        $this->_redirect('*/*/');
    }

    /**
     * Assign Customer Lists
     *
     * @return void
     */
    public function massAssignCustomerAction()
    {
        $ids = (array) $this->getRequest()->getParam('listid');
        $customerId = $this->getRequest()->getParam('assign_customer');

        $helper = Mage::helper('epicor_lists');
        $session = Mage::getSingleton('adminhtml/session');

        $customer = Mage::getModel('customer/customer')->load($customerId);
        /* @var $customer Epicor_Comm_Model_Customer */

        if ($customer->isObjectNew()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select a Customer.'));
        } else {
            $returnvalues = Mage::helper('epicor_lists/admin')->assignCustomerAccountListsCheck($ids, $customer);
            if ($returnvalues) {
                if (!empty($returnvalues['success']['id'])) {
                    $customer->addLists($returnvalues['success']['values']);
                    $customer->save();
                    $session->addSuccess($helper->__('Customer Account assigned to ' . count(array_keys($returnvalues['success']['values'])) . ' Lists. ' . "List Id: (" . $returnvalues['success']['id'] . ")"));
                }
                if (!empty($returnvalues['error']['id'])) {
                    $session->addError($helper->__('Customer Account not assigned to ' . count(array_keys($returnvalues['error']['values'])) . ' Lists. ' . "List Id: (" . $returnvalues['error']['id'] . ")"));
                }
            }
        }

        $this->_redirect('*/*/');
    }

    /**
     * Remove ERP Account Lists
     *
     * @return void
     */
    public function massRemoveErpAccountAction()
    {
        $ids = (array) $this->getRequest()->getParam('listid');
        $erpAccountId = $this->getRequest()->getParam('remove_erp_account');

        $helper = Mage::helper('epicor_lists');
        $session = Mage::getSingleton('adminhtml/session');

        $erpAccount = Mage::getModel('epicor_comm/customer_erpaccount')->load($erpAccountId);
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */

        if ($erpAccount->isObjectNew()) {
            $session->addError(Mage::helper('adminhtml')->__('Please select an Erp Account.'));
        } else {
            $erpAccount->removeLists($ids);
            $erpAccount->save();
            $session->addSuccess($helper->__('ERP Account removed to ' . count($ids) . ' Lists '));
        }

        $this->_redirect('*/*/');
    }

    /**
     * Remove Customer Lists
     *
     * @return void
     */
    public function massRemoveCustomerAction()
    {
        $ids = (array) $this->getRequest()->getParam('listid');
        $customerId = $this->getRequest()->getParam('remove_customer');

        $helper = Mage::helper('epicor_lists');
        $session = Mage::getSingleton('adminhtml/session');

        $customer = Mage::getModel('customer/customer')->load($customerId);
        /* @var $customer Epicor_Comm_Model_Customer */

        if ($customer->isObjectNew()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select a Customer.'));
        } else {
            $customer->removeLists($ids);
            $customer->save();
            $session->addSuccess($helper->__('Customer removed to ' . count($ids) . ' Lists '));
        }

        $this->_redirect('*/*/');
    }

    /**
     * Assign Status Lists
     *
     * @return void
     */
    public function massAssignStatusAction()
    {
        $ids = (array) $this->getRequest()->getParam('listid');
        $assign_status = $this->getRequest()->getParam('assign_status');
        $assignContractIds = array();
        foreach ($ids as $id) {
            $list = Mage::getModel('epicor_lists/list')->load($id);
            //If the list type is contract
            //Contract status is Not Active
            //But Assign status is 1
            //then don't assign the status
            if ($list->getType() == "Co") {
                $model = Mage::getModel('epicor_lists/contract');
                $model->load($list->getId(), 'list_id');
                $getContractStatus = $model->getContractStatus();
            }
            if (($list->getType() == "Co") && ($getContractStatus == "I") && ($assign_status)) {
                $excludeIds[] = $id;
            } else {
                if (($list->getType() == "Co") && (!$assign_status)) {
                    $assignContractIds[] = $id;
                }
                $includeIds[] = $id;
                $list->setActive($assign_status);
                $list->save();
            }
        }

        $errorIds = rtrim(implode(',', $excludeIds), ',');
        $disableIds = rtrim(implode(',', $assignContractIds), ',');
        $changedIds = rtrim(implode(',', $includeIds), ',');

        //if (!empty($disableIds)) {
        //    Mage::helper('epicor_lists/admin')->massUpdateListContracts($disableIds);
        //}

        $helper = Mage::helper('epicor_lists');
        $session = Mage::getSingleton('adminhtml/session');
        if (!empty($errorIds)) {
            $session->addError($helper->__('List Status not changed to ' . count(array_keys($excludeIds)) . ' Lists. ' . "List Id: (" . $errorIds . ")"));
        }
        if (!empty($changedIds)) {
            $session->addSuccess($helper->__('List Status  changed to ' . count(array_keys($includeIds)) . ' Lists. ' . "List Id: (" . $changedIds . ")"));
        }

        $this->_redirect('*/*/');
    }

    /**
     * Loads List
     *
     * @return Epicor_Lists_Model_List
     */
    protected function loadEntity()
    {
        $id = $this->getRequest()->getParam('id', null);
        $list = Mage::getModel('epicor_lists/list')->load($id);
        /* @var $list Epicor_Lists_Model_List */
        Mage::register('list', $list);

        return $list;
    }

    public function productpricingAction()
    {
        $listId = $this->getRequest()->getParam('list');
        $productId = $this->getRequest()->getParam('product');
        $listProduct = Mage::getModel('epicor_lists/list_product')->getCollection();
        /* @var $listProduct Epicor_Lists_Model_Resource_List_Product_Collection */
        $listProduct->addFieldToFilter('list_id', $listId);
        $listProduct->addFieldToFilter('sku', $productId);

        $product = $listProduct->getFirstItem();
        /* @var $item Epicor_Lists_Model_List_Product */

        $pricing = array();
        foreach ($product->getPricing() as $item) {
            /* @var $item Epicor_Lists_Model_List_Product_Price */
            $pricing[$item->getId()] = array(
                'id' => $item->getId(),
                'currency' => $item->getCurrency(),
                'price' => $item->getPrice(),
                'price_breaks' => $item->getPriceBreaks(),
                'value_breaks' => $item->getValueBreaks()
            );
        }


        $this->getResponse()->setBody(json_encode($pricing, JSON_FORCE_OBJECT));
    }

    /**
     * Checks if there are conditions to save
     *      *
     * @param Epicor_Lists_Model_List $list
     *
     * @param array $data
     */
    protected function processConditionsSave($list, $data)
    {
        $rule = Mage::getModel('epicor_lists/list_rule');
        $excludeExist =  (isset($data['links']['products'])) ? 1 : 0 ;
        //Product tab values are present
        if($excludeExist) {    
           $condition = $this->getRequest()->getParam('rule_is_enabled');
           //If Link products to list conditionally was ticked
           if (isset($condition)) {
               $data = $this->getRequest()->getParam('rule');
               $rule->loadPost($data);
               $list->setConditions(serialize($rule->getConditions()->asArray()));
           }  else {
               //If Link products to list conditionally was not ticked
               $list->setConditions(null);
           }
        }
    }

    /**
     * Ajax Import Products Action
     *
     * @return void
     */
    public function productsimportpostAction()
    {


        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        $list = $this->loadEntity();
        /* @var $list Epicor_Lists_Model_List */

        $errors = array();
        if (!$list->isObjectNew() && !empty($_FILES['import']['tmp_name'])) {
            $errors = $helper->importCsvProducts($list, $_FILES['import']['tmp_name']);
            $list->save();
        }

        $productIds = array_keys($list->getProducts());

        $this->getResponse()->setBody(json_encode(array(
            'products' => $productIds,
            'errors' => $errors
        )));
    }

    /**
     * Generates a CSV that can be used for upload
     *
     * @return void
     */
    public function productimportcsvAction()
    {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename= example_list_product_import.csv");
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
        header("Pragma: no-cache"); // HTTP 1.0
        header("Expires: 0"); // Proxies

        $csvString = '"SKU","UOM"' . "\n" . '"ExampleProduct1","EA"';

        $this->getResponse()->setBody($csvString);
    }

    /**
     * Generates a CSV that can be used for create a new list
     *
     * @return void
     */
    public function createnewlistcsvAction()
    {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename= example_create_new_list.csv");
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
        header("Pragma: no-cache"); // HTTP 1.0
        header("Expires: 0"); // Proxies

        $csvString = '"Header",""' . "\n" .
                '"List Code","LISTCODE"' . "\n" .
                '"Title","New List Title"' . "\n" .
                '"Type","PG"' . "\n" .
                '"Active","Y"' . "\n" .
                '"Notes","List Notes"' . "\n" .
                '"Account Link Type","B"' . "\n" .
                '"Mandatory List","Y"' . "\n" .
                '"Default list","Y"' . "\n" .
                '"Auto load","Y"' . "\n" .                
                '"",' . "\n" .
                '"Accounts",exclude="Y|N"' . "\n" .
                '"ERPCode1",' . "\n" .
                '"ERPCode2",' . "\n" .
                '"",""' . "\n" .
                '"Products",exclude="Y|N"' . "\n" .
                '"SKU","UOM"' . "\n" .
                '"ExampleProduct1","EA"' . "\n" .
                '"ExampleProduct2",""';

        $this->getResponse()->setBody($csvString);
    }

    /**
     * Get customer contract addresses
     *
     * @return string
     */
    public function fetchaddressAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $addressId = $data['id'];
            $customerId = $data['customer_id'];
            $result = Mage::helper('epicor_lists')->customerSelectedAddressById($addressId, $customerId);
        }
        echo $result;
    }
    
    /**
     * Retrieve the erp account and customer details before save
     *
     * @return string
     */
    public function orphanCheckAction()
    {
        $data = Mage::app()->getRequest()->getParams();
        $list = $this->loadEntity();
        
        $response = array(
            'message' => '', 
            'type' => 'no_change',
            'erpaccounts' => 0,
            'exlusionerror' => false,
        );
        
        if ($list->getId()) {
            $this->processERPAccountsSave($list, $data);
            $this->processCustomersSave($list, $data);
            $warning = $list->orphanCheck('warn');
            
            if ($warning) {
                $response = array_merge($response, $warning);
            } else {
                $response['erpaccounts'] = count($list->getErpAccountsWithChanges());
            }

            $inclusion = $list->getErpAccountsExclusion() == 'N';
            $validLinkType = $list->getErpAccountLinkType() != 'N';
            
            $response['exlusionerror'] = ($inclusion && $validLinkType && $response['erpaccounts'] == 0);
        }

        Mage::App()->getResponse()->setBody(json_encode($response));
    }

    public function restrictionsAction()
    {
        $list = $this->loadEntity();
        if ($list->getId()) {
            $restrictedPurchase = Mage::getModel('epicor_lists/list_address_restriction')
                    ->getCollection()->addFieldToFilter('list_id', $list->getId())
                    ->addFieldToSelect('restriction_type');
            foreach ($restrictedPurchase->getData() as $key => $value) {
                $restArray[$key] = $value['restriction_type'];
            }
            $listRestrictions = array_values(array_unique($restArray));
            Mage::getSingleton('admin/session')->setRestrictionTypeValue($listRestrictions[0]);
        }
        
        $rtValue = Mage::getSingleton('admin/session')->getRestrictionTypeValue();
        if (empty($rtValue)) {
            Mage::getSingleton('admin/session')->setRestrictionTypeValue(Epicor_Lists_Model_List_Address_Restriction::TYPE_ADDRESS);
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('restrictions_grid');
        $this->renderLayout();
    }

    public function addgridAction()
    {
        //$list = $this->loadEntity();
        $data = $this->getRequest()->getPost();
        $list = Mage::getModel('epicor_lists/list')->load($data['listId']);
        if ($list) {
            if ($data['linkTypeValue']) {
                $selectedRestricionType = $data['linkTypeValue'];
                Mage::getSingleton('admin/session')->setRestrictionTypeValue($selectedRestricionType);
            }
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('restrictions_grid');
        $this->renderLayout();
    }

    /**
     * Addresses ajax reload of grid tab
     *
     * @return void
     */
    public function restrictionsgridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('restrictions_grid');
        $this->renderLayout();
    }

    /**
     * Load address restriction form for add/update
     *
     * @return void
     */
    public function addupdateAction()
    {
        $id = $this->getRequest()->getPost('list_id', null);
        $address_id = $this->getRequest()->getPost('address_id', null);
        $list = Mage::getModel('epicor_lists/list')->load($id);
        /* @var $list Epicor_Lists_Model_List */
        Mage::register('list', $list);
        $address = Mage::getModel('epicor_lists/list_address')->load($address_id);
        Mage::register('address', $address);
        $this->getResponse()->setBody($this->getLayout()->createBlock('epicor_lists/adminhtml_list_edit_tab_restrictions_form')->toHtml());
    }

    public function restrictedaddresspostAction()
    {
        $response = array();
        $response['type'] = 'success-msg';
        $response['message'] = Mage::helper('epicor_lists')->__('Restriction successfully saved');
        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('address_id');
            $model = Mage::getModel('epicor_lists/list_address');
            /* @var $model Epicor_Lists_Model_List_Address */

            try {
                if ($id) {
                    $model->load($id);
                }
                
                if (isset($data['county_id']) && !empty($data['county_id'])) {
                    $region = Mage::getModel('directory/region')->load($data['county_id']);
                    /* @var $region Mage_Directory_Model_Region */
                    $data['county'] = $region->getCode();
                }

                $model->addData($data);
                
                if ($model->validateRestriction()) {
                    $model->save();
                } else {
                    Mage::throwException($this->__('Error: Restriction already exists'));
                }
            } catch (Exception $e) {
                $response['type'] = 'error-msg';
                $response['message'] = $e->getMessage();
            }
        } else {
            $response['type'] = 'error-msg';
            $response['message'] = Mage::helper('epicor_lists')->__('No data found to save');
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
    
    public function validateCodeAction()
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */
        $this->getResponse()->setBody(json_encode($helper->validateNewListCode($this->getRequest())));
    }
}
