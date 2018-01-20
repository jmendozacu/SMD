<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Grid
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('name');
    }

    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();


        $this->getMassactionBlock()->addItem('assign_account_type', array(
            'label' => Mage::helper('customer')->__('Assign an Account'),
            'url' => $this->getUrl('adminhtml/epicorcommon_customer/massAssignAccount'),
            'additional' => array(
                'ecc_erp_account_type' => array(
                    'name' => 'ecc_erp_account_type',
                    'type' => 'account_selector',
                    'renderer' => array(
                        'type' => 'account_selector',
                        'class' => 'Epicor_Common_Block_Adminhtml_Form_Element_Erpaccounttype'
                    ),
                    'label' => Mage::helper('customer')->__('Assign Account'),
                )
            )
        ));

        $this->getMassactionBlock()->addItem('reset_password', array(
            'label' => Mage::helper('epicor_comm')->__('Reset Password - Set Value'),
            'url' => $this->getUrl('adminhtml/epicorcomm_customer/massResetPassword'),
            'additional' => array(
                'visibility' => array(
                    'name' => 'password',
                    'type' => 'text',
                    'label' => Mage::helper('epicor_comm')->__('New Password'),
                )
            )
        ));

        $this->getMassactionBlock()->addItem('reset_random_password', array(
            'label' => Mage::helper('epicor_comm')->__('Reset Password - Randomly Generated'),
            'url' => $this->getUrl('adminhtml/epicorcomm_customer/massResetPassword'),
        ));
           $this->getMassactionBlock()->addItem('set_shopper', array(
            'label' => Mage::helper('epicor_comm')->__('Set Master Shopper'),
            'url' => $this->getUrl('adminhtml/epicorcomm_customer/massSetShopper'),
        ));
        $this->getMassactionBlock()->addItem('revoke_shopper', array(
            'label' => Mage::helper('epicor_comm')->__('Revoke Master Shopper'),
            'url' => $this->getUrl('adminhtml/epicorcomm_customer/massRevokeShopper'),
        ));


        return $this;
    }

    protected function _prepareCollection()
    {

        $collection = Mage::getResourceModel('customer/customer_collection');
        /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        $erpaccountTable = $collection->getTable('epicor_comm/customer_erpaccount');
        $salesRepTable = $collection->getTable('epicor_salesrep/account');

        $collection->addNameToSelect();
        $collection->addAttributeToSelect('email');
        $collection->addAttributeToSelect('ecc_master_shopper');
        $collection->addAttributeToSelect('created_at');
        $collection->addAttributeToSelect('group_id');
        $collection->addAttributeToSelect('previous_erpaccount');
        $collection->addAttributeToSelect('erpaccount_id', 'left');
        $collection->addAttributeToSelect('sales_rep_account_id', 'left');
        $collection->addAttributeToSelect('ecc_erp_account_type', 'left');
        $collection->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left');
        $collection->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left');
        $collection->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left');
        $collection->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left');
        $collection->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');
        $collection->joinTable(array('cc' => $erpaccountTable), 'entity_id=erpaccount_id', array('customer_erp_code' => 'erp_code', 'customer_company' => 'company', 'customer_short_code' => 'short_code'), null, 'left');

//        if (Mage::helper('epicor_comm')->isModuleEnabled('Epicor_Supplierconnect')) {
        // if supplierconnect is enabled, then join the erpaccountinfo on the 
        $collection->addAttributeToSelect('supplier_erpaccount_id', 'left');
        $collection->joinTable(array('ss' => $erpaccountTable), 'entity_id=supplier_erpaccount_id', array('supplier_erp_code' => 'erp_code', 'supplier_company' => 'company', 'supplier_short_code' => 'short_code'), null, 'left');
        $collection->joinTable(array('sr' => $salesRepTable), 'id=sales_rep_account_id', array('sales_rep_id' => 'sales_rep_id'), null, 'left');
        $collection->addExpressionAttributeToSelect('joined_company', "IF(cc.company IS NOT NULL, cc.company, IF(ss.company IS NOT NULL, ss.company, ''))", 'erpaccount_id');
        $collection->addExpressionAttributeToSelect('joined_short_code', "IF(sr.sales_rep_id IS NOT NULL, sr.sales_rep_id, IF(cc.short_code IS NOT NULL, cc.short_code, IF(ss.short_code IS NOT NULL, ss.short_code, '')))", 'erpaccount_id');
        $collection->addExpressionAttributeToSelect('erp_account_type', "IF(cc.erp_code IS NOT NULL, 'Customer', IF(ss.erp_code IS NOT NULL, 'Supplier', IF(at_sales_rep_account_id.value IS NOT NULL, 'Sales Rep', 'Guest')))", 'erpaccount_id');
//        } else {
//            $collection->addExpressionAttributeToSelect('erp_account_type', "IF(cc.erp_code IS NOT NULL, 'Customer', IF(sales_rep_account_id IS NOT NULL, 'Sales Rep', IF(at_sales_rep_account_id.value IS NOT NULL, 'Sales Rep', 'Guest')))", 'erpaccount_id');
//        }
        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();

            $this->addColumnAfter('ecc_master_shopper', array(
            'header'    => Mage::helper('customer')->__('Master Shopper'),
           // 'type'      => 'text',
            'align'     => 'center',
            'index'     => 'ecc_master_shopper',
                'type'=>'options',
             'options' => array('1' => 'Yes', '0' => 'No')
        ), 'group');
            
        $this->addColumnAfter('customer_company', array(
            'header' => Mage::helper('epicor_comm')->__('Company'),
            'index' => 'customer_company',
            'width' => '90',
                ), 'group');
        $this->addColumnAfter('customer_short_code', array(
            'header' => Mage::helper('epicor_comm')->__('Short Code'),
            'index' => 'customer_short_code',
            'width' => '90',
                ), 'customer_company');



        if (Mage::helper('epicor_comm')->isModuleEnabled('Epicor_Supplierconnect')) {
            $this->getColumn('customer_company')->setIndex('joined_company');
            $this->getColumn('customer_short_code')->setIndex('joined_short_code');
        }

        $this->addColumnAfter('previous_erpaccount', array(
            'header' => Mage::helper('epicor_comm')->__('Previous'),
            'index' => 'previous_erpaccount',
                ), 'customer_short_code');

        $this->addColumnAfter('erp_account_type', array(
            'header' => Mage::helper('epicor_comm')->__('Account Type'),
            'index' => 'erp_account_type',
            'filter_index' => 'ecc_erp_account_type',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Customer_Grid_Renderer_Accounttype(),
            'type'      => 'options',
            'options' => Mage::helper('epicor_common/account_selector')->getAccountTypeNames(),   
            ), 'customer_short_code');

        $this->removeColumn('entity_id');
        $this->sortColumnsByOrder();
        return $this;
    }

}
