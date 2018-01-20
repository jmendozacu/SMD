<?php

/**
 * 
 * Customer Access Groups management controller
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Controller_Access_Management_Abstract extends Mage_Core_Controller_Front_Action {

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch() {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this) ||
                !Mage::getStoreConfigFlag('epicor_common/accessrights/active')) {
            $this->setFlag('', 'no-dispatch', true);
            $this->_redirectUrl($this->_getRefererUrl());
        }
    }

    public function indexAction() {
        $this->loadErpAccount();
        $this->loadLayout()->renderLayout();
    }

    public function addgroupAction() {
        $this->loadErpAccount();
        $this->loadLayout()->renderLayout();
    }

    public function editgroupAction() {
        $id = $this->getRequest()->getParam('id');
        $this->_loadGroup($id);
        $this->loadLayout()->renderLayout();
    }

    public function savegroupAction() {
        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('id');
            $group = $this->_loadGroup($id);

            try {

                $erpAccount = Mage::registry('access_erp_account');
                if (!Mage::registry('access_group_global')) {
                    $group->setErpAccountId($erpAccount->getId());
                    $group->setEntityName($data['name']);
                    
                    if ($erpAccount->isTypeSupplier()) {
                        $group->setType('supplier');
                    } else {
                        $group->setType('customer');
                    }

                    $group->save();

                    if (!$group->getId()) {
                        Mage::throwException(Mage::helper('epicor_common')->__('Error saving Access Group'));
                    }

                    $this->saveRights($data, $group);
                }

                $this->saveContacts($data, $group, $erpAccount);

                Mage::getSingleton('core/session')->addSuccess(Mage::helper('epicor_common')->__('Access Group was successfully saved.'));
                Mage::getSingleton('core/session')->setFormData(false);

                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/editgroup', array('id' => $group->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                if ($group && $group->getId()) {
                    $this->_redirect('*/*/editgroup', array('id' => $group->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            }
        }
    }

    /**
     * Loads the erp account for this customer
     * 
     * @return Epicor_Comm_Model_Customer_Erpaccount
     */
    protected function loadErpAccount() {

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */
        
        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */
        
        if ($customer->isSupplier()) {
            $erpAccount = $helper->getErpAccountInfo(null, 'supplier');
        } else {
            $erpAccount = $helper->getErpAccountInfo();
        }
        
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */

        Mage::register('access_erp_account', $erpAccount);

        return $erpAccount;
    }

    /**
     * Saves contacts to the group
     * 
     * @param array $data
     * @param Epicor_Common_Model_Access_Group $group
     * @param Epicor_Comm_Model_Customer_Erpaccount $erpAccount
     */
    private function saveContacts($data, $group, $erpAccount) {

        $contacts = isset($data['contacts']) ? $data['contacts'] : array();
        $customer = Mage::getSingleton('customer/session')->getCustomer()->getId();

        $erpContacts = $this->_loadContacts($erpAccount);

        $model = Mage::getModel('epicor_common/access_group_customer');
        /* @var $model Epicor_Common_Model_Access_Group_Customer */

        $collection = $model->getCollection();
        /* @var $model Epicor_Common_Model_Access_Group_Customer */
        $collection->addFilter('group_id', $group->getId());
        $items = $collection->getItems();

        $existing = array();

        // Remove old - only if they're not passed in the data

        foreach ($items as $cus) {
            if (!in_array($cus->getCustomerId(), $contacts) && in_array($cus->getCustomerId(), $erpContacts) && $cus->getCustomerId() != $customer) {
                $cus->delete();
            } else {
                $existing[] = $cus->getCustomerId();
            }
        }

        // Add new - only if they don't already exist

        foreach ($contacts as $customerId) {
            if (!in_array($customerId, $existing) && in_array($customerId, $erpContacts) && $customerId != $customer) {
                $model = Mage::getModel('epicor_common/access_group_customer');
                /* @var $model Epicor_Common_Model_Access_Group_Customer */
                $model->setCustomerId($customerId);
                $model->setGroupId($group->getId());
                $model->save();
            }
        }
    }

    /**
     * Loads all the ids of contacts for the ERP Account
     * 
     * @param Epicor_Comm_Model_Customer_Erpaccount $erpAccount
     * 
     * @return array
     */
    private function _loadContacts($erpAccount) {
        $collection = Mage::getResourceModel('customer/customer_collection');
        /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */

        if ($erpAccount->isTypeSupplier()) {
            $collection->addAttributeToSelect('supplier_erpaccount_id');
            $collection->addAttributeToFilter('supplier_erpaccount_id', $erpAccount->getId());
        } else if ($erpAccount->isTypeCustomer()) {
            $collection->addAttributeToSelect('erpaccount_id');
            $collection->addAttributeToFilter('erpaccount_id', $erpAccount->getId());
        }

        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection->addFieldToFilter('entity_id', array('neq' => $customerId));

        return $collection->getAllIds();
    }

    /**
     * Saves rights to the group
     * 
     * @param array $data
     * @param Epicor_Common_Model_Access_Group $group
     */
    private function saveRights($data, $group) {
        $rightIds = isset($data['rights']) ? $data['rights'] : array();

        $existing = array();

        $model = Mage::getModel('epicor_common/access_group_right');
        /* @var $model Epicor_Common_Model_Access_Group_Right */

        $collection = $model->getCollection();
        /* @var $collection Epicor_Common_Model_Resource_Access_Group_Right_Collection */

        $collection->addFilter('group_id', $group->getId());
        $items = $collection->getItems();

        // Remove old - only if they're not passed in the data

        foreach ($items as $right) {
            if (!in_array($right->getRightId(), $rightIds)) {
                $right->delete();
            } else {
                $existing[] = $right->getRightId();
            }
        }

        // Add new - only if they don't already exist

        foreach ($rightIds as $rightId) {
            if (!in_array($rightId, $existing)) {
                $model = Mage::getModel('epicor_common/access_group_right');
                /* @var $model Epicor_Common_Model_Access_Group_Right */
                $model->setRightId($rightId);
                $model->setGroupId($group->getId());
                $model->save();
            }
        }

        $group->clearCache();
    }

    /**
     * Loads group by ID
     * 
     * @param integer $id
     * 
     * @return Epicor_Common_Model_Access_Group
     */
    private function _loadGroup($id) {
        $model = Mage::getModel('epicor_common/access_group');
        /* @var $model Epicor_Common_Model_Access_Group */

        $erpAccount = $this->loadErpAccount();
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */

        if ($id) {
            $model->load($id);
        }

        if (!$model->getId()) {
            $global = false;
            if (!empty($id)) {
                Mage::getSingleton('core/session')->addError(Mage::helper('epicor_common')->__('Access Group does not exist'));
                $this->_redirect('*/*/');
            } else {
                $model = Mage::getModel('epicor_common/access_group');
                /* @var $model Epicor_Common_Model_Access_Group */
            }
        } else {
            if ($model->getErpAccountId() && $model->getErpAccountId() != $erpAccount->getId()) {
                Mage::getSingleton('core/session')->addError(Mage::helper('epicor_common')->__('Access Group does not exist'));
                $this->_redirect('*/*/');
            }

            $global = ($model->getErpAccountId()) ? false : true;
        }

        Mage::register('access_group', $model);
        Mage::register('access_group_global', $global);

        return $model;
    }

}
