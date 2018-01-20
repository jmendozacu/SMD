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

}
