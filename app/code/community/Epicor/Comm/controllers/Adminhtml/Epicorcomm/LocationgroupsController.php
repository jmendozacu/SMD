<?php

/**
 * Location Groups admin controller
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 * 
 */
class Epicor_Comm_Adminhtml_Epicorcomm_LocationgroupsController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
                        ->isAllowed('epicor_comm/groups');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('epicor_comm/adminhtml_locations_groups'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $group = Mage::getModel('epicor_comm/location_groupings')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location_Groupings */

        Mage::register('group', $group);
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('epicor_comm/adminhtml_locations_groups_edit'))
                ->_addLeft($this->getLayout()->createBlock('epicor_comm/adminhtml_locations_groups_edit_tabs'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $group = Mage::getModel('epicor_comm/location_groupings')->load($this->getRequest()->getParam('id'));
            /* @var $location Epicor_Comm_Model_Location_Groupings */
           $group->addData($data);
            
            $helper = Mage::helper('epicor_comm/locations');
            /* @var $helper Epicor_Comm_Helper_Locations */
            
            $group->save();
                        
            // Handle Locations Tab
            if (isset($data['links']['locations'])) {
                $positions = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['locations']);
                $locations = array_keys($positions);
                $helper->syncGroupLocations($group->getId(), $locations, $positions);
            }
            
            $session = Mage::getSingleton('adminhtml/session');
            /* @var $helper Epicor_Comm_Helper_Data */
            $session->addSuccess($this->__('Group %s Saved Successfully', $group->getGroupName()));

            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', array('id' => $group->getId()));
            } else {
                $this->_redirect('*/*/');
            }
        } else {
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $this->delete($id);
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $ids = (array) $this->getRequest()->getParam('group_id');
        foreach ($ids as $id) {
            $this->delete($id, true);
        }
        $helper = Mage::helper('epicor_comm');
        $session = Mage::getSingleton('adminhtml/session');
        $session->addSuccess($helper->__(count($ids) . ' Message log entries deleted'));
        $this->_redirect('*/*/');
    }

    public function locationsAction()
    {
        $group = Mage::getModel('epicor_comm/location_groupings')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location_Groupings */

        Mage::register('group', $group);
        $this->loadLayout();
        $this->getLayout()->getBlock('grouplocations_grid')
                ->setSelected($this->getRequest()->getParam('locations'));
        $this->renderLayout();
    }

    public function locationsgridAction()
    {
        $group = Mage::getModel('epicor_comm/location_groupings')->load($this->getRequest()->get('id'));
        /* @var $location Epicor_Comm_Model_Location */

        Mage::register('group', $group);
        $locations = $this->getRequest()->getParam('locations');
        $this->loadLayout();
        $this->getLayout()->getBlock('grouplocations_grid')->setSelected($locations);
        $this->renderLayout();
    }
    
    private function delete($id, $mass = false)
    {
        $model = Mage::getModel('epicor_comm/location_groupings');
        /* @var $model Epicor_Comm_Model_Location */
        $session = Mage::getSingleton('adminhtml/session');
        /* @var $helper Epicor_Comm_Helper_Data */
        $helper = Mage::helper('epicor_comm');
        if ($id) {
            $model->load($id);
            if ($model->getId()) {
                if ($model->delete()) {
                    if (!$mass) {
                        $session->addSuccess($helper->__('Group deleted'));
                    }
                } else {
                    $session->addError($helper->__('Could not delete Location ' . $id));
                }
            }
        }
    }
}
