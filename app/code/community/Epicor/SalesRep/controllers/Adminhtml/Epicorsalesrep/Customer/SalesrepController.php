<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SalesrepController
 *
 * @author Paul.Ketelle
 */
class Epicor_SalesRep_Adminhtml_Epicorsalesrep_Customer_SalesrepController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/epicor_salesrep');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_initSalesRepAccount();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {

        if ($data = $this->getRequest()->getPost()) {
            $salesRep = $this->_initSalesRepAccount();
            /* @var $salesRep Epicor_SalesRep_Model_Account */

            Mage::getSingleton('adminhtml/session')->setFormData($data);

            try {
                $this->_processDetailsSave($salesRep, $data);

                if ($salesRep->isObjectNew()) {
                    $salesRep->save();
                }

                $this->_processSalesRepsSave($salesRep, $data);
                $this->_processErpAccountsSave($salesRep, $data);
                $this->_processHierarchySave($salesRep, $data);
                //$this->_processPricingRulesSave($salesRep, $data);

                $salesRep->save();

                if (!$salesRep->getId()) {
                    Mage::throwException(Mage::helper('epicor_comm')->__('Error saving Sales Rep Account'));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_comm')->__('Sales Rep Account was successfully saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $salesRep->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($salesRep && $salesRep->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $salesRep->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            }

            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_comm')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

    protected function _processDetailsSave(&$salesRep, $data)
    {
        if (isset($data['sales_rep_name'])) {
            $salesRep->setName($data['sales_rep_name']);
        }
        if (isset($data['sales_rep_id'])) {
            $salesRep->setSalesRepId($data['sales_rep_id']);
        }
        if (isset($data['catalog_access'])) {
            $allowed = !empty($data['catalog_access']) ? $data['catalog_access'] : false;
            $salesRep->setCatalogAccess($allowed);
        }
    }

    protected function _processHierarchySave(&$salesRep, $data)
    {
        $parents = $this->getRequest()->getParam('selected_parents', false);
        if ($parents !== false) {
            $this->_saveParentAccounts($salesRep, $data);
        }

        $children = $this->getRequest()->getParam('selected_children', false);
        if ($children !== false) {
            $this->_saveChildAccounts($salesRep, $data);
        }
    }

    /**
     * 
     * @param Epicor_SalesRep_Model_Account $salesRep
     * @param type $data
     */
    protected function _saveParentAccounts(&$salesRep, $data)
    {
        $parents = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['parents']));
        // load current and check if any need to be removed

        $salesRep->setParentAccounts($parents);
    }

    /**
     * 
     * @param Epicor_SalesRep_Model_Account $salesRep
     * @param type $data
     */
    protected function _saveChildAccounts(&$salesRep, $data)
    {
        $children = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['children']));
        // load current and check if any need to be removed

        $salesRep->setChildAccounts($children);
    }

    protected function _processSalesRepsSave(&$salesRep, $data)
    {
        $salesReps = $this->getRequest()->getParam('selected_salesreps');
        if (!is_null($salesReps)) {
            $this->_saveSalesReps($salesRep, $data);
        }
    }

    protected function _saveSalesReps(&$salesRep, $data)
    {
        $salesReps = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['salesreps']));

        // load current and check if any need to be removed

        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->addFieldToFilter('sales_rep_account_id', $salesRep->getId());

        $existing = array();
        /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        foreach ($collection->getItems() as $customer) {
            if (!in_array($customer->getId(), $salesReps)) {
                $customer->setSalesRepAccountId(false);
                $customer->save();
            } else {
                $existing[] = $customer->getId();
            }
        }

        // loop through passed values and only update customers who are new
        foreach ($salesReps as $customerId) {
            if (!in_array($customerId, $existing)) {
                $customerModel = Mage::getModel('customer/customer')->load($customerId);
                if (!$customerModel->isObjectNew()) {
                    $customerModel->setSalesRepAccountId($salesRep->getId());
                    $customerModel->save();
                }
            }
        }
    }

    protected function _processPricingRulesSave(&$salesRep, $data)
    {
        $salesRep->setName($data['name']);
        $salesRep->setSalesRepId($data['sales_rep_id']);
    }

    protected function _processErpAccountsSave(&$salesRep, $data)
    {
        $erpAccounts = $this->getRequest()->getParam('selected_erpaccounts');
        if (!is_null($erpAccounts)) {
            $this->_saveErpAccounts($salesRep, $data);
        }
    }

    /**
     * 
     * @param Epicor_SalesRep_Model_Account $salesRep
     * @param type $data
     */
    protected function _saveErpAccounts($salesRep, $data)
    {
        $salesReps = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['erpaccounts']));
        // load current and check if any need to be removed

        $salesRep->setErpAccounts($salesReps);
    }

    public function listsalesrepaccountsAction()
    {
        if ($this->getRequest()->get('grid')) {
            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('epicor_salesrep/adminhtml_customer_salesrep_popup_grid')->toHtml()
            );
        } else {
            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('epicor_salesrep/adminhtml_customer_salesrep_popup')->toHtml()
            );
        }
    }

    public function salesrepsAction()
    {
        $this->_initSalesRepAccount();
        $this->loadLayout();
        $this->getLayout()->getBlock('salesrep_grid')
                ->setSelected($this->getRequest()->getPost('salesreps', null));
        $this->renderLayout();
    }

    public function hierarchyAction()
    {
        $this->_initSalesRepAccount();
        $this->loadLayout();
        $this->getLayout()->getBlock('parents_grid')
                ->setSelected($this->getRequest()->getPost('parents', null));
        $this->getLayout()->getBlock('children_grid')
                ->setSelected($this->getRequest()->getPost('children', null));
        $this->renderLayout();
    }

    public function parentsgridAction()
    {
        $this->_initSalesRepAccount();
        $parents = $this->getRequest()->getParam('parents');
        $this->loadLayout();
        $this->getLayout()->getBlock('parents_grid')->setSelected($parents);
        $this->renderLayout();
    }

    public function childrengridAction()
    {
        $this->_initSalesRepAccount();
        $children = $this->getRequest()->getParam('children');
        $this->loadLayout();
        $this->getLayout()->getBlock('children_grid')->setSelected($children);
        $this->renderLayout();
    }

    public function salesrepsgridAction()
    {
        $this->_initSalesRepAccount();
        $customers = $this->getRequest()->getParam('salesreps');
        $this->loadLayout();
        $this->getLayout()->getBlock('salesrep_grid')->setSelected($customers);
        $this->renderLayout();
    }

    public function erpaccountsAction()
    {
        $this->_initSalesRepAccount();
        $this->loadLayout();
        $this->getLayout()->getBlock('erpaccount_grid')
                ->setSelected($this->getRequest()->getPost('erpaccounts', null));
        $this->renderLayout();
    }

    public function erpaccountsgridAction()
    {
        $this->_initSalesRepAccount();
        $customers = $this->getRequest()->getParam('erpaccounts');
        $this->loadLayout();
        $this->getLayout()->getBlock('erpaccount_grid')->setSelected($customers);
        $this->renderLayout();
    }

    public function pricingrulesAction()
    {
        $this->_initSalesRepAccount();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function pricingrulesgridAction()
    {
        $this->_initSalesRepAccount();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function massAssignToErpAccountsAction()
    {

        $erpAccountsIds = $this->getRequest()->getPost('accounts');
        $salesRepAccountId = $this->getRequest()->getPost('sales_rep_account');

        if (!$salesRepAccountId) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_salesrep')->__('The Sales Rep is required.'));
            $this->_redirect('adminhtml/epicorcomm_customer_erpaccount/index');
            return;
        }

        $erpAccountsExistent = Mage::getModel('epicor_salesrep/erpaccount')->getCollection()->getErpAccountsBySalesRepAccount($salesRepAccountId);

        $erpAccountsRemove = array();
        foreach ($erpAccountsExistent as $erpAccount) {
            $erpAccountsRemove[] = $erpAccount->getErpAccountId();
        }

        $erpAccountsIds = array_diff($erpAccountsIds, $erpAccountsRemove);
        foreach ($erpAccountsIds as $erpAccountId) {
            $model = Mage::getModel('epicor_salesrep/erpaccount');
            $model->setErpAccountId($erpAccountId);
            $model->setSalesRepAccountId($salesRepAccountId)->save();
            $model->save();
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_salesrep')->__('The Sales Rep have been assigned.'));
        $this->_redirect('adminhtml/epicorcomm_customer_erpaccount/index');
    }

    /**
     * 
     * @return Epicor_SalesRep_Model_Account
     */
    private function _initSalesRepAccount()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('epicor_salesrep/account');
        if ($id) {
            $model->load($id);
            if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            }
        }

        Mage::register('salesrep_account', $model);

        return $model;
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id != null) {
            $this->delete($id);
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the Sales Rep Account to delete.'));
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
        $session->addSuccess($helper->__(count($ids) . ' Sales Rep Accounts deleted'));
        $this->_redirect('*/*/');
    }

    private function delete($id, $mass = false)
    {
        try {
            $model = Mage::getModel('epicor_salesrep/account')->load($id);
            if ($model->delete()) {
                if (!$mass) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_comm')->__('The Sales Rep Account has been deleted.'));
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError('Could not delete Sales Rep Account ' . $model->getErpCode());
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

    public function pricingrulespostAction()
    {
        $dataArr = $this->getRequest()->getParams();

        if (isset($dataArr['rule'])) {
            $dataArr['conditions'] = $dataArr['rule']['conditions'];
            unset($dataArr['rule']);
        }

        $data = new Varien_Object($dataArr);
        
        if($data->getName()){
            $rule = Mage::getModel('epicor_salesrep/pricing_rule')->load($data->getId());
            /* @var $rule Epicor_SalesRep_Model_Pricing_Rule */

            unset($dataArr['id']);

            $rule->loadPost($dataArr);

            $rule->setName($data->getName());
            $rule->setSalesRepAccountId($data->getSalesrepAccountId());
            $rule->setFromDate($data->getFromDate());
            $rule->setToDate($data->getToDate());
            $rule->setIsActive($data->getIsActive());
            $rule->setPriority($data->getPriority());
            $rule->setActionOperator($data->getActionOperator());
            $rule->setActionAmount($data->getActionAmount());

            $rule->save();
        }
    }

    public function deletepricingruleAction()
    {
        $dataArr = $this->getRequest()->getParams();

        if (!empty($dataArr['id'])) {

            $rule = Mage::getModel('epicor_salesrep/pricing_rule')->load($dataArr['id']);
            /* @var $rule Epicor_SalesRep_Model_Pricing_Rule */

            if (!$rule->isObjectNew()) {
                $rule->delete();
            }
        }
    }

    /**
     * Export Sales Rep Accounts grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'salesrepaccounts.csv';
        $content = $this->getLayout()->createBlock('epicor_salesrep/adminhtml_customer_salesrep_grid')
                ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export Sales Rep Accounts grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName = 'salesrepaccounts.xml';
        $content = $this->getLayout()->createBlock('epicor_salesrep/adminhtml_customer_salesrep_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

}
