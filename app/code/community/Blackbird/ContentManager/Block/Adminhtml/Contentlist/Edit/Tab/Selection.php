<?php

/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category	Blackbird
 * @package		Blackbird_ContentManager
 * @copyright	Copyright (c) 2014 Blackbird Content Manager (http://black.bird.eu)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version		
 */
class Blackbird_ContentManager_Block_Adminhtml_Contentlist_Edit_Tab_Selection extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareLayout() {
        parent::_prepareLayout();
        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $model = Mage::registry('contentlist_data');
        
        $fieldset = $form->addFieldset('contentlist_selection', array('legend' => Mage::helper('contentmanager')->__('Selection')));

        $fieldset->addField('ct_id', 'select', array(
            'label' => Mage::helper('contentmanager')->__('Content type'),
            'name' => 'ct_id',
            'values' => Mage::getSingleton('contentmanager/widget_contenttype')->toOptionArray(true),
            'note' => Mage::helper('contentmanager')->__('This field allows you to chose the content to display in the list. You can also filter and sort this content. Please save the content list after an update')
        ));

        $fieldset->addField('limit_display', 'text', array(
            'label' => Mage::helper('contentmanager')->__('Limit'),
            'name' => 'limit_display',
            'note' => Mage::helper('contentmanager')->__('Number of contents listed per page. Keep empty to display all contents')
        ));

        $fieldset->addField('order_field', 'select', array(
            'label' => Mage::helper('contentmanager')->__('Order field'),
            'name' => 'order_field',
            'values' => Mage::getSingleton('contentmanager/widget_contenttype_option')->toOptionArray($model->getCtId()),
            'note' => Mage::helper('contentmanager')->__('The listing will be ordered by this field.')
        ));
        
        $fieldset->addField('order_by', 'select', array(
            'label' => Mage::helper('contentmanager')->__('Order direction'),
            'name' => 'order_by',
            'options' => array(
                "ASC" => Mage::helper('contentmanager')->__('Ascending'),
                "DESC" => Mage::helper('contentmanager')->__('Descending'),
            ),
        ));
        $fieldset->addField('pagination', 'select', array(
            'label' => Mage::helper('contentmanager')->__('Pagination'),
            'name' => 'pagination',
            'options' => array(
                0 => Mage::helper('contentmanager')->__('None'),
                1 => Mage::helper('contentmanager')->__('Top'),
                2 => Mage::helper('contentmanager')->__('Bottom'),
                3 => Mage::helper('contentmanager')->__('Both'),
            ),
            'note' => Mage::helper('contentmanager')->__('Position of the pagination')));

        
        if (Mage::getSingleton('adminhtml/session')->getContentListData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getContentListData());
            Mage::getSingleton('adminhtml/session')->setContentListData(null);
        } elseif ($model) {
            $form->setValues($model->getData());
        }


        return parent::_prepareForm();
    }

}
