<?php

/**
 * Manage controller
 *
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Account_ManageController extends Epicor_SalesRep_Controller_Abstract
{
    protected $_product = array();
    
    public function preDispatch()
    {
        parent::preDispatch();
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        $helper->registerAccounts();

        $base = $helper->getBaseSalesRepAccount();
        $managed = $helper->getManagedSalesRepAccount();

        if ($base->getId() != $managed->getId() && !$this->getRequest()->getPost() && !$this->getRequest()->getActionName() == 'reset') {
            $link = '<a href="' . Mage::getUrl('*/*/reset') . '">' . $this->__('Return to My Sales Rep Account') . '</a>';
            Mage::getSingleton('core/session')->addSuccess($this->__('You are currently managing Sales Rep Account: %s, %s', $managed->getName(), $link));
        }
    }

    /**
     * Index action 
     */
    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }
 
    public function saveAction()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        $salesRepAccount = $helper->getManagedSalesRepAccount();

        $data = $this->getRequest()->getPost();

        if ($data) {           
            $salesRepAccount->setName($data['name']);
            $salesRepAccount->save();

            Mage::getSingleton('core/session')->addSuccess($this->__('Sales Rep Account Updated Successfully'));
        }

        $this->_redirect('*/*/');
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
        $this->_redirect('*/*/pricelist');
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


    public function pricingrulesAction()
    {
        $this->loadLayout()->renderLayout();
    }

    public function manageAction()
    {
        $encodedId = $this->getRequest()->getParam('salesrepaccount');
        $salesRepAccountId = unserialize(base64_decode($encodedId));

        $baseAccount = Mage::registry('sales_rep_account_base');

        if ($baseAccount->hasChildAccount($salesRepAccountId)) {
            $customerSession = Mage::getSingleton('customer/session');
            $customerSession->setManageSalesRepAccountId($salesRepAccountId);
        }

        $this->_redirect('*/*/index');
    }

    public function resetAction()
    {
        $customerSession = Mage::getSingleton('customer/session');
        $customerSession->setManageSalesRepAccountId(false);
        $this->_redirect('*/*/index');
    }

    public function pricingrulespostAction()
    {
        $dataArr = $this->getRequest()->getParams();

        if (isset($dataArr['rule'])) {
            $dataArr['conditions'] = $dataArr['rule']['conditions'];
            unset($dataArr['rule']);
        }

        $data = new Varien_Object($dataArr);

        $rule = Mage::getModel('epicor_salesrep/pricing_rule')->load($data->getId());
        /* @var $rule Epicor_SalesRep_Model_Pricing_Rule */

        unset($dataArr['id']);

        $rule->loadPost($dataArr);

        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        $salesRepAccount = $helper->getManagedSalesRepAccount();

        $rule->setName($data->getName());
        $rule->setSalesRepAccountId($salesRepAccount->getId());
        $rule->setFromDate($data->getFromDate());
        $rule->setToDate($data->getToDate());
        $rule->setIsActive($data->getIsActive());
        $rule->setPriority($data->getPriority());
        $rule->setActionOperator($data->getActionOperator());
        $rule->setActionAmount($data->getActionAmount());

        $rule->save();
    }

    public function deletepricingruleAction()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        $salesRepAccount = $helper->getManagedSalesRepAccount();

        $dataArr = $this->getRequest()->getParams();

        if (!empty($dataArr['id'])) {

            $rule = Mage::getModel('epicor_salesrep/pricing_rule')->load($dataArr['id']);
            /* @var $rule Epicor_SalesRep_Model_Pricing_Rule */

            if ($salesRepAccount->getId() == $rule->getSalesRepAccountId()) {
                if (!$rule->isObjectNew()) {
                    $rule->delete();
                    Mage::getSingleton('core/session')->addSuccess($this->__('Pricing Rule Deleted Successfully'));
                }
            }
        }

        $this->_redirect('*/*/pricingrules');
    }

    public function hierarchyAction()
    {
        $this->loadLayout()->renderLayout();
    }

    public function salesrepsAction()
    {
        $this->loadLayout()->renderLayout();
    }

    public function salesrepaddAction()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        $salesRepAccount = $helper->getManagedSalesRepAccount();

        $data = $this->getRequest()->getPost();

        if ($data) {

            try {
                $customer = Mage::getModel('customer/customer');
                /* @var $customer Epicor_Comm_Model_Customer */
                $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                $customer->loadByEmail($data['email_address']);

                $error = '';
                $msg = '';

                if (!$customer->isObjectNew()) {
                    $currentId = $customer->getSalesRepAccountId();
                    if (!empty($currentId) && $salesRepAccount->getId() != $currentId) {
                        $error = $this->__('Existing Sales Rep Email Address Found. Cannot assign as a Sales Rep');
                    } else {
//                        $customer->setSalesRepId($data['sales_rep_id']);
//                        $customer->setSalesRepAccountId($salesRepAccount->getId());
//                        $customer->save();
//                        $msg = $this->__('Existing Non-Sales Rep Customer Found. They have been updated to be a Sales Rep for %s', $salesRepAccount->getName());
                        $msg = $this->__('Email assigned to existing customer / supplier. Cannot assign as a Sales Rep');
                    }
                } else {
                    $store = Mage::app()->getWebsite()->getDefaultStore();
                    $customer->setStore($store);
                    $customer->setFirstname($data['first_name']);
                    $customer->setLastname($data['last_name']);
                    $customer->setEmail($data['email_address']);
                    $customer->setSalesRepId($data['sales_rep_id']);
                    $customer->setSalesRepAccountId($salesRepAccount->getId());
                    $customer->setPassword($customer->generatePassword(10));
                    $customer->save();
                    $customer->sendNewAccountEmail();

                    $msg = $this->__('New Sales Rep Created. An email has been sent to %s with login details', $data['email_address']);
                }
            } catch (Exception $ex) {
                Mage::logException($ex);
                $error = $this->__('An error occured, please try again');
            }
            $session = Mage::getSingleton('core/session');

            if (!empty($error)) {
                $session->addError($error);
            } else {
                $session->addSuccess($msg);
            }
        }

        $this->_redirect('*/*/salesreps');
    }

    public function childaccountaddAction()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        $salesRepAccount = $helper->getManagedSalesRepAccount();
        /* @var $salesRepAccount Epicor_SalesRep_Model_Account */

        $data = $this->getRequest()->getPost();

        if ($data && $helper->canAddChildrenAccounts()) {

            try {
                $child = Mage::getModel('epicor_salesrep/account')->load($data['child_sales_rep_account_id'],'sales_rep_id');
                /* @var $child Epicor_SalesRep_Model_Account */

                $error = '';
                $msg = '';

                if (!$child->isObjectNew()) {
                    if(Mage::getStoreConfig('epicor_salesrep/management/frontend_children_addexisting')){
                        if($child->getId() == $salesRepAccount->getId() || $child->hasChildAccount($salesRepAccount->getId())){
                            $error = $this->__('Existing Sales Rep Account Found. Cannot assign as a Children due Hierarchy Loop');
                        }else if(in_array($child->getId(),$salesRepAccount->getChildAccountsIds())){
                            $error = $this->__('Existing Sales Rep Account Found. Account is already a Child');
                        } else {
                            $salesRepAccount->addChildAccount($child->getId());
                            $salesRepAccount->save();
                            $msg = $this->__('Existing Sales Rep Account Found. It has been updated to be a Children for %s', $salesRepAccount->getName());
                        }
                    }else{
                        $error = $this->__('Existing Sales Rep Account Found. Cannot create this Account');
                    }
                } else {
                    $child->setCompany($salesRepAccount->getCompany());
                    $child->setSalesRepId($data['child_sales_rep_account_id']);
                    $child->setName($data['child_sales_rep_account_name']);
                    $child->setCatalogAccess($salesRepAccount->getCatalogAccess());
                    $child->save();
                    $salesRepAccount->addChildAccount($child->getId());
                    $salesRepAccount->save();
                    $msg = $this->__('New Sales Rep Account Created. It has been assigned to be a Children for %s', $salesRepAccount->getName());
                }
            } catch (Exception $ex) {
                Mage::logException($ex);
                $error = $this->__('An error occured, please try again');
            }
            $session = Mage::getSingleton('core/session');

            if (!empty($error)) {
                $session->addError($error);
            } else {
                $session->addSuccess($msg);
            }
        }

        $this->_redirect('*/*/hierarchy');
    }
    
    public function unlinkchildaccountAction()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        $salesRepAccount = $helper->getManagedSalesRepAccount();
        /* @var $salesRepAccount Epicor_SalesRep_Model_Account */
        
        try {
            $childAccountId = unserialize(base64_decode($this->getRequest()->getParam('salesrepaccount')));
            $salesRepAccount->removeChildAccount($childAccountId);
            $salesRepAccount->save();
            $msg = $this->__('Child Sales Rep Account has been unlinked from %s', $salesRepAccount->getName());
        } catch (Exception $ex) {
            Mage::logException($ex);
            $error = $this->__('An error occured, please try again');
        }
        
        $session = Mage::getSingleton('core/session');
        if (!empty($error)) {
            $session->addError($error);
        } else {
            $session->addSuccess($msg);
        }

        $this->_redirect('*/*/hierarchy');
    }

    public function erpaccountspopupAction()
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
    public function erpaccountsAction()
    {
        $this->loadLayout()->renderLayout();
    }

    public function erpaccountsgridAction()
    {
        $customers = $this->getRequest()->getParam('erpaccounts');
        $this->loadLayout();
        $this->getLayout()->getBlock('manage.erpaccounts')->setSelected($customers);
        $this->renderLayout();
    }

    public function erpaccountspostAction()
    {
        $erpAccounts = $this->getRequest()->getParam('selected_erpaccounts');
        if ($data = $this->getRequest()->getPost()) {
            if (!is_null($erpAccounts)) {
                $salesReps = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['erpaccounts']));
                // load current and check if any need to be removed

                $helper = Mage::helper('epicor_salesrep/account_manage');
                /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

                $salesRepAccount = $helper->getManagedSalesRepAccount();
                $salesRepAccount->setErpAccounts($salesReps);
                $salesRepAccount->save();
                Mage::getSingleton('core/session')->addSuccess($this->__('Sales Rep Account Updated Successfully'));
            }
        }

        $this->_redirect('*/*/erpaccounts');
    }

    public function listerpaccountsAction()
    {
        $this->loadLayout()->renderLayout();
    }

    public function listerpaccountsgridAction()
    {
        $parms = $this->getRequest()->getParams();
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('epicor_salesrep/account_manage_listerpaccounts_grid')
        );
        $this->renderLayout();
    }
    public function unlinkSalesRepAction()
    {
        $ids = (array) $this->getRequest()->getParam('salesreps');

        $session = Mage::getSingleton('core/session');

        $error = $this->_processSalesRep($ids, 'unlink');

        if (!$error) {
            $session->addSuccess($this->__('%s Sales Reps unlinked', count($ids)));
        } else {
            $session->addError($this->__('Could not unlink one or more Sales Reps, please try again'));
        }

        $this->_redirectToSalesReps();
    }

    public function deleteSalesRepAction()
    {
        $ids = (array) $this->getRequest()->getParam('salesreps');
        $session = Mage::getSingleton('core/session');

        $error = $this->_processSalesRep($ids, 'delete');

        if (!$error) {
            $session->addSuccess($this->__('%s Sales Reps deleted', count($ids)));
        } else {
            $session->addError($this->__('Could not delete one or more Sales Reps, please try again'));
        }

        $this->_redirectToSalesReps();
    }

    protected function _redirectToSalesReps()
    {
        $params = array();

        $salesRepId = $this->getRequest()->getParams('salesrepacc');

        if (empty($salesRepId)) {
            $params['salesrepacc'] = $salesRepId;
        }

        $this->_redirect('*/*/salesreps', $params);
    }

    protected function _processSalesRep($ids, $action)
    {
        $session = Mage::getSingleton('core/session');
        $error = false;
        Mage::register('isSecureArea', true, true);

        if (!empty($ids)) {
            $helper = Mage::helper('epicor_salesrep/account_manage');
            /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

            $salesRepAccount = $helper->getManagedSalesRepAccount();

            foreach ($ids as $id) {
                $customer = Mage::getModel('customer/customer')->load($id);
                try {
                    if ($customer->isObjectNew()) {
                        $error = true;
                        $session->addError($this->__('1Unable to find the Sales Rep to %s', $action));
                    } else if ($customer->getSalesRepAccountId() != $salesRepAccount->getId()) {
                        $error = true;
                        $session->addError($this->__('2Unable to find the Sales Rep to %s', $action));
                    } else {
                        if ($action == 'delete' && !$customer->delete()) {
                            $error = true;
                            $session->addError('Could not delete Sales Rep Account ' . $customer->getEmailAddress());
                        } else if ($action == 'unlink') {
                            $customer->setSalesRepAccountId(false);
                            $customer->save();
                        }
                    }
                } catch (Exception $e) {
                    $session->addError('Could not %s Sales Rep Account ' . $customer->getEmailAddress(), $action);
                    Mage::logException($e);
                }
            }
        } else {
            $error = true;
        }

        Mage::unregister('isSecureArea');

        return $error;
    }
     /**
     * List pricelist grid
     *
     * @return void
     */
    protected function pricelistAction()
    {
        $this->loadLayout()->renderLayout();
    }
     /**
     * List ajax reload of pricelist grid tab
     *
     * @return void
     */
    public function pricelistgridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('epicor_salesrep/account_manage_pricelist_grid')->toHtml());
    }
      /**
     * display list details for salesreps
     *
     * @return void
     */
    protected function editAction()
    {
        $this->loadLayout()->renderLayout();
    }
      /**
     * display list details for salesreps
     *
     * @return void
     */
    protected function newAction()
    {
        $this->_redirect('*/*/edit');
    //    $this->loadLayout()->renderLayout();
    }
     public function masqueradeaccountsAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
     public function masqueradegridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('epicor_salesrep/manage_select_grid')->toHtml());
    }
    public function editProductsAction()
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
        //Mage::register('list', $list);
        return $list;
    }
    public function saveListAction()
    {

        $data = $this->getRequest()->getPost();
        $list = Mage::getModel('epicor_lists/list')->load($data['id']);

        if ($data) {

            $list->setTitle($data['title']);
            $list->setNotes($data['notes']);
            $list->setPriority($data['priority']);
            $list->setActive(isset($data['active']) ? 1 : 0);
            if ($list->isObjectNew()) {
                $list->setErpCode($data['erp_code']);
                $list->setType($data['type']);
            } else {
                $overrides = isset($data['erp_override']) ? $data['erp_override'] : array();
                $list->setErpOverride($overrides);
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
             $settings = isset($data['settings']) ? $data['settings'] : array();

            if (isset($data['exclude_selected_products'])) {
                $settings[] = 'E';
            }

            $list->setSettings($settings);
            $list->save();

            Mage::getSingleton('core/session')->addSuccess($this->__('List Updated Successfully'));
        }

    }
    public function testPricingRulesAction(){
        $valid = false;
        $salesrepId = $this->getRequest()->getParam('salesrepId');
        $productId = $this->getRequest()->getParam('product');
        $currency = $this->getRequest()->getParam('currency');
        $price = $this->getRequest()->getParam('price');
        $listProductId = $this->getRequest()->getParam('listProductId');
        $product = Mage::getModel('catalog/product')->load($productId);
        $pricingRules = Mage::getModel('epicor_salesrep/pricing_rule')->getCollection()
                        ->addFieldToFilter('sales_rep_account_id', array('eq'=>$salesrepId));    
        $originalPriceValue = $this->getRequest()->getParam('price');
        foreach($pricingRules->getItems() as $pricingRule){   // loop through each pricing rule for salesrep
            if($pricingRule->getIsActive()){                
                $rules = unserialize($pricingRule->getConditionsSerialized());            
                foreach($rules['conditions'] as $rule){                
                    $variable = '';
                    switch ($rule['attribute']) {
                        case 'attribute_set_id':
                            $variable = $product->getAttributeSetId();
                            break;
                        case 'sku':
                            $variable = $product->getSku(); 
                            break;

                        case 'category_ids':
                            $variable = $product->getCategoryIds();
                            //convert so that products with multiple categories are processed correctly
                            $rule['operator'] = ($rule['operator'] == "==") ? "()": $rule['operator'];
                            $rule['operator'] = ($rule['operator']== "!=") ?  "!()": $rule['operator'];
                            break;
                        default:
                            break;
                    }    
                    $valid = $this->evaluateOperator($variable, $rule['operator'], $rule['value']);  
                    if(!$valid){
                        break 2;
                    }
                }            
                // only use salesrep rules if $valid == true 
                $passed = true;
                if($valid){    
                    $msq = Mage::getModel('epicor_comm/message_request_msq');
                    /* @var $msq Epicor_Comm_Model_Message_Request_Msq */

                    $msq->setTrigger('salesrep_price_lists');
                    
                    // use the supplied erp account if available or the default erp account
                    $requiredErpaccount = $suppliederpAccount ? $suppliederpAccount : Mage::getStoreConfig('customer/create_account/default_erpaccount');
                    $msq->setAccountNumber($requiredErpaccount);

                    if ($msq->isActive()) {
                        $msq->addProduct($product, 1);
                        $msq->sendMessage();
                    }
                    $actionOperator = $pricingRule->getActionOperator();
                    $actionAmount = $pricingRule->getActionAmount();
                    
                    //NB the cost price isn't altered by the MSQ, if that option is selected
                  
                    $priceArray = array('list'=>'customer_price', 'base'=>'base_price', 'cost'=>'cost');
                    $price = $product->getData($priceArray[$actionOperator]);
                    //cost is % above cost price 
                    if($actionOperator == 'cost'){
                        $newPriceLimitAfterUpdate = ($price / 100) * (100 + $actionAmount);

                        if(($newPriceLimitAfterUpdate < $originalPriceValue) || $originalPriceValue < $price){
                            $passed = false;
                        } 
                    }else{
                        //list and base are % below customer_price and base_price 
                        $newPriceLimitAfterUpdate = ($price / 100) * (100 - $actionAmount);
                        if($newPriceLimitAfterUpdate > $originalPriceValue || $originalPriceValue > $price ){
                            $passed = false;
                        }   
                    }
                }
            }
        }     
       
        echo json_encode(array('passed' => $passed, 'original_value'=>$product->getData($priceArray[$actionOperator]),'type' => 'success'));
     
    }
    private function evaluateOperator($variable, $operator, $value){
        $valid = false;
         switch ($operator) {
                 case "==":
                //equals  
                $valid = ($variable ==  $value) ? true : false;    
                break;
            case "!=":                                
                //not equals 
                 $valid = ($variable !=  $value) ? true : false;
                break;
            case ">=":
                //greater than or equal    
                $valid = ($variable >=  $value) ? true : false; 
                break;
            case "<=":
                //less than or equal    
                 $valid = ($variable <=  $value) ? true : false;
                break;
            case ">":
                //greater than
                 $valid = ($variable >  $value) ? true : false;  
                break;
            case "<":
                //less than
                 $valid = ($variable <  $value) ? true : false;
                break;
            case "{}":
                //contains
                $valid = (strpos(strtolower($variable), strtolower($value)) !== false) ? true : false;                                    
                break;
            case "!{}":
                //does not contain
                $valid = (strpos($variable, $value) === false) ? true : false;           
                break;          
            case "()":
                //is one of
                $valueArray = array_flip(explode(', ', $value));
                $variable = is_array($variable) ? $variable : array($variable);
                $valid = (array_intersect_key(array_flip($variable), $valueArray)) ? true : false;   
                break;
            case "!()":
                //is not one of
                $valueArray = array_flip(explode(', ', $value));
                $valid = (!array_intersect_key(array_flip($variable), $valueArray)) ? true : false;   
                break;
            default:
                break;
        }
        return $valid;
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
      //      $checkCustomer = $listModel->isValidEditForCustomers($customerSession, $id, $ownerId);
         //   if ((!$checkMasterErp) || (!$checkCustomer)) {
            if ((!$checkMasterErp)) {
                //get the erp code
                $error[] = $erpCode;
            } else {
                $success[] = $ListSeparateData->getTitle();
                $this->delete($id, is_array($id) ? true : false);
            }
        }
        $session = Mage::getSingleton('core/session');
        if (!empty($error)) {
            $errorLists = implode(', ', $error);
            $session->addError($helper->__('Could not delete ' . count(array_keys($error)) . ' Lists. ' . "List Reference Code: (" . $errorLists . ")"));
        }
        if (!empty($success)) {
            $successList = implode(', ', $success);
            $session->addSuccess($helper->__(count(array_keys($success)) . ' Lists deleted. ' . "Title: " . $successList));
        }
        $this->_redirect('*/*/pricelist');
    }
     public function massAssignStatusAction()
    {
        $ids = explode(',', $this->getRequest()->getParam('listid'));
        $assignContractIds = array();
        foreach ($ids as $id) {
            $list = Mage::getModel('epicor_lists/list')->load($id);            
            $status = $list->getActive() ? false: true;          
          
            if($status){
                $active[] = $list->getTitle();
            }else{
                $disable[] = $list->getTitle();
            }
            $list->setActive($status);
            $list->save();
            
        }

        $helper = Mage::helper('epicor_salesrep');
        $session = Mage::getSingleton('core/session');
        if (!empty($active)) {
            $session->addSuccess($helper->__('List Status Changed to Active. Title : '.implode(', ', $active)));
        }
        if (!empty($disable)) {
            $session->addNotice($helper->__('Lists Status Changed to Disabled. Title : '.implode(', ', $disable)));
        }

        $this->_redirect('*/*/pricelist');
    }

}
