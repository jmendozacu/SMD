<?php

/**
 * 
 * Access rights controller
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Adminhtml_Epicorcommon_Access_RightController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    private $_selected = array();
    
    protected $_aclId = 'customer/access/rights';

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('epicor_common/access/right')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Access Right'), Mage::helper('adminhtml')->__('Access Right'));
        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id', null);
        $this->_loadRight($id);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function groupsAction() {
        $id = $this->getRequest()->getParam('id', null);
        $this->_loadRight($id);
        $this->loadLayout();
        $this->getLayout()->getBlock('groups.grid')->setSelected($this->getRequest()->getPost('groups', null));
        $this->renderLayout();
    }

    public function groupsgridAction() {
        $id = $this->getRequest()->getParam('id', null);
        $this->_loadRight($id);
        $this->loadLayout();
        
        $this->getLayout()->getBlock('groups.grid')->setSelected($this->getRequest()->getPost('groups', null));
        $this->renderLayout();
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('epicor_common/access_right');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setEntityId($id);
                }
                $model->save();

                if (isset($data['group_in_right'])) {
                    $this->saveGroups($data, $model);
                }

                if (isset($data['element_in_right'])) {
                    $this->saveElements($data, $model);
                }

                if (!$model->getEntityId()) {
                    Mage::throwException(Mage::helper('epicor_common')->__('Error saving Access Right'));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_common')->__('Access Right was successfully saved.'));
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

    private function saveGroups($data, $right) {
        $groupIds = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['groups']));

        // Remove old - only if they're not passed in the data

        $model = Mage::getModel('epicor_common/access_group_right');
        $collection = $model->getCollection();
        $collection->addFilter('right_id', $right->getId());
        $items = $collection->getItems();
        //delete existing.
        foreach ($items as $group) {
            if (!in_array($group->getGroupId(), $groupIds)) {
                $group->delete();
            } else {
                $existing[] = $group->getGroupId();
            }
        }

        // Add new - only if they don't already exist

        foreach ($groupIds as $groupId) {
            if (!in_array($groupId, $existing)) {
                $model = Mage::getModel('epicor_common/access_group_right');
                $model->setRightId($right->getId());
                $model->setGroupId($groupId);
                $model->save();
                Mage::getModel('epicor_common/access_group')->clearCache($groupId);
            }
        }
    }

    /**
     * @param array $elementIds
     * @param Epicor_Common_Model_Access_Right $right
     */
    private function saveElements($data, $right) {
        $elementIds = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['elements']));

        // Remove old - only if they're not passed in the data
        $existing = array();

        $model = Mage::getModel('epicor_common/access_right_element');
        $collection = $model->getCollection();
        $collection->addFilter('right_id', $right->getId());
        $items = $collection->getItems();
        //delete existing.
        foreach ($items as $element) {
            if (!in_array($element->getElementId(), $elementIds)) {
                $element->delete();
            } else {
                $existing[] = $element->getElementId();
            }
        }

        // Add new - only if they don't already exist

        foreach ($elementIds as $elementId) {
            if (!in_array($elementId, $existing)) {
                $model = Mage::getModel('epicor_common/access_right_element');
                $model->setRightId($right->getId());
                $model->setElementId($elementId);
                $model->save();
            }
        }
        
        $right->clearGroupsCache();
    }

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('epicor_common/access_right');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_common')->__('The Access Right has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/view', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the Access Right to delete.'));
        $this->_redirect('*/*/');
    }

    private function _loadRight($id) {

        $model = Mage::getModel('epicor_common/access_right');
        if ($id) {
            $model->load($id);
            if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_common')->__('Access Right does not exist'));
                $this->_redirect('*/*/');
            }
        }

        Mage::register('access_right_data', $model);
    }

    public function elementsAction() {
        $id = $this->getRequest()->getParam('id', null);
        $this->_loadRight($id);
        $this->loadLayout();
        $this->getLayout()->getBlock('elements.grid')->setSelected($this->getRequest()->getPost('elements', null));
        $this->renderLayout();
    }

    public function elementsgridAction() {
        $id = $this->getRequest()->getParam('id', null);
        $this->_loadRight($id);
        $this->loadLayout();
        $this->getLayout()->getBlock('elements.grid')->setSelected($this->getRequest()->getPost('elements', null));
        $this->renderLayout();
    }

    public function updateelementsAction() {
        $model = Mage::getResourceModel('epicor_common/access_element');
        /* @var $model Epicor_Common_Model_Resource_Access_Element */
        $model->regenerate();
        Mage::app()->getResponse()->setBody('true');
    }

}