<?php

/**
 * Entity register log details renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Advanced_Entity_Register_Renderer_Details
        extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $function = 'get' . $row->getType() . 'Details';
        return $this->$function($row);
    }

    private function getErpAccountDetails($row)
    {
        $erpAccount = Mage::getModel('epicor_comm/customer_erpaccount')->load($row->getEntityId());
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */

        if ($erpAccount->isObjectNew()) {
            $details = 'No longer available';
        } else {
            $details = 'Short Code: ' . $erpAccount->getShortCode();
        }

        return $details;
    }

    private function getErpAddressDetails($row)
    {
        $erpAddress = Mage::getModel('epicor_comm/customer_erpaccount_address')->load($row->getEntityId());
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount_Address */

        if ($erpAddress->isObjectNew()) {
            $details = 'No longer available';
        } else {
            $details = 'ERP Code: ' . $erpAddress->getErpCode();
        }

        return $details;
    }

    private function getRelatedDetails($row)
    {
        return $this->getAlternativeDetails($row, 'related');
    }

    private function getUpSellDetails($row)
    {
        return $this->getAlternativeDetails($row, 'upsell');
    }

    private function getCrossSellDetails($row)
    {
        return $this->getAlternativeDetails($row, 'cross sell');
    }

    private function getAlternativeDetails($row, $type)
    {
        $entity = Mage::getModel('catalog/product')->load($row->getEntityId());
        /* @var $entity Epicor_Comm_Model_Product */

        $child = Mage::getModel('catalog/product')->load($row->getChildId());
        /* @var $child Epicor_Comm_Model_Product */

        if ($entity->isObjectNew() || $child->isObjectNew()) {
            $details = 'No longer available';
        } else {
            $details = 'SKU ' . $entity->getSku() . ': ' . $type . ' link to sku ' . $child->getSku();
        }
        
        return $details;
    }

    private function getCustomerSkuDetails($row)
    {
        $entity = Mage::getModel('epicor_comm/customer_sku')->load($row->getEntityId());
        /* @var $entity Epicor_Comm_Model_Customer_Sku */
        
        if ($entity->isObjectNew()) {
            $details = 'No longer available';
        } else {
            $details = 'Customer SKU ' . $entity->getSku();
        }
        
        return $details;
    }

    private function getCategoryProductDetails($row)
    {
        $entity = Mage::getModel('catalog/product')->load($row->getEntityId());
        /* @var $entity Epicor_Comm_Model_Product */

        $child = Mage::getModel('catalog/category')->load($row->getChildId());
        /* @var $child Epicor_Comm_Model_Category */

        if ($entity->isObjectNew() || $child->isObjectNew()) {
            $details = 'No longer available';
        } else {
            $details = 'Product ' . $entity->getSku() . ' link to category ' . $child->getName();
        }
        
        return $details;
    }

    private function getCategoryDetails($row)
    {
        $entity = Mage::getModel('catalog/category')->load($row->getEntityId());
        /* @var $child Epicor_Comm_Model_Category */

        if ($entity->isObjectNew()) {
            $details = 'No longer available';
        } else {
            $details = 'Category ' . $entity->getName();
        }
        
        return $details;
    }

    private function getProductDetails($row)
    {
        $entity = Mage::getModel('catalog/product')->load($row->getEntityId());
        /* @var $entity Epicor_Comm_Model_Product */

        if ($entity->isObjectNew()) {
            $details = 'No longer available';
        } else {
            $details = 'Product ' . $entity->getSku();
        }
        
        return $details;
    }

    private function getCustomerDetails($row)
    {
        $entity = Mage::getModel('customer/customer')->load($row->getEntityId());
        /* @var $entity Mage_Customer_Model_Customer */

        if ($entity->isObjectNew()) {
            $details = 'No longer available';
        } else {
            $details = 'Customer ' . $entity->getEmail();
        }
        
        return $details;
    }
    
    private function getSupplierDetails($row)
    {
        $entity = Mage::getModel('customer/customer')->load($row->getEntityId());
        /* @var $entity Mage_Customer_Model_Customer */

        if ($entity->isObjectNew()) {
            $details = 'No longer available';
        } else {
            $details = 'Supplier ' . $entity->getEmail();
        }
        
        return $details;
    }

}
