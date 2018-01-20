<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer register form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Epicor_B2b_Block_Portal_Register extends Mage_Customer_Block_Form_Register
{

    public function __construct()
    {
        parent::__construct();
        $this->setShowAddressFields(Mage::getStoreConfigFlag('epicor_b2b/registration/show_address_fields'));
        
        if(Mage::getStoreConfig('epicor_b2b/registration/reg_options') == 'cnc') {
            $this->setSendAccountToErp(true);
        }
    }

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Create New Customer Account'));
        return parent::_prepareLayout();
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return Mage::getUrl('b2b/portal/registerpost');
    }

    public function getBackUrl()
    {
        return Mage::getUrl('b2b/portal/login');
    }
    
    public function showPreReg()
    {
         if (Mage::getStoreConfig('epicor_b2b/registration/reg_options') == 'prereg' || 
                 Mage::getStoreConfigFlag('epicor_b2b/registration/prereg_active')
                 )
             return true;
         else
             return false;
    }
    
    /**
     * Retrieve form data
     *
     * @return Varien_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $formData = Mage::getSingleton('customer/session')->getCustomerFormData(true);
            $data = new Varien_Object();
            if ($formData) {
                $data->addData($formData);
                $data->setCustomerData(1);
            }
            if (isset($data['region_id'])) {
                $data['region_id'] = (int)$data['region_id'];
            }
            
            if (!isset($data['delivery'])) {
                $data['delivery'] = array();
            }

            if (!isset($data['invoice'])) {
                $data['invoice'] = array();
            }

            if (!isset($data['registered'])) {
                $data['registered'] = array();
            }
            
            $this->setData('form_data', $data);
        }
        
        
        return $data;
    }
    
    public function showPasswordField()
    {
        $result = true;
        if (Mage::getStoreConfig('epicor_b2b/registration/reg_options') =='email_request')
        {
            $result = Mage::getStoreConfigFlag('epicor_b2b/registration/reg_show_password');
        }      
        return $result;
    }
    
    public function renderAddressForm($type)
    {
        $block = $this->getLayout()->createBlock('epicor_common/customer_erpaccount_address');
        //$block->initForm();
        return $block->getAddressHtml($type,array());
    }

    public function showDeliveryAddress(){
        return Mage::getStoreConfigFlag('epicor_b2b/registration/delivery_address');
    }
    public function showInvoiceAddress(){
        return Mage::getStoreConfigFlag('epicor_b2b/registration/invoice_address');
    }
    public function showRegisteredAddress(){
        return Mage::getStoreConfigFlag('epicor_b2b/registration/registered_address');
    }

    public function showDeliveryAddressTelephoneFax(){
        return Mage::getStoreConfigFlag('epicor_b2b/registration/delivery_address_phone_fax');
    }
    public function showInvoiceAddressTelephoneFax(){
        return Mage::getStoreConfigFlag('epicor_b2b/registration/invoice_address_phone_fax');
    }
    public function showRegisteredAddressTelephoneFax(){
        return Mage::getStoreConfigFlag('epicor_b2b/registration/registered_address_phone_fax');
    }
    
    public function displayMobilePhone()
    {
        return Mage::getStoreConfigFlag('customer/address/display_mobile_phone');
    }
    public function displayInstructions()
    {
        return Mage::getStoreConfigFlag('customer/address/display_instructions');
    }
}
