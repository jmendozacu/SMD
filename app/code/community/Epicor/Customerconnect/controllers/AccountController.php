<?php

/**
 * Customer Account controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_AccountController extends Epicor_Customerconnect_Controller_Abstract
{

    /**
     * Index action 
     */
    public function indexAction()
    {
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $erp_account_number = $helper->getErpAccountNumber();
        $message = Mage::getSingleton('customerconnect/message_request_cuad');
        $error = false;
        $messageTypeCheck = $message->getHelper("customerconnect/messaging")->getMessageType('CUAD');
        if ($message->isActive() && $messageTypeCheck) {
            $message->setAccountNumber($erp_account_number)
                    ->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));

            if ($message->sendMessage()) {
                Mage::register('customer_connect_account_details', $message->getResults());

                $accessHelper = Mage::helper('epicor_common/access');
                /* @var $helper Epicor_Common_Helper_Access */
                Mage::register('manage_permissions', $accessHelper->customerHasAccess('Epicor_Customerconnect', 'Account', 'index', 'manage_permissions', 'view'));
            } else {
                $error = true;
                Mage::getSingleton('core/session')->addError($helper->__('Failed to retrieve Account Details'));
            }
        } else {
            $error = true;
            Mage::getSingleton('core/session')->addError($helper->__('Account Details not available'));
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveBillingAddressAction()
    {
        $helper = Mage::helper('customerconnect');
        $data = $this->getRequest()->getPost();
        $error = false;
        if ($data) {

            $form_data = json_decode($data['json_form_data'], true);
            Mage::helper('customerconnect')->addressValidate($form_data, false);
            $old_form_data = json_decode($form_data['old_data'], true);

            unset($form_data['old_data']);

            // add this otherwise the difference check will always be true and always send a message
            $form_data['address_code'] = $old_form_data['address_code'];

            if ($old_form_data != $form_data) {

                $message = Mage::getSingleton('customerconnect/message_request_cuau');
                /* @var $message Epicor_Customerconnect_Model_Message_Request_Cuau */
                $message->addInvoiceAddress($form_data, $old_form_data);
                $message->setAction('update');
                $message->setAddressType('invoice');
                $this->_successMsg = $helper->__('Billing address updated successfully');
                $this->_errorMsg = $helper->__('Failed to update Billing Address');

                $this->sendUpdate($message);
            } else {
                Mage::getSingleton('core/session')->addNotice($helper->__('No changes made to Billing Address'));
                $error = true;
            }
        } else {
            $error = true;
        }

        if ($error) {
            echo json_encode(array('redirect' => Mage::getUrl('customerconnect/account/'), 'type' => 'success'));
        }
    }

    public function saveShippingAddressAction()
    {
        $helper = Mage::helper('customerconnect');
        $data = $this->getRequest()->getPost();

        $error = false;

        if ($data) {

            $form_data = json_decode($data['json_form_data'], true);
            Mage::helper('customerconnect')->addressValidate($form_data, false);
            $old_form_data = json_decode($form_data['old_data'], true);
            unset($form_data['old_data']);

            // add this otherwise the difference check will always be true and always send a message
            if (!isset($form_data['address_code'])) {
                $form_data['address_code'] = $old_form_data['address_code'];
            }

            if ($old_form_data != $form_data) {

                $message = Mage::getSingleton('customerconnect/message_request_cuau');
                /* @var $message Epicor_Customerconnect_Model_Message_Request_Cuau */

                $action = ($old_form_data) ? 'U' : 'A';

                $message->addDeliveryAddress($action, $form_data, $old_form_data);
                $message->setAddressType('delivery');
                if ($action == 'U') {
                    $this->_successMsg = $helper->__('Delivery Address updated successfully');
                    $this->_errorMsg = $helper->__('Failed to update Delivery Address');
                    $message->setAction('update');
                } else {
                    $this->_successMsg = $helper->__('Delivery Address added successfully');
                    $this->_errorMsg = $helper->__('Failed to add Delivery Address');
                    $message->setAction('add');
                }
                $this->sendUpdate($message);
            } else {
                Mage::getSingleton('core/session')->addNotice($helper->__('No Changes Made to Shipping Address'));
                $error = true;
            }
        } else {
            $error = true;
        }

        if ($error) {
            echo json_encode(array('redirect' => Mage::getUrl('customerconnect/account/'), 'type' => 'success'));
        }
    }

    public function deleteShippingAddressAction()
    {
        $helper = Mage::helper('customerconnect');
        $data = $this->getRequest()->getPost();

        $error = false;

        if ($data) {

            $form_data = json_decode($data['json_form_data'], true);

            $message = Mage::getSingleton('customerconnect/message_request_cuau');
            /* @var $message Epicor_Customerconnect_Model_Message_Request_Cuau */

            $message->setAction('delete');
            $message->setAddressType('delivery');
            $message->deleteDeliveryAddress($form_data);

            $this->_successMsg = $helper->__('Shipping Address deleted successfully');
            $this->_errorMsg = $helper->__('Failed to delete Shipping Address');

            $this->sendUpdate($message);
        } else {
            $error = true;
        }

        if ($error) {
            echo json_encode(array('redirect' => Mage::getUrl('customerconnect/account/'), 'type' => 'success'));
        }
    }

    public function syncContactAction()
    {
        $helper = Mage::helper('customerconnect');

        $data = $this->getRequest()->getPost();
        $error = false;

        if ($data) {

            $form_data = json_decode($data['json_form_data'], true);
            $old_form_data = false;
            unset($form_data['old_data']);
            // add this otherwise the difference check will always be true and always send a message
            $form_data['contact_code'] = $old_form_data['contact_code'];
            switch ($form_data["source"]) {
                case $helper::SYNC_OPTION_ONLY_ECC:
                    $form_data['login_id'] = 'true';                        // must be true, or sync will never be web enabled
                    $message = Mage::getSingleton('customerconnect/message_request_cuau');
                    $message->addContact('A', $form_data, $old_form_data);
                    $this->_successMsg = $helper->__('Contact added successfully');
                    $this->_errorMsg = $helper->__('Failed to add Contact');
                    $this->sendUpdate($message);
                    break;
                default:
                    Mage::getSingleton('core/session')->addNotice($helper->__('Sync is not necessary'));
                    $error = true;
                    break;
            }
        } else {
            $error = true;
        }

        if ($error) {
            echo json_encode(array('redirect' => Mage::getUrl('customerconnect/account/'), 'type' => 'success'));
        }
    }

    public function saveContactAction()
    {
        $helper = Mage::helper('customerconnect');

        $data = $this->getRequest()->getPost();

        $error = false;

        if ($data) {

            $form_data = json_decode($data['json_form_data'], true);
            $old_form_data = json_decode($form_data['old_data'], true);


            unset($form_data['old_data']);
            $customerExists = false;
            $form_data['login_id'] = 'false';
            if (isset($form_data['web_enabled'])) {

                $form_data['login_id'] = 'true';
                $customer = Mage::getModel('customer/customer');
                $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                /* @var $customer Epicor_Comm_Model_Customer */

                $customer->loadByEmail($form_data['email_address']);
                if ($customer->getId()) {
                    if ($form_data['ecc_master_shopper'] == 'y') {
                        $customer->setEccMasterShopper(1);
                    } else {
                        $customer->setEccMasterShopper(0);
                    }
                    $customer->getResource()->saveAttribute($customer, 'ecc_master_shopper');
                }
                unset($form_data['web_enabled']);

                if (!isset($old_form_data['email_address']) || $old_form_data['email_address'] != $form_data['email_address']) {
                    $customer = Mage::getModel('customer/customer');
                    $customer->setWebsiteId(Mage::app()->getDefaultStoreView()->getWebsiteId());
                    /* @var $customer Epicor_Comm_Model_Customer */
                    $customer->loadByEmail($form_data['email_address']);
                    if ($customer && !$customer->isObjectNew()) {
                        $customerExists = true;
                    }
                }
            }
            // add this otherwise the difference check will always be true and always send a message
            $form_data['contact_code'] = $old_form_data['contact_code'];

            $access_groups = null;
            if (isset($form_data['access_groups'])) {
                $accessHelper = Mage::helper('epicor_common/access');
                /* @var $helper Epicor_Common_Helper_Access */
                if ($accessHelper->customerHasAccess('Epicor_Customerconnect', 'Account', 'index', 'manage_permissions', 'view')) {
                    $this->updateContactAccessGroups($form_data['contact_code'], $form_data['access_groups']);
                }
                unset($form_data['access_groups']);
            }

            if ($customerExists) {
                Mage::getSingleton('core/session')->addError($helper->__('Contact error: Email address already exists'));
                $error = true;
            } else if ($old_form_data != $form_data) {

                $message = Mage::getSingleton('customerconnect/message_request_cuau');
                /* @var $message Epicor_Customerconnect_Model_Message_Request_Cuau */

                if (empty($old_form_data)) {
                    $action = 'A';
                }else{ 
                    if($old_form_data['source'] === $helper::SYNC_OPTION_ONLY_ECC){
                        $action = 'A';
                    }else{
                        $action = 'U';
                    }
                }
                $message->addContact($action, $form_data, $old_form_data);

                if ($action == 'U') {
                    $this->_successMsg = $helper->__('Contact updated successfully');
                    $this->_errorMsg = $helper->__('Failed to update Contact');
                } else {
                    $this->_successMsg = $helper->__('Contact added successfully');
                    $this->_errorMsg = $helper->__('Failed to add Contact');
                }
                $this->sendUpdate($message);
            } else {
                Mage::getSingleton('core/session')->addNotice($helper->__('No changes made to Contact'));
                $error = true;
            }
        } else {
            $error = true;
        }

        if ($error) {
            echo json_encode(array('redirect' => Mage::getUrl('customerconnect/account/'), 'type' => 'success'));
        }
    }

    /**
     * Updates a contact with the provided access groups
     * 
     * @param string $contact
     * @param array $groupIds
     */
    private function updateContactAccessGroups($contactCode, $groupIds)
    {

        // load the customer by contact code & ERP account ID
        $customerSession = Mage::getSingleton('customer/session');
        $commHelper = Mage::helper('epicor_comm');
        /* @var $commHelper Epicor_Comm_Helper_Data */
        $erpAccount = $commHelper->getErpAccountInfo();
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
        $erpAccountId = $erpAccount->getId();

        $collection = Mage::getModel('customer/customer')->getCollection();
        $collection->addAttributeToFilter('contact_code', $contactCode);
        $collection->addAttributeToFilter('erpaccount_id', $erpAccountId);
        $customer = $collection->getFirstItem();
        /* @var $customer Mage_Customer_Model_Customer */

        if ($customer && !$customer->isObjectNew() && $customer->getId() != $customerSession->getCustomer()->getId()) {
            Mage::getModel('epicor_common/access_group_customer')->updateCustomerAccessGroups($customer->getId(), $groupIds);
        }
    }

    public function deleteContactAction()
    {
        $helper = Mage::helper('customerconnect');
        $data = $this->getRequest()->getPost();

        if ($data) {

            $form_data = json_decode($data['json_form_data'], true);

            $message = Mage::getSingleton('customerconnect/message_request_cuau');
            /* @var $message Epicor_Customerconnect_Model_Message_Request_Cuau */
            if ($form_data['source'] == 0 || $form_data['source'] == 2) {
                $message->deleteContact($form_data);
            }
            if ($form_data['source'] == 1 || $form_data['source'] == 2) {

                $customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getWebsite()->getId());
                /* @var $customer Epicor_Comm_Model_Customer */
                $customer->loadByEmail($form_data['email_address']);
                Mage::register('isSecureArea', true);
                $customer->delete();
                Mage::unregister('isSecureArea');
            }
            $this->_successMsg = $helper->__('Contact deleted successfully');
            $this->_errorMsg = $helper->__('Failed to delete Contact');
            if ($form_data['source'] == 0 || $form_data['source'] == 2) {
                $this->sendUpdate($message);
            } else {
                Mage::getSingleton('core/session')->addSuccess($this->_successMsg);
                echo json_encode(array('redirect' => Mage::getUrl('customerconnect/account/'), 'type' => 'success'));
            }
        } else {
            $error = true;
        }

        if ($error) {
            echo json_encode(array('redirect' => Mage::getUrl('customerconnect/account/'), 'type' => 'success'));
        }
    }

    /**
     * Index action 
     * 
     * @var $message Epicor_Customerconnect_Model_Message_Request_Cuau
     */
    private function sendUpdate($message)
    {
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $erp_account_number = $helper->getErpAccountNumber();
        $messageTypeCheck = $message->getHelper("customerconnect/messaging")->getMessageType('CUAU');

        if ($message->isActive() && $messageTypeCheck) {

            $message->setAccountNumber($erp_account_number)
                    ->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));

            if ($message->sendMessage()) {
                Mage::getSingleton('core/session')->addSuccess($this->_successMsg);
            } else {
                Mage::getSingleton('core/session')->addError($this->_errorMsg . ': ' . $message->getStatusDescription());
            }
        } else {
            Mage::getSingleton('core/session')->addError($helper->__('Account update not available'));
        }

        echo json_encode(array('redirect' => Mage::getUrl('customerconnect/account/'), 'type' => 'success'));
    }

    public function saveErpBillingAddressAction($data = null)
    {

        $form_data = json_decode($this->getRequest()->getParam('json_form_data'), true);

        $commHelper = Mage::helper('epicor_comm');
        /* @var $commHelper Epicor_Comm_Helper_Data */
        $erpAccountInfo = $commHelper->getErpAccountInfo();
        /* @var $erpAccountInfo Epicor_Comm_Model_Customer_Erpaccount */
        $erpCode = $erpAccountInfo->getCompany() . "_" . $erpAccountInfo->getShortCode();

        $config = new Mage_Core_Model_Config();
        $config->saveConfig("Epicor_Comm/save_new_addresses/{$erpCode}", $form_data['new_address_values']);
        $config->saveConfig("Epicor_Comm/save_new_addresses/erp_save_billing_{$erpCode}", $form_data['save_billing']);
        Mage::app()->cleanCache(array('CONFIG', 'LAYOUT_GENERAL_CACHE_TAG'));
    }

    public function saveCustomAddressAllowedAction()
    {
        $customerSession = Mage::getSingleton('customer/session');
        /* @var $customerSession Mage_Customer_Model_Session */
        $customer = $customerSession->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */

        $commHelper = Mage::helper('epicor_comm');
        /* @var $commHelper Epicor_Comm_Helper_Data */
        $erpAccount = $commHelper->getErpAccountInfo();
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */

        $customAddressAllowed = Mage::app()->getRequest()->getParam('customAddressAllowed');
        try {
            $erpAccount->setCustomAddressAllowed($customAddressAllowed)->save();
            $this->getResponse()->setBody(
                    json_encode(
                            array(
                                'redirect' => Mage::getUrl('customerconnect/account/'),
                                'type' => 'success'
                            )
                    )
            );
        } catch (Exception $ex) {
            Mage::log('--- update of erp account failed---');
            Mage::log($ex->getMessage());
            $this->getResponse()->setBody(
                    json_encode(
                            array(
                                'redirect' => Mage::getUrl('customerconnect/account/'),
                                'type' => 'error',
                                'message' => $this->__('Unable to update, please try later')
                            )
                    )
            );
        }
    }

}
