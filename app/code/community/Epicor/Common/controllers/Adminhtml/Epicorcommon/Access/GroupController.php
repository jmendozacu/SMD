<?php

/**
 * 
 * Access Groups controller
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Adminhtml_Epicorcommon_Access_GroupController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    protected $_aclId = 'customer/access/groups';

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('epicor_common/access/group')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Access Group'), Mage::helper('adminhtml')->__('Access Group'));
        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    private function _loadGroup($id) {

        $model = Mage::getModel('epicor_common/access_group');
        /* @var $model Epicor_Common_Model_Access_Group */

        if ($id) {
            $model->load($id);
            if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_common')->__('Access Group does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('access_group_data', $model);
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id', null);
        $this->_loadGroup($id);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function rightsAction() {
        $id = $this->getRequest()->getParam('id', null);
        $this->_loadGroup($id);
        $this->loadLayout();
        $this->getLayout()->getBlock('rights.grid')->setSelected($this->getRequest()->getPost('rights', null));
        $this->renderLayout();
    }

    public function rightsgridAction() {
        $id = $this->getRequest()->getParam('id', null);
        $this->_loadGroup($id);
        $this->loadLayout();
        $this->getLayout()->getBlock('rights.grid')->setSelected($this->getRequest()->getPost('rights', null));
        $this->renderLayout();
    }
    
    public function saveAction() {

        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('epicor_common/access_group');
            /* @var $model Epicor_Common_Model_Access_Group */
            
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            if (!isset($data['erp_account_id']) || empty($data['erp_account_id']) ) {
                $model->setErpAccountId(null);
            }
            
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setId($id);
                }
                $model->save();

                if (isset($data['customer_in_group'])) {
                    $this->saveCustomers($data, $model);
                }

                if (isset($data['right_in_group'])) {
                    $this->saveRights($data, $model);
                }

                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('epicor_common')->__('Error saving Access Group'));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_common')->__('Access Group was successfully saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($model && $model->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            }

            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_common')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('epicor_common/access_group');
                /* @var $model Epicor_Common_Model_Access_Group */
                
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_common')->__('The Access Group has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/view', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the Access Group to delete.'));
        $this->_redirect('*/*/');
    }

    /**
     * 
     * @param array $customerIds
     * @param Epicor_Common_Model_Access_Group $group
     */
    private function saveCustomers($data, $group) {

        $customerIds = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['customers']));

        $model = Mage::getModel('epicor_common/access_group_customer');
        /* @var $model Epicor_Common_Model_Access_Group_Customer */
        $collection = $model->getCollection();
        $collection->addFilter('group_id', $group->getId());
        $items = $collection->getItems();

        $existing = array();

        // Remove old - only if they're not passed in the data

        foreach ($items as $cus) {
            if (!in_array($cus->getCustomerId(), $customerIds)) {
                $cus->delete();
            } else {
                $existing[] = $cus->getCustomerId();
            }
        }

        // Add new - only if they don't already exist

        foreach ($customerIds as $customerId) {
            if (!in_array($customerId, $existing)) {
                $model = Mage::getModel('epicor_common/access_group_customer');
                /* @var $model Epicor_Common_Model_Access_Group_Customer */
                $model->setCustomerId($customerId);
                $model->setGroupId($group->getId());
                $model->save();
            }
        }
    }

    public function customerAction() {
        $id = $this->getRequest()->getParam('id', null);
        $this->_loadGroup($id);
        $this->loadLayout();
        $this->getLayout()->getBlock('customer.grid')->setSelected($this->getRequest()->getPost('customers', null));
        $this->renderLayout();
    }

    public function customergridAction() {
        $id = $this->getRequest()->getParam('id', null);
        $this->_loadGroup($id);
        $this->loadLayout();
        $this->getLayout()->getBlock('customer.grid')->setSelected($this->getRequest()->getPost('customers', null));
        $this->renderLayout();
    }

    /**
     * 
     * @param array $rightIds
     * @param Epicor_Common_Model_Access_Group $group
     */
    private function saveRights($data, $group) {
        $rightIds = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['rights']));

        $existing = array();

        $model = Mage::getModel('epicor_common/access_group_right');
        /* @var $model Epicor_Common_Model_Access_Group_Right */
        $collection = $model->getCollection();
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
    
}
