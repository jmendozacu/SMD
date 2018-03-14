<?php
/**
 * Customer Address controller
 *
 * @category   Epicor
 * @package    Epicor_Comm
 */
class Epicor_Comm_Customer_AddressController extends Mage_Core_Controller_Front_Action
{
    
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
     
    /**
     * 
     * Sets the Address as Default
     *      
     */
    public function saveDefaultAddressAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }
        if ($this->getRequest()->isPost()) {
            $customer = $this->_getSession()->getCustomer();
            /* @var $address Mage_Customer_Model_Address */
            $address  = Mage::getModel('customer/address');
            $addressId = $this->getRequest()->getParam('id');
            if ($addressId) {
                $existsAddress = $customer->getAddressById($addressId);
                if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
                    $address->setId($existsAddress->getId());
                }
            }
            try {
                $address->setCustomerId($customer->getId())
                        ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                        ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false))
                        ->save();
                $this->_getSession()->addSuccess($this->__('The address has been saved.'));
                return $this->_redirectSuccess(Mage::getUrl('customer/address/index', array('_secure'=>true)));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save address.'));
            }
        }
        return $this->_redirectError(Mage::getUrl('customer/address/edit', array('id' => $address->getId())));
    }
}
