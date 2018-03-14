<?php

/**
 * List controller for frontend
 *
 * @category   Epicor
 * @package    Epicor_Salesrep
 * @author     Epicor Websales Team
 */
class Epicor_Salesrep_ListController extends Mage_Core_Controller_Front_Action
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
     * Saves details for the list
     *
     * @param Epicor_Lists_Model_List $list
     * @param array $data
     *
     */
    protected function processDetailsSave($list, $data)
    {
        $list->setOwnerId($data['owner_id']);
        $list->setTitle($data['title']);
        $list->setNotes($data['notes']);
        $list->setDescription($data['description']);
        $list->setPriority(isset($data['priority']) ? $data['priority'] : 0);
        $list->setActive(isset($data['active']) ? $data['active'] : 0);
        $list->setSalesrepErpaccount(isset($data['salesrep_erpaccount']) ? $data['salesrep_erpaccount'] : 0);
        
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
                    if (!array_key_exists('start_time', $data)) {
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
                    if (!array_key_exists('end_time', $data)) {
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
            $excludeSelectedProducts = $data['exclude_selected_products'];
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
            //add type for pricelists if new list
            if(!isset($data['type'])){
                $data['type'] = 'Pr';
            }
            //add owner id if new list
            if(empty($data['owner_id'])){
                $customer = Mage::getModel('customer/session')->getCustomer();
                 $data['owner_id'] = $customer->getId();
                 $data['sales_rep_id'] = $customer->getSalesRepAccountId();
        /* @var $customer Epicor_Comm_Model_Customer */

            }
            /* @var $list Epicor_Lists_Model_List */

            $this->processDetailsSave($list, $data);

            $typeInstance = $list->getTypeInstance();
            if ($typeInstance->isSectionEditable('erpaccounts')) {
                $this->processERPAccountsSave($list, $data);
            }
            if ($typeInstance->isSectionEditable('products')) {
                $this->processProductsSave($list, $data);
            }
            if ($typeInstance->isSectionEditable('pricing')) {
                $this->processProductsPricingSave($list, $data);
            }
// orphan check removed as taking out all erpaccounts - will reinsert if required            
//           $list->orphanCheck();
            $valid = $list->validate();
            $session = Mage::getSingleton('core/session');

            if ($valid === true) {
                $importProductErrors = false; //$this->importProducts($list);
                $list->save();

                $session = Mage::getSingleton('core/session');
                $session->addSuccess($this->__('List Saved Successfully'));

                $this->_redirect('salesrep/account_manage/edit', array('id' => $list->getId()));
            } else {
                $session->addError($this->__('The Following Error(s) occurred on Save:'));
                foreach ($valid as $error) {
                    $session->addError($error);
                }
                $session->setFormData($data);
                $this->_redirect('salesrep/account_manage/edit', array('id' => $list->getId()));
            }
        } else {
               $this->_redirect('salesrep/account_manage/edit', array('id' => $list->getId()));
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
        $erpaccounts = $this->getRequest()->getParam('links');
        if (isset($erpaccounts['erpaccounts'])) {
            Mage::app()->getRequest()->setPost('selected_erpaccounts', true);
            $this->saveERPAccounts($list, $data);
            // if erp_account_link_type = 'N', save erp account exclusion indicator as 'N', else save value
            $dataExclusion = isset($data['erp_accounts_exclusion']) ? 'Y' : 'N';
            $exclusion = $linkType == 'N' ? 'N' : $dataExclusion;
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
            $skuPricing = array();
            foreach ($pricing as $key => $value){
                $sku = Mage::getModel('catalog/product')->load($key)->getSku();
                $skuPricing[$sku] = $value;
            }
        //    $list->addPricing($pricing);
            $list->addPricing($skuPricing);
        }
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
    public function productpricingAction()
    {
        $listId = $this->getRequest()->getParam('list');
        $productId = $this->getRequest()->getParam('product');
        $productSku = Mage::getModel('catalog/product')->load($productId)->getSku();
        $listProduct = Mage::getModel('epicor_lists/list_product')->getCollection();
        /* @var $listProduct Epicor_Lists_Model_Resource_List_Product_Collection */
        $listProduct->addFieldToFilter('list_id', $listId);
        $listProduct->addFieldToFilter('sku', $productSku);

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
    
}

