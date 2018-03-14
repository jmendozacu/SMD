<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Adminhtml_Files_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $helper = $this->_getHelper();
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('general_form_legend', array('legend' => $helper->__('Files')));

        $categories = Mage::getSingleton('mageworx_downloads/categories')->getCategoriesList();

        $fieldset->addField(
            'category_id', 'select', array(
                'label' => $helper->__('Category'),
                'name' => 'general[category_id]',
                'values' => $categories,
                'required' => true
            )
        );

        if (Mage::app()->getRequest()->getActionName() != 'multiupload') {
            $fieldset->addField(
                'name', 'text', array(
                    'label' => $helper->__('Name'),
                    'name' => 'general[name]',
                    'index' => 'name',
                    'required' => true
                )
            );
        }

        $fieldset->addField(
            'file_description', 'textarea', array(
                'label' => $helper->__('Description'),
                'name' => 'general[file_description]',
                'index' => 'file_description',
            )
        );

        $fieldset->addField(
            'downloads_limit', 'text', array(
                'label' => $helper->__('Downloads Limit'),
                'name' => 'general[downloads_limit]',
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_ids', 'multiselect', array(
                    'label' => $helper->__('Stores'),
                    'name' => 'general[store_ids]',
                    'required' => true,
                    'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                )
            );
        } else {
            $fieldset->addField(
                'store_id', 'hidden', array(
                    'name' => 'general[store_ids]',
                    'value' => Mage::app()->getStore(true)->getId(),
                )
            );
        }

        $customerGroups = $this->_getCustomerGroups();
        if ($customerGroups) {
            $fieldset->addField(
                'customer_groups', 'multiselect', array(
                    'label' => $helper->__('Customer Groups'),
                    'name' => 'general[customer_groups][]',
                    'values' => $customerGroups,
                )
            );
        }

        $fileId = Mage::app()->getRequest()->getParam('id');

        if (Mage::app()->getRequest()->getActionName() == 'multiupload') {
            $multiUpload = array(
                'label' => $helper->__('Multi Upload'),
                'name' => 'general[multiupload]',
                'index' => 'multiupload',
                'values' => $fileId ? $fileId : uniqid()
            );

            $class = Mage::getConfig()->getGroupedClassName('model', 'mageworx_downloads/form_element_multiupload');

            $fieldset->addType('multiupload', $class);
            $form->addType('multiupload', $class);
            $fieldset->addField('multiupload', 'multiupload', $multiUpload);
        } else {
            $fileConf = array('label' => $helper->__('File'), 'name' => 'file');
            if ($fileId) {

                $url = $this->getUrl('*/*/download', array('id' => $fileId));
                $fileConf['after_element_html'] =
                    '<p class="nm"><small><a href="' . $url . '">' . $helper->__('Download') . '</a></small></p>';
            }

            $fieldset->addField('file', 'file', $fileConf);

            $noticeMessage = $this->__('When uploading video URL embedded video code is required');
            $fieldset->addField(
                'url', 'text', array(
                    'label' => $helper->__('URL'),
                    'name' => 'general[url]',
                    'index' => 'url',
                    'after_element_html' => '<p class="note"><span>' . $noticeMessage . '</span></p>'
                )
            );

            $fieldset->addField(
                'embed_code', 'textarea', array(
                    'label' => $helper->__('Embedded Video Code'),
                    'name' => 'general[embed_code]',
                    'required' => false
                )
            );
        }

        $fieldset->addField(
            'is_attach', 'select', array(
                'label' => $helper->__('Add file in "New Order" email.'),
                'name' => 'general[is_attach]',
                'index' => 'is_attach',
                'values' => $helper->getAttachArray()
            )
        );

        $fieldset->addField(
            'is_active', 'select', array(
                'label' => $helper->__('Status'),
                'name' => 'general[is_active]',
                'index' => 'is_active',
                'values' => $helper->getStatusArray()
            )
        );

        $session = Mage::getSingleton('adminhtml/session');
        if ($data = $session->getData('downloads_data')) {
            $form->setValues($data['general']);
        } elseif (Mage::registry('downloads_data')) {
            $form->setValues(Mage::registry('downloads_data')->getData());
        }
        $this->setForm($form);

        return $this;
    }

    protected function _getCustomerGroups()
    {
        $result = array();
        $customerGroups = Mage::getSingleton('customer/group')->getCollection()->getItems();
        if ($customerGroups) {
            foreach ($customerGroups as $item) {
                $result[] = array(
                    'value' => $item->getData('customer_group_id'),
                    'label' => $item->getData('customer_group_code')
                );
            }
        }
        return $result;
    }

    protected function _getHelper()
    {
        return Mage::helper('mageworx_downloads');
    }

}