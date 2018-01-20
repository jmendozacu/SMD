<?php

/**
 * 
 * Access rights controller
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Adminhtml_Epicorcommon_Access_AdminController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    protected $_aclId = 'customer/access/admin';

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('epicor_common/access/admin')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Access Management '), Mage::helper('adminhtml')->__('Administration'));
        return $this;
    }

    public function indexAction() {
        $this->_initAction()->loadLayout()->renderLayout();
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {

                if (isset($data['element_excluded'])) {
                    $this->saveElements($data);
                    if (Mage::app()->useCache('access')) {
                        $cache = Mage::app()->getCacheInstance();
                        /* @var $cache Mage_Core_Model_Cache */
                        $cache->clean(array('EXCLUSIONS'));
                    }
                }

                Mage::dispatchEvent('epicor_common_access_rights_admin_save',array('request' => $this->getRequest()));
                
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/');
            }

            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_common')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

    /**
     * @param array $elementIds
     */
    private function saveElements($data) {
        $elementIds = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['elements']));
        $collection = Mage::getModel('epicor_common/access_element')->getCollection();
        /* @var $collection Epicor_Common_Model_Resource_Access_Element_Collection */
        $collection->addFieldToFilter('excluded', 1);
        
        $existing = array();
        
        // Remove old - only if they're not passed in the data
        
        foreach ($collection->getItems() as $element) {
            if (!in_array($element->getId(), $elementIds)) {
                $element->setExcluded(0);
                $element->save();
            } else {
                $existing[] = $element->getId();
            }
        }

        // Add new - only if they don't already exist
        
        foreach ($elementIds as $elementId) {
            if(!in_array($elementId, $existing)) {
                $model = Mage::getModel('epicor_common/access_element')->load($elementId);
                $model->setExcluded(1);
                $model->save();
            }
        }
    }

    public function excludedelementsAction() {
        $this->loadLayout();
        $elements = $this->getRequest()->getParam('elements');
        
        $this->getLayout()->getBlock('elements.grid')->setSelected($elements);
        $this->renderLayout();
    }

    public function excludedelementsgridAction() {
        $this->loadLayout();
        $elements = $this->getRequest()->getParam('elements');
        $this->getLayout()->getBlock('elements.grid')->setSelected($elements);
        $this->renderLayout();
    }

    public function updateelementsAction() {
        $model = Mage::getResourceModel('epicor_common/access_element');
        /* @var $model Epicor_Common_Model_Resource_Access_Element */
        $model->regenerate();
        Mage::app()->getResponse()->setBody('true');
    }

}