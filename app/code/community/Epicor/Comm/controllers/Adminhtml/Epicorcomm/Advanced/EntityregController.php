<?php

/**
 * Epicor_Comm_Adminhtml_Message_SynController
 * 
 * Controller for Epicor > Messages > Send SYN
 * 
 * @author Gareth.James
 */
class Epicor_Comm_Adminhtml_Epicorcomm_Advanced_EntityregController
        extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    protected function _initAction()
    {
        $this->loadLayout()
                ->_setActiveMenu('epicor_common/advanced/entity_register')
                ->_addBreadcrumb(Mage::helper('epicor_comm')->__('Entity Register'), Mage::helper('epicor_comm')->__('Entity Register'));
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
                ->renderLayout();
    }

    public function markForDeletionAction()
    {
        $ids = (array) $this->getRequest()->getParam('rowid');
        foreach ($ids as $id) {
            $this->delete($id, true);
        }
        $helper = Mage::helper('epicor_comm');
        $session = Mage::getSingleton('adminhtml/session');
        $session->addSuccess($helper->__(count($ids) . ' Uploaded items marked for deletion'));
        $this->_redirect('*/*/');
    }

    private function delete($id, $mass = false)
    {
        $model = Mage::getModel('epicor_comm/entity_register');
        /* @var $model Epicor_Comm_Model_Entity_Register */
        $session = Mage::getSingleton('adminhtml/session');
        /* @var $helper Epicor_Comm_Helper_Data */
        $helper = Mage::helper('epicor_comm');
        if ($id) {
            $model->load($id);
            if ($model->getId()) {
                $model->setToBeDeleted(true);
                if ($model->save()) {
                    if (!$mass) {
                        $session->addSuccess($helper->__('Uploaded data entry marked for deletion'));
                    }
                } else {
                    $session->addError($helper->__('Could not delete Uploaded data entry ' . $id));
                }
            }
        }
    }

}
