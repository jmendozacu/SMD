<?php

/**
 * List ERP Accounts Form
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Brands_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->_title = 'Brands';
    }

    /**
     * Gets the List for this tab
     *
     * @return boolean
     */
    public function getList()
    {
        if (!$this->list) {
            if (Mage::registry('list')) {
                $this->list = Mage::registry('list');
            } else {
                $this->list = Mage::getModel('epicor_lists/list')->load($this->getRequest()->getParam('id'));
            }
        }
        return $this->list;
    }

    /**
     * Builds List ERP Accounts Form
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Brands_Form
     */
    protected function _prepareForm()
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        $list = Mage::registry('list');
        /* @var $list Epicor_Lists_Model_List */

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('brands_form', array('legend' => $helper->__('Brands')));

        $fieldset->setHeaderBar(
            '<button title="' . $helper->__('Close') . '" type="button" class="scalable" onclick="listBrand.close();"><span><span><span>' . $helper->__('Close') . '</span></span></span></button>'
        );

        $fieldset->addField('brand_post_url', 'hidden', array(
            'name' => 'post_url',
            'value' => $this->getUrl('adminhtml/epicorlists_list/brandpost')
        ));

        $fieldset->addField('brand_delete_url', 'hidden', array(
            'name' => 'delete_url',
            'value' => $this->getUrl('adminhtml/epicorlists_list/branddelete')
        ));

        $fieldset->addField('list_id', 'hidden', array(
            'name' => 'list_id',
            'value' => $this->getList()->getId()
        ));

        $fieldset->addField('brand_id', 'hidden', array(
            'name' => 'brand_id',
        ));

        $fieldset->addField('company', 'text', array(
            'label' => $helper->__('Company *'),
            'required' => false,
            'name' => 'company',
            'class' => 'check-empty' //Handling validation in javascript(ChildrenGrid.js),
        ));

        $fieldset->addField('site', 'text', array(
            'label' => $helper->__('Site'),
            'required' => false,
            'name' => 'site'
        ));

        $fieldset->addField('warehouse', 'text', array(
            'label' => $helper->__('Warehouse'),
            'required' => false,
            'name' => 'warehouse'
        ));

        $fieldset->addField('group', 'text', array(
            'label' => $helper->__('Group'),
            'required' => false,
            'name' => 'group'
        ));

        $fieldset->addField('addSubmit', 'submit', array(
            'value' => $helper->__('Add'),
            'onclick' => "return listBrand.save();",
            'name' => 'addSubmit',
            'class' => 'form-button'
        ));

        $fieldset->addField('updateSubmit', 'submit', array(
            'value' => $helper->__('Update'),
            'onclick' => "return listBrand.save();",
            'name' => 'updateSubmit',
            'class' => 'form-button'
        ));

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
