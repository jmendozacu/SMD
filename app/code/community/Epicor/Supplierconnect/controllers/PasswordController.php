<?php

/**
 * Account controller
 *
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_PasswordController extends Epicor_Supplierconnect_Controller_Abstract
{

    /**
     * Index action 
     */
    public function indexAction() {
        $this->loadLayout()->renderLayout();
    }

    public function updateAction() {
        $parms = $this->getRequest()->getParam('login');
        $customerSession = Mage::getSingleton('customer/session');
        $newPassword = $parms['new_password'];
        if ($customerSession->isLoggedIn()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer')->load($customerSession->getId());
            if (!empty($newPassword)) {
                try {
                    $customer->changePassword($newPassword);
                    $customer->sendPasswordReminderEmail();
                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('supplierconnect')->__('Password changed successfully. Confirmation email sent.'), true);
                } catch (Mage_Core_Exception $e) {
                    Mage::log('--- core exception when updating user password in supplierconnect ---');
                    Mage::log($e);
                    Mage::getSingleton('core/session')->addError(
                            Mage::helper('supplierconnect')->__('A core exception error occurred while saving the customer. Password not changed'), true);
                } catch (Exception $e) {
                    Mage::log('--- exception when updating user password in supplierconnect ---');
                    Mage::log($e);
                    Mage::getSingleton('core/session')->addError(
                            Mage::helper('supplierconnect')->__('An error occurred while saving the customer. Password not changed '), true);
                }
            }
        }
        
        $this->_redirect('*/account/index');            // return to supplierconnect dashboard
    }

}

