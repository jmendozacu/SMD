<?php

/**
 * Response SUCO - Upload Supplier Connect Users 
 *
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Common_Adminhtml_Epicorcommon_CustomerController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    public function massAssignAccountAction()
    {
        $data = $this->getRequest()->getPost();

        if ($data) {
            $customers = $data['customer'];
            $accountType = $data['ecc_erp_account_type'];
            $customerField = $data[$accountType . '_field'];
            $accountId = $data[$customerField];

            if (!is_array($customers)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer1(s).'));
            } else {
                try {
                    foreach ($customers as $customerId) {
                        $customer = Mage::getModel('customer/customer')->load($customerId);
                        $customer->setEccErpAccountType($accountType);
                        $customer->setData($customerField, $accountId);
                        $customer->save();
                    }

                    Mage::getSingleton('adminhtml/session')->addSuccess(
                            Mage::helper('adminhtml')->__('Total of %d record(s) were updated.', count($customers))
                    );
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer2(s).'));
        }

        $this->_redirect('adminhtml/customer/index');
    }
    public function fetchAddressAction()
    {
        $result = array(
            'type' => 'error',
            'html' => '',
            'error' => ''
        );
        $data = $this->getRequest()->getPost();
        if ($data) {
            $addressId = $data['id'];
            $customerId  = $data['customerId'];
            $result =  Mage::helper('epicor_common')->customerSelectedAddressById($addressId,$customerId);
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(json_encode($result));
        }
    }  
}