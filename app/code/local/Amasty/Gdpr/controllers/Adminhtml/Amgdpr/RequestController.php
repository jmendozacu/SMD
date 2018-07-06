<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Adminhtml_Amgdpr_RequestController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('customer/amgdpr/requests');

        $this->_title($this->__('Customer'))
            ->_title($this->__('GDPR'))
            ->_title($this->__('Manage Requests'));

        $this->_addContent(
            $this->getLayout()->createBlock('amgdpr/adminhtml_deleteRequest')
        );

        $this->renderLayout();
    }

    public function approveAction()
    {
        $ids = $this->getRequest()->getParam('ids');

        try {
            /** @var Amasty_Gdpr_Model_AnonymizeModel $anonymizer */
            $anonymizer = Mage::getSingleton('amgdpr/anonymizeModel');

            $action = function ($customerId) use ($anonymizer) {
                $anonymizer->deleteCustomer($customerId);
            };

            $total = $this->processRequests($ids, $action);

            $this->_getSession()->addSuccess(
                $this->__('%d customer(s) has been successfully deleted', $total)
            );
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('An error has occurred: %s', $e->getMessage()));
        }

        $this->_redirect('*/*');
    }

    public function denyAction()
    {
        $requestIds = $this->getRequest()->getParam('ids');

        if (!$requestIds) {
            $this->_getSession()->addError($this->__('Please select at least one request'));
            $this->_redirect('*/*');

            return;
        }

        $this->loadLayout();

        $this->_addContent($this->getLayout()->createBlock('amgdpr/adminhtml_deleteRequest_denyTemplate_edit'));

        $this->renderLayout();
    }

    public function denyPostAction()
    {
        $requests = explode(',', $this->getRequest()->getParam('requests'));
        $comment = $this->getRequest()->getParam('comment');

        try {
            /** @var Amasty_Gdpr_Model_DeleteRequest_Notifier $notifier */
            $notifier = Mage::getSingleton('amgdpr/deleteRequest_notifier');

            $action = function ($customerId) use ($notifier, $comment) {
                $notifier->notify($customerId, $comment);
            };

            $total = $this->processRequests($requests, $action);

            $this->_getSession()->addSuccess($this->__('%s email(s) have been sent.', $total));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('An error has occurred: %s', $e->getMessage()));
        }

        return $this->_redirect('*/*');
    }

    protected function processRequests($ids, Closure $action)
    {
        /** @var Amasty_Gdpr_Model_Resource_DeleteRequest_Collection $requests */
        $requests = Mage::getResourceModel('amgdpr/deleteRequest_collection');

        $requests->addFieldToFilter('id', array('in' => $ids));

        $customerIds = array_unique($requests->getColumnValues('customer_id'));

        foreach ($customerIds as $customerId) {
            $action($customerId);
            $requests->deleteByCustomerId($customerId);
        }

        return count($customerIds);
    }

    /**
     * @return mixed
     * @throws Varien_Exception
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/amgdpr/requests');
    }
}
