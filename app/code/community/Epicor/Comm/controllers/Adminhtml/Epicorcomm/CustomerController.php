<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CustomerController
 *
 * @author David.Wylie
 */
include_once("Mage/Adminhtml/controllers/CustomerController.php");

class Epicor_Comm_Adminhtml_Epicorcomm_CustomerController extends Mage_Adminhtml_CustomerController
{

    protected function _isAllowed() {
        return true;
    }
    
    public function massResetPasswordAction()
    {
        $customersIds = $this->getRequest()->getParam('customer');
        if (!is_array($customersIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
            try {

                $customer = Mage::getModel('customer/customer');
                /* @var $customer Epicor_Comm_Model_Customer */

                $newPassword = $this->getRequest()->getParam('password');

                if (empty($newPassword)) {
                    $newPassword = $customer->generatePassword();
                }

                foreach ($customersIds as $customerId) {
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    $customer->changePassword($newPassword);
                    $customer->sendPasswordReminderEmail();
                    $customer->save();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Total of %d record(s) were updated.', count($customersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('adminhtml/customer/index');
    }
    
    public function addressesAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function locationsAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->getLayout()->getBlock('customer_locations_wrapper')
                ->setSelected($this->getRequest()->getPost('locations', null));
        $this->renderLayout();
    }

    public function locationsgridAction()
    {
        $this->_initCustomer();
        $customers = $this->getRequest()->getParam('locations');
        $this->loadLayout();
        $this->getLayout()->getBlock('locations_grid')->setSelected($customers);
        $this->renderLayout();
    }
    
     public function massSetShopperAction() {

        $customersIds = $this->getRequest()->getParam('customer');
        if (!is_array($customersIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
            try {

                foreach ($customersIds as $customerId) {
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    $customer->setEccMasterShopper(1);
                    $customer->save();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Total of %d record(s) were updated.', count($customersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('adminhtml/customer/index');
    }

    public function massRevokeShopperAction() {
        $customersIds = $this->getRequest()->getParam('customer');
        if (!is_array($customersIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
            try {
                foreach ($customersIds as $customerId) {
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    $customer->setEccMasterShopper(0);
                    $customer->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Total of %d record(s) were updated.', count($customersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('adminhtml/customer/index');
    }
    
    public function listcustomersAction()
    {
        if ($this->getRequest()->get('grid')) {
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_attribute_grid')->toHtml()
            );
        } else {
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_attribute')->toHtml()
            );
        }
    }

}
