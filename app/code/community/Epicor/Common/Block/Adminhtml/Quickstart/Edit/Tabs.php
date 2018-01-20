<?php
/**
 * Created by PhpStorm.
 * User: lguerra
 * Date: 12/2/14
 * Time: 2:45 PM
 */

class Epicor_Common_Block_Adminhtml_Quickstart_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct()
    {
        parent::__construct();
        $this->setId('form_tabs');
        $this->setDestElementId('edit_form'); // this should be same as the form id define above
        $this->setTitle(Mage::helper('epicor_common')->__('Quick Start'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label'     => Mage::helper('epicor_common')->__('General'),
            'title'     => Mage::helper('epicor_common')->__('General'),
            'content'   => $this->getLayout()->createBlock('epicor_common/adminhtml_quickstart_edit_tab_general_form')->toHtml()
        ));

        $this->addTab('customer', array(
            'label'     => Mage::helper('epicor_common')->__('Customer Settings'),
            'title'     => Mage::helper('epicor_common')->__('Customer Settings'),
            'url'       => $this->getUrl('*/*/customer_settings'),
            'class'     => 'ajax',
        ));

        $this->addTab('products_configurator', array(
            'label'     => Mage::helper('epicor_common')->__('Products/Configurator Settings'),
            'title'     => Mage::helper('epicor_common')->__('Products/Configurator Settings'),
            'url'       => $this->getUrl('*/*/products_configurator_settings'),
            'class'     => 'ajax',
            //'content'   => $this->getLayout()->createBlock('epicor_common/adminhtml_quickstart_edit_tab_productsconfigurator_form')->toHtml()
        ));

        $this->addTab('checkout', array(
            'label'     => Mage::helper('epicor_common')->__('Checkout Settings'),
            'title'     => Mage::helper('epicor_common')->__('Checkout Settings'),
            'url'       => $this->getUrl('*/*/checkout_settings'),
            'class'     => 'ajax',
        ));

        $this->addTab('b2b', array(
            'label'     => Mage::helper('epicor_common')->__('B2B Settings'),
            'title'     => Mage::helper('epicor_common')->__('B2B Settings'),
            'url'       => $this->getUrl('*/*/b2b_settings'),
            'class'     => 'ajax',
        ));

        return parent::_beforeToHtml();
    }
} 