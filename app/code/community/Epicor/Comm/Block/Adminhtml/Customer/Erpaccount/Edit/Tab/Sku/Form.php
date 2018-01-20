<?php

/**
 * Description of Form
 *
 * @author Guillermo.Garza
 */
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Sku_Form extends Mage_Adminhtml_Block_Widget_Form
{
    
    public function __construct($attributes = array()) {
        parent::__construct($attributes);
        $this->setId('customer_sku_edit_form');
    }
    
    public function getErpCustomer() {
        if (!$this->_erp_customer) {
            $this->_erp_customer = Mage::registry('customer_erp_account');
        }
        return $this->_erp_customer;
    }
    
    protected function _prepareForm()
    {
        
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('customer_sku_form', array('legend' => Mage::helper('epicor_comm')->__('SKU')));
        $fieldset->setHeaderBar(
                '<button title="'.Mage::helper('epicor_comm')->__('Close').'" type="button" class="scalable" onclick="customerSku.close();"><span><span><span>'.Mage::helper('epicor_comm')->__('Close').'</span></span></span></button>'
                );
        
        $fieldset->addField('customersku_post_url', 'hidden', array(
            'name' => 'customersku_post_url',
            'value' => $this->getUrl('adminhtml/epicorcomm_customer_erpaccount/customerskupost')
        ));
        
        $fieldset->addField('customersku_delete_url', 'hidden', array(
            'name' => 'customersku_delete_url',
            'value' => $this->getUrl('adminhtml/epicorcomm_customer_erpaccount/customerskudelete')
        ));
        
        $fieldset->addField('entity_id', 'hidden', array(
            'name' => 'entity_id',
        ));
        
        $fieldset->addField('customer_group_id', 'hidden', array(
            'name' => 'customer_group_id',
            'value' => $this->getErpCustomer()->getId()
        ));

        $fieldset->addField('product_id','product',array(
            'label' => Mage::helper('epicor_comm')->__('Product'),
            'name' => 'product_id',
            'value' => 'default',
            'required' => true
        ));
        
        $fieldset->addField('sku','text',array(
            'label' => Mage::helper('epicor_comm')->__('Customer SKU'),
            'name' => 'sku',
            'value' => 'default',
            'required' => true
        ));
        $fieldset->addField('description','text',array(
            'label' => Mage::helper('epicor_comm')->__('Description'),
            'name' => 'description',
            'value' => 'default',
            //'required' => true
        ));
        
        $fieldset->addField('updateCustomerSkuSubmit', 'submit', array(
          'value'  => Mage::helper('epicor_comm')->__('Update'),
          'onclick' => "return customerSku.save();",
          'name' => 'updateCustomerSkuSubmit',
          'class' => 'form-button'
        ));
        
        $fieldset->addField('addCustomerSkuSubmit', 'submit', array(
          'value'  => Mage::helper('epicor_comm')->__('Add'),
          'onclick' => "return customerSku.save();",
          'name' => 'addCustomerSkuSubmit',
          'class' => 'form-button'
        ))->setAfterElementHtml("
            <script type=\"text/javascript\">
                createCustomerSku('customer_sku_form','customer_sku_grid');
            </script>");

        $this->setForm($form);
    }
}
