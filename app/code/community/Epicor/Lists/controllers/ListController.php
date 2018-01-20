<?php

/**
 * List frontend actions
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_ListController extends Epicor_Customerconnect_Controller_Abstract
{

    /**
     * Delivery Address page
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * List ajax reload of grid tab
     *
     * @return void
     */
    public function listgridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('epicor_lists/customer_account_list_grid')->toHtml());
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
        if (isset($data['links']['customers'])) {
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
        $customers = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['customers']));
        $list->removeCustomers($list->getCustomers());
        $list->addCustomers($customers);
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

    public function saveAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($data = $this->getRequest()->getPost()) {
            $list = $this->loadEntity();
            $listData = $list->getData();
            if (empty($listData)) {
                $list = Mage::getModel('epicor_lists/list')->load($this->getRequest()->getPost('id'));
            }
            $list->setErpAccountsExclusion();
            $list->setErpAccountLinkType('E');
            $this->processDetailsSave($list, $data);
            $valid = $list->validate(true);
            $session = Mage::getSingleton('core/session');
            $isMasterShopper = $customer->getEccMasterShopper();

            if ($valid === true) {
                $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getId());
                $erpAccount = Mage::helper('epicor_comm')->getErpAccountInfo();
                $customerAccountType = $customer->getEccErpAccountType();
                if ($customerAccountType == "guest") {
                    $list->setErpAccountLinkType('N');
                } else {
                    $list->addErpAccounts(array(
                        $erpAccount->getid()
                    ));
                }

                $new = false;
                if ($list->isObjectNew()) {
                    $new = true;
                    $list->setOwnerId(Mage::getSingleton('customer/session')->getId());
                    $list->setSource('customer');
                }
                $list->save();
                $list_id = array(
                    $list->getId()
                );

                // When a master shopper creates or edits a list he should not be assigned to the list automatically. 
                //It is up to the master shopper to decide whether he wants to assign himself. 
                $checkMasters = $customer->getEccMasterShopper();
                if (!$checkMasters) {
                    $customer->addLists($list_id);
                }

                $customer->save();
                $this->processCustomersSave($list, $data);
                $this->processProductsSave($list, $data);
                $list->save();
                $session->addSuccess($this->__('List Saved Successfully'));
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array(
                        'id' => base64_encode($list->getId())
                    ));
                } else {
                    $this->_redirect('*/*/');
                }
            } else {
                $session->addError($this->__('The Following Error(s) occurred on Save:'));
                foreach ($valid as $error) {
                    $session->addError($error);
                }
                $session->setFormData($data);
                if($list->getId()){
                    $this->_redirect('*/*/edit', array(
                        'id' => base64_encode($list->getId())
                        ));  
                }else{
                    $this->_redirect('*/*/new');
                }
            }
        } else {
            $this->_redirect('*/*/');
        }
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

        if (isset($data['exclude_selected_products'])) {
            $settings[] = 'E';
        }

        $list->setSettings($settings);
    }

    protected function processErpOverrideSave($list, $data)
    {
        $overrides = isset($data['erp_override']) ? $data['erp_override'] : array();
        $list->setErpOverride($overrides);
    }

    public function productsAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $products = $this->getRequest()->getParam('list_id');
        $this->getLayout()->getBlock('list_products')->setSelected($products);
        $this->renderLayout();
    }

    public function productsgridAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $products = $this->getRequest()->getParam('list_id');
        $this->getLayout()->getBlock('list_products')->setSelected($products);
        $this->renderLayout();
    }

    public function customersAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $this->getLayout()->getBlock('list_customers')->setSelected($this->getRequest()->getPost('customers', null));
        $this->renderLayout();
    }

    public function customersgridAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $customers = $this->getRequest()->getParam('customers');
        $this->getLayout()->getBlock('list_customers')->setSelected($customers);
        $this->renderLayout();
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
        $list->setPriority($data['priority']);
        $list->setActive(isset($data['active']) ? 1 : 0);
        if ($list->isObjectNew()) {
            $list->setErpCode($data['erp_code']);
            $list->setType($data['type']);
        } else {
            $this->processErpOverrideSave($list, $data);
        }
        if (empty($data['start_date']) == false) {
            $time = implode(':', $data['start_time']);
            $dateTime = $data['start_date'] . ' ' . $time;

            $dateObj = Mage::getSingleton('core/date');
            /* @var $dateObj Mage_Core_Model_Date */

            $list->setStartDate($dateObj->gmtDate('Y-m-d H:i:s', $dateTime));
        } else {
            $list->setStartDate(false);
        }
        if (empty($data['end_date']) == false) {
            $time = implode(':', $data['end_time']);
            $dateTime = $data['end_date'] . ' ' . $time;
            $dateObj = Mage::getSingleton('core/date');
            /* @var $dateObj Mage_Core_Model_Date */
            $list->setEndDate($dateObj->gmtDate('Y-m-d H:i:s', $dateTime));
        } else {
            $list->setEndDate(false);
        }
        $this->processSettingsSave($list, $data);
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

        $list->setErpAccountLinkType('E');
        if ($list->getErpAccountLinkType() == 'E') {
            $list->addErpAccounts($erpaccounts);
        }
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $this->delete($id);
        $this->_redirect('*/*/');
    }

    protected function delete($id, $mass = false)
    {
        $model = Mage::getModel('epicor_lists/list');
        /* @var $list Epicor_Lists_Model_List */
        $session = Mage::getSingleton('core/session');
        $helper = Mage::helper('epicor_lists');
        if ($id) {
            $model->load($id);
            if ($model->getId()) {
                $erpCode = $model->getErpCode();
                $ownerId = $model->getOwnerId();
                $customerSession = Mage::getSingleton('customer/session')->getCustomer();
                $checkMasterErp = $model->isValidEditForErpAccount($customerSession, $id);
                $checkCustomer = $model->isValidEditForCustomers($customerSession, $id, $ownerId);
                //$defaultCheck   = $model->isValidForCustomer(Mage::getSingleton('customer/session')->getCustomer());
                if ((!$checkMasterErp) || (!$checkCustomer)) {
                    $session->addError($helper->__('Could not delete List %d', $erpCode));
                } else {
                    if ($model->delete()) {
                        if (!$mass) {
                            $session->addSuccess($helper->__('List Deleted'));
                        } else {
                            return $erpCode;
                        }
                    } else {
                        $session->addError($helper->__('Could not delete List ' . $erpCode));
                    }
                }
            }
        }
    }

    public function editAction()
    {
        $list = $this->loadEntity();
        $listData = $list->getData();
        if (!empty($listData)) {
            //$check = $this->loadEntity()->isValidForCustomer(Mage::getSingleton('customer/session')->getCustomer());
            //A master shopper only sees (and can only amend and delete) lists with a list type of pre-defined or favourite 
            //and which are assigned to his ERP Account and no other ERP Account
            $checkMasterErp = $this->loadEntity()->isValidEditForErpAccount(Mage::getSingleton('customer/session')->getCustomer(), $list->getId());
            //non master shopper/Registered shopper/Registered Guest only sees (and can only amend and delete) lists with a list type of pre-defined or favourite 
            //and which are assigned to his ERP Account and customer and no other ERP Account / customer 
            $checkCustomer = $this->loadEntity()->isValidEditForCustomers(Mage::getSingleton('customer/session')->getCustomer(), $list->getId(), $list->getOwnerId());
            if ((!$checkMasterErp) || (!$checkCustomer)) {
                Mage::getSingleton('core/session')->addError(Mage::helper('epicor_lists')->__('This list type cannot be edited'));
                session_write_close();
                $this->_redirect('*/*/');
            }
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Loads List
     *
     * @return Epicor_Lists_Model_List
     */
    protected function loadEntity()
    {
        $id = base64_decode($this->getRequest()->getParam('id', null));
        $list = Mage::getModel('epicor_lists/list')->load($id);
        /* @var $list Epicor_Lists_Model_List */
        //Mage::register('list', $list);
        return $list;
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
        $session = Mage::getSingleton('core/session');
        $customer = Mage::getModel('customer/customer')->load($customerId);
        /* @var $customer Epicor_Comm_Model_Customer */
        if ($customer->isObjectNew()) {
            Mage::getSingleton('core/session')->addError(Mage::helper('adminhtml')->__('Please select a Customer.'));
        } else {
            $explodedIds = explode(',', $ids[0]);
            $customer->addLists($explodedIds);
            $customer->save();
            $session->addSuccess($helper->__('Customer assigned to ' . count($explodedIds) . ' Lists '));
        }
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
        if (strpos($ids[0], ',') !== false) {
            $ids = explode(',', $ids[0]);
        }
        $helper = Mage::helper('epicor_lists');
        /* @var $list Epicor_Lists_Helper_Data */
        $ListDatas = $helper->getListDatas($ids);
        $listModel = Mage::getModel('epicor_lists/list');
        /* @var $list Epicor_Lists_Model_List */
        $customerSession = Mage::getSingleton('customer/session')->getCustomer();
        foreach ($ListDatas as $ListSeparateData) {
            //get List Id
            $id = $ListSeparateData->getId();
            //get List Erp code
            $erpCode = $ListSeparateData->getErpCode();
            //get List Owner Id
            $ownerId = $ListSeparateData->getOwnerId();
            $checkMasterErp = $listModel->isValidEditForErpAccount($customerSession, $id);
            $checkCustomer = $listModel->isValidEditForCustomers($customerSession, $id, $ownerId);
            if ((!$checkMasterErp) || (!$checkCustomer)) {
                //get the erp code
                $errorIds[] = $erpCode;
            } else {
                $successIds[] = $id;
                $deleteList = $this->delete($id, true);
                $successErps[] = $deleteList;
            }
        }
        $session = Mage::getSingleton('core/session');
        if (!empty($errorIds)) {
            $errorLists = implode(', ', $errorIds);
            $session->addError($helper->__('Could not delete ' . count(array_keys($errorIds)) . ' Lists. ' . "List Reference Code: (" . $errorLists . ")"));
        }
        if (!empty($successIds)) {
            $successList = implode(', ', $successErps);
            $session->addSuccess($helper->__(count(array_keys($successIds)) . ' Lists deleted. ' . "List Reference Code: (" . $successList . ")"));
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
        $session = Mage::getSingleton('core/session');
        $customer = Mage::getModel('customer/customer')->load($customerId);
        /* @var $customer Epicor_Comm_Model_Customer */
        if ($customer->isObjectNew()) {
            Mage::getSingleton('core/session')->addError(Mage::helper('adminhtml')->__('Please select a Customer.'));
        } else {
            $explodedIds = explode(',', $ids[0]);
            $customer->removeLists($explodedIds);
            $customer->save();
            $session->addSuccess($helper->__('Customer removed from ' . count($explodedIds) . ' Lists '));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Sends the Lists grid Selector
     *
     * @return void
     */
    public function selectorAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Delivery Address page
     *
     * @return void
     */
    public function deliveryaddressAction()
    {
        $this->_title($this->__('Delivery Address Select'));
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Delivery address ajax reload of grid tab
     *
     * @return void
     */
    public function deliveryaddressgridAction()
    {
        //$this->loadEntity();
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('epicor_lists/customer_account_deliveryaddress_grid')->toHtml());
    }

    /**
     * Change address Action in delivery address grid
     * 
     * return json
     */
    public function changeshippingaddressAction()
    {
        $frontendHelper = Mage::helper('epicor_lists/frontend_restricted');
        /* @var $frontendHelper Epicor_Lists_Helper_Frontend_Restricted */
        $addressId = $this->getRequest()->getParam('addressid');
        $removeProducts = $frontendHelper->checkProductAddress($addressId);
        $this->sendAjaxResponse($removeProducts, $addressId);
    }

    /**
     * Change address Action in delivery address grid
     * 
     * return json
     */
    public function changeshippingaddressajaxAction()
    {
        $frontendHelper = Mage::helper('epicor_lists/frontend_restricted');
        /* @var $frontendHelper Epicor_Lists_Helper_Frontend_Restricted */
        $addressId = $this->getRequest()->getParam('addressid');
        $removeProducts = $frontendHelper->checkProductAddressNew($addressId, 'shipping');
        $this->sendAjaxResponse($removeProducts, $addressId);
    }
    
    public function changebillingaddressajaxAction()
    {
        $frontendHelper = Mage::helper('epicor_lists/frontend_restricted');
        /* @var $frontendHelper Epicor_Lists_Helper_Frontend_Restricted */
        $addressId = $this->getRequest()->getParam('addressid');
        $removeProducts = $frontendHelper->checkProductAddressNew($addressId, 'billing');
        $this->sendAjaxResponse($removeProducts, $addressId);
    }
    
    /**
     * Address Select Ajax Action
     */
    public function selectaddressajaxAction()
    {
        $addressId = $this->getRequest()->getParam('addressid');
        if ($addressId) {
            $listHelper = Mage::helper('epicor_lists/frontend_restricted');
            /* @var $listHelper Epicor_Lists_Helper_Frontend_Restricted */
            $listHelper->setRestrictionAddress($addressId);
            $helper = Mage::helper('epicor_lists');
            $resetBranchPickup = $helper->resetLocationFilter();
        }
    }
    
    protected function sendAjaxResponse($values, $addressId)
    {
        $frontendHelper = Mage::helper('epicor_lists/frontend_restricted');
        /* @var $frontendHelper Epicor_Lists_Helper_Frontend_Restricted */
        
        $result = array(
            'type' => 'success',
            'values' => !empty($values) ? $values : array(),
            'addressid' => $addressId,
            'details' => $frontendHelper->getShippingAddress($addressId)
        );
        
        Mage::App()->getResponse()->setHeader('Content-type', 'application/json');
        Mage::App()->getResponse()->setBody(json_encode($result));
    }

    /**
     * Shows the cart popup, If the item are not available for the selected address
     * return html
     */
    public function cartPopupAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('checkout/cart_sidebar')->setTemplate('epicor/lists/checkout/sidebar.phtml')->toHtml());
    }

    /**
     * Remove the items in the cart(After user confirmation), If the item is not available for that delivery address
     * return boolean
     */
    public function removeItemsInCartAction()
    {
        $postValues = Mage::App()->getRequest()->getParam('removeitems');
        $addressId = Mage::App()->getRequest()->getParam('addressid');
        $removeProducts = explode(',', $postValues);
        $cartHelper = Mage::helper('checkout/cart');
        /* @var $cartHelper MAge_Checkout_Helper_Cart */
        $items = $cartHelper->getCart()->getItems();
        foreach ($items as $item) {
            if (in_array($item->getProduct()->getId(), $removeProducts)) {
                $itemId = $item->getItemId();
                $cartHelper->getCart()->removeItem($itemId)->save();
            }
        }
        if ($addressId) {
            $listHelper = Mage::helper('epicor_lists/frontend_restricted');
            /* @var $listHelper Epicor_Lists_Helper_Frontend_Restricted */
            $listHelper->setRestrictionAddress($addressId);
        }
        $this->_redirect('checkout/cart');
        /* comment for WSO-3375 */
//        $controller = Mage::App()->getRequest()->getParam('page');
//        if($controller =="chooseaddress") {
//           $this->_redirect('lists/list/deliveryaddress');
//        } else {
//          $this->_redirect('checkout/cart');
//        }
    }
    
    public function validateCodeAction()
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */
        $this->getResponse()->setBody(json_encode($helper->validateNewListCode($this->getRequest())));
    }
}
