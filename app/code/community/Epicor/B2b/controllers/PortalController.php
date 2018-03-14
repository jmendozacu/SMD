<?php

class Epicor_B2b_PortalController extends Mage_Core_Controller_Front_Action
{

    private $successMessageDisplayed = false;

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    protected function _getRegUrl()
    {
        return 'b2b/portal/register';
    }

    protected function _getLoginUrl()
    {
        return 'b2b/portal/login';
    }

    protected function _getSuccessUrl()
    {
        return '/';
    }

    protected function _getData()
    {
        return $this->getRequest()->getPost();
    }

    protected function _sendEmail($vars, $to, $name)
    {
        if ($vars['region_id'] != '') {
            $vars['state_code'] = Mage::getModel('directory/region')->load($vars['region_id'])->getName();
        } else {
            $vars['state_code'] = $vars['region'];
        }
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $storeId = Mage::app()->getStore()->getId();

        Mage::getModel('core/email_template')
                ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                ->sendTransactional(
                        Mage::getStoreConfig('epicor_b2b/registration/reg_email_template', $storeId), Mage::getStoreConfig('epicor_b2b/registration/reg_email_account', $storeId), $to, $name, $vars
        );

        $translate->setTranslateInline(true);
    }

    protected function _registrationRequestEmail()
    {
        $customerData = $this->_getData();
        $setting = Mage::getStoreConfig('epicor_b2b/registration/reg_email_account');
        $successMsg = Mage::getStoreConfig('epicor_b2b/registration/request_email_success');
        $email = Mage::getStoreConfig('trans_email/ident_' . $setting . '/email');
        $name = Mage::getStoreConfig('trans_email/ident_' . $setting . '/name');
        $customerData['adminname'] = $name;
        $customerData['customer_email'] = $customerData['email'];
        $customerData['street1'] = @$customerData['street'][0];
        $customerData['street2'] = @$customerData['street'][1];
        $customerData['street3'] = @$customerData['street'][2];
        $this->_sendEmail($customerData, $email, $name);
        if(!empty($successMsg)){
            $this->_getSession()->addSuccess($this->__(Mage::helper('epicor_comm')->__($successMsg)));
        }
        $this->_redirect($this->_getLoginUrl());
    }

    protected function _registrationCashEmail()
    {
        if ($this->_createAccount()) {
            //send email requesting account.
            $customerData = $this->_getData();
            $setting = Mage::getStoreConfig('epicor_b2b/registration/reg_email_account');
            $email = Mage::getStoreConfig('trans_email/ident_' . $setting . '/email');
            $name = Mage::getStoreConfig('trans_email/ident_' . $setting . '/name');
            $customerData['adminname'] = $name;
            $customerData['street1'] = @$customerData['street'][0];
            $customerData['street2'] = @$customerData['street'][1];
            $customerData['street3'] = @$customerData['street'][2];
            $customerData['customer_email'] = $customerData['email'];
            $this->_sendEmail($customerData, $email, $name);
            $this->_redirect($this->_getSuccessUrl());
        } else {
            $this->_redirect($this->_getRegUrl());
        }
    }

    protected function _registrationGroupPassword()
    {
        $error = '';
        //find customer group with this password.
        $customerData = $this->_getData();
        $erpAccount = Mage::getModel('epicor_comm/customer_erpaccount')
                ->load($customerData['b2bcompanyreg'], 'pre_reg_password');
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */

        if (!empty($erpAccount) && !$erpAccount->isEmpty()) {
            if ($erpAccount->isValidForStore()) {
                if ($this->_createAccount($erpAccount)) {
                    $this->_redirect($this->_getSuccessUrl());
                } else {
                    $error = 'Error occured while creating account';
                }
            } else {
                $error = 'Pre-Registered Password is not valid for this website';
            }
        } else {
            $error = 'Unknown Pre register password';
        }

        if ($error) {
            $this->_getSession()->addError($error);
            $this->_redirect($this->_getRegUrl());
        }
    }

    protected function _registrationSendCnc()
    {
        //find customer group with this password.
        $customerData = $this->_getData();

        $error = '';

        if ($customerData) {
            try {

                // set registered delivery and invoice email 
                $customerData['registered']['email_address'] = $customerData['email'];
                $customerData['delivery']['email_address'] = $customerData['email'];
                $customerData['invoice']['email_address'] = $customerData['email'];
                $customer = Mage::getModel('customer/customer');
                /* @var $customer Mage_Customer_Model_Customer */

                $customer->setStore(Mage::app()->getStore());
                $customer->setWebsiteId(Mage::app()->getWebsite()->getId());

                $customer->loadByEmail($customerData['email']);
                if (!$customer->getId()) {
                    $cnc = Mage::getModel('epicor_comm/message_request_cnc');
                    /* @var $cnc Epicor_Comm_Model_Message_Request_Cnc */
                    if ($cnc->isActive()) {
                        $erpAccount = Mage::getModel('epicor_comm/customer_erpaccount');
                        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
                        $erpAccount->setName($customerData['company']);
                        $erpAccount->setEmail($customerData['email']);

                        $registeredAddress = Mage::getStoreConfigFlag('epicor_b2b/registration/registered_address');
                        $invoiceAddress = Mage::getStoreConfigFlag('epicor_b2b/registration/invoice_address');
                        $deliveryAddress = Mage::getStoreConfigFlag('epicor_b2b/registration/delivery_address');

                        if ($registeredAddress)
                            $erpAccount->addAddress('registered', 'registered', $customerData['registered']);
                        if ($deliveryAddress)
                            $erpAccount->addAddress('delivery', 'delivery', $customerData['delivery']);
                        if ($invoiceAddress)
                            $erpAccount->addAddress('invoice', 'invoice', $customerData['invoice']);

                        $cnc->setAccount($erpAccount);
                        if ($cnc->sendMessage()) {
                            if ($this->_createAccount($cnc->getAccount())) {
                                $this->_addSuccesMessage();
                            } else {
                                $error = $this->__('Error occured while creating account');
                            }
                        } else {
                            $error = $this->__('Account creation failed. Error - %s', $cnc->getStatusDescriptionText());
                        }
                    } else {
                        $error = $this->__('Account creation failed. Messaging Disabled');
                    }
                } else {
                    $url = Mage::getUrl('customer/account/forgotpassword');
                    $error = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                    $this->_getSession()->setEscapeMessages(false);
                }
            } catch (Exception $e) {
                $error = $this->__('ERP Account creation failed. Error  - %s', $e->getMessage());
            }
        } else {
            $error = $this->__('No data found to save');
        }

        if ($error) {
            $this->_getSession()->setCustomerFormData($customerData);
            $this->_getSession()->addError($error);
            $this->_redirect($this->_getRegUrl());
        } else {
            $this->_redirect($this->_getSuccessUrl());
        }
    }

    public function registerAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect($this->_getSuccessUrl());
            return;
        }

        $this->processlayout();
    }

    public function registerPostAction()
    {
//lookup action.
        $delivery = $this->getRequest()->getPost('delivery');
        if ($delivery) {
            Mage::helper('customerconnect')->addressValidate($delivery);
        }
        $registered = $this->getRequest()->getPost('registered');
        if ($registered) {
            Mage::helper('customerconnect')->addressValidate($registered);
        }
        $invoice = $this->getRequest()->getPost('registered');
        if ($invoice) {
            Mage::helper('customerconnect')->addressValidate($invoice);
        }

        $option = Mage::getStoreConfig('epicor_b2b/registration/reg_options');

        $b2bcompanyreg = $this->getRequest()->getPost('b2bcompanyreg');
        if (!empty($b2bcompanyreg)) {
            $return = $this->_registrationGroupPassword();
        } else {
            switch ($option) {
                case 'email_request':
                    $this->_registrationRequestEmail();
                    break;
                case 'email_cash':
                    $return = $this->_registrationCashEmail();
                    break;
                case 'prereg':
                    $return = $this->_registrationGroupPassword();
                    break;
                case 'cnc':
                    $return = $this->_registrationSendCnc();
                    break;
                default: $return = false;
            }
        }

        //redirect to selected home page after successful registration (user will be logged in)
        if ($this->_getSession()->isLoggedIn()) {
            $successRedirection = Mage::getStoreConfig('epicor_b2b/registration/success_redirection');      // retrieve all cms values possible
            $this->_addSuccesMessage();
            if ($successRedirection != '') {
                $this->_redirect($successRedirection);              // redirect only if specifically selected in admin
            }
        }
    }

    /**
     * Create customer account action
     * 
     * @param Epicor_Comm_Model_Customer_Erpaccount $erpAccount 
     */
    protected function _createAccount($erpAccount = false)
    {
        $session = $this->_getSession();
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($this->getRequest()->isPost()) {
            $errors = array();

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')
                    ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            if ($erpAccount !== false) {
                if ($erpAccount->isTypeSupplier()) {
                    $customer->setSupplierErpaccountId($erpAccount->getId());
                } else {
                    $customer->setErpaccountId($erpAccount->getId());
                }
            }

            $customer->setForceErpAccountGroup(1);
        }
        if ($this->getRequest()->getPost('create_address')) {
            /* @var $address Mage_Customer_Model_Address */
            $address = Mage::getModel('customer/address');
            /* @var $addressForm Mage_Customer_Model_Form */
            $addressForm = Mage::getModel('customer/form');
            $addressForm->setFormCode('customer_register_address')
                    ->setEntity($address);

            $addressData = $addressForm->extractData($this->getRequest(), 'address', false);
            $addressErrors = $addressForm->validateData($addressData);
            if ($addressErrors === true) {
                $address->setId(null)
                        ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                        ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
                $addressForm->compactData($addressData);
                $customer->addAddress($address);

                $addressErrors = $address->validate();
                if (is_array($addressErrors)) {
                    $errors = array_merge($errors, $addressErrors);
                }
            } else {
                $errors = array_merge($errors, $addressErrors);
            }
        }

        try {
            $customerErrors = $customerForm->validateData($customerData);
            if ($customerErrors !== true) {
                $errors = array_merge($customerErrors, $errors);
            } else {
                $customerForm->compactData($customerData);
                $customer->setPassword($this->getRequest()->getPost('password'));
                $customer->setConfirmation($this->getRequest()->getPost('confirmation'));
                $customer->setPasswordConfirmation($this->getRequest()->getPost('confirmation'));
                $customerErrors = $customer->validate();
                if (is_array($customerErrors)) {
                    $errors = array_merge($customerErrors, $errors);
                }
            }

            $validationResult = count($errors) == 0;

            if (true === $validationResult) {
                if($this->getRequest()->getPost('firstname') && $this->getRequest()->getPost('lastname')){
                $name=$this->getRequest()->getPost('firstname').' '.$this->getRequest()->getPost('lastname');
                }else{
                $name=$this->getRequest()->getPost('firstname');
                }
                $registeredData = $this->getRequest()->getPost('registered');
                $formData = array('login_id' => 'true',
                    'name' => $name,
                    'telephone_number' => $registeredData['phone'],
                    'fax_number' => $registeredData['fax_number'],
                    'email_address' => $this->getRequest()->getPost('email')
                    );
                $customer->setTelephoneNumber($formData['telephone_number']);
                $customer->setFaxNumber($formData['fax_number']);
                $customer->save();
                Mage::dispatchEvent('customer_register_success', array('account_controller' => $this, 'customer' => $customer)
                );

                if ($customer->isConfirmationRequired()) {
                    $customer->sendNewAccountEmail(
                            'confirmation', $session->getBeforeAuthUrl(), Mage::app()->getStore()->getId()
                    );
                    $this->_addSuccesMessage();
                    $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                } else {
                    $session->setCustomerAsLoggedIn($customer);

                    // display success message on first entry after account created 
                    $this->_addSuccesMessage();
                }
                //send CUAU to create the login in ERP.
                if(Mage::getStoreConfigFlag('epicor_comm_enabled_messages/cnc_request/send_automate_cnc_request', Mage::app()->getStore()->getId())){
                $oldFormData = false;
                $message = Mage::getSingleton('customerconnect/message_request_cuau');
                $message->addContact('A', $formData, $oldFormData);
                $this->sendUpdate($message,$customer->getErpaccountId(), $customer, $formData);
                }
                return true;
            } else {
                $session->setCustomerFormData($this->getRequest()->getPost());
                if (is_array($errors)) {
                    foreach ($errors as $errorMessage) {
                        $session->addError($errorMessage);
                    }
                } else {
                    $session->addError($this->__('Invalid customer data'));
                }
            }
        } catch (Mage_Core_Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                $url = Mage::getUrl('customer/account/forgotpassword');
                $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                $session->setEscapeMessages(false);
            } else {
                $message = $e->getMessage();
            }
            $session->addError($message);
        } catch (Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
        }
        return false;
    }

    public function errorAction()
    {
        $errormsg = Mage::registry('b2bregerrormsg');
        Mage::getSingleton('core/session')->addError($errormsg);
    }

    private function processlayout()
    {
        if (Mage::getStoreConfigFlag('epicor_b2b/registration/reg_portaltype')) {
            $customHandle = 'USE_PORTAL';
        } else {
            $customHandle = 'USE_ONE_COLUMN';
        }
        $this->loadLayout(null, false);
        $update = $this->getLayout()->getUpdate();
        $update->addHandle($customHandle);
        if (Mage::app()->useCache('layout')) {
            $cacheId = $update->getCacheId() . $customHandle;
            $update->setCacheId($cacheId);

            if (!Mage::app()->loadCache($cacheId)) {
                foreach ($update->getHandles() as $handle) {
                    $update->merge($handle);
                }
                $update->saveCache();
            } else {
                //load updates from cache
                $update->load();
            }
        } else {
//load updates
            $update->load();
        }

        $this->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $this->renderLayout();
    }

    /**
     * Customer login form page
     */
    public function loginAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            if ($this->getRequest()->getParam('access') != 'denied') {
                $this->_redirect($this->_getSuccessUrl());
                return;
            }
        }
        $this->processlayout();
    }

    /**
     * Add the success Account Registration Message if it hasn't been added already
     */
    protected function _addSuccesMessage()
    {
        if ($this->successMessageDisplayed == false) {
            $successMessage = Mage::getStoreConfig('epicor_b2b/registration/success_message');
            if (!empty($successMessage)) {
                $this->_getSession()->addSuccess($this->__(Mage::helper('epicor_comm')->__(Mage::getStoreConfig('epicor_b2b/registration/success_message'))));
            }
        }
        $this->successMessageDisplayed = true;
    }
    
    private function sendUpdate($message, $erpAccountId, $customer) 
    {
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $xmlHelper = Mage::helper('epicor_common/xml');
        /* @var $helper Epicor_Common_Helper_Xml */
        $storeId = Mage::app()->getStore()->getId();
        $erp_account_number = $helper->getErpAccountNumber($erpAccountId, $storeId);
        $messageTypeCheck = $message->getHelper("customerconnect/messaging")->getMessageType('CUAU');

        if ($message->isActive() && $messageTypeCheck) {

            $message->setAccountNumber($erp_account_number)
                    ->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));

            if ($message->sendMessage()) {

                $response = $message->getResponse();
                $contacts = $xmlHelper->varienToArray($response->getCustomer()->getContacts());
                $currentEmail = $customer->getEmail();
                $filteredContact = array_filter($contacts, function ($val) use ($currentEmail) {
                    return $val['email_address'] == $currentEmail;
                });
                if (empty($filteredContact['contact']['contact_code'])) {
                    $customer->setEccCucoPending('1');
                } else {
                    $customer->setContactCode($filteredContact['contact']['contact_code']);
                    $customer->setEccCucoPending('0');
                }
            } else {
                $customer->setEccCucoPending('1');
            }
        } else {
            $customer->setEccCucoPending('1');
        }
        $customer->save();
    }

}
