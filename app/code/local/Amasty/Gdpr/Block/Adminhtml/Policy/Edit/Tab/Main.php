<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Adminhtml_Policy_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $storeId = $this->getRequest()->getParam('store');


        /** @var Amasty_Gdpr_Model_PrivacyPolicy $model */
        $model = Mage::registry('current_policy');
        $content = null;

        if ($storeId) {
            $content = Mage::getResourceModel('amgdpr/privacyPolicyContent_collection')
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('policy_id', $model->getData('id'))
                ->getFirstItem();
        }

        $useDefault = !$content || !$content->getId();

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $form->setHtmlIdPrefix('amgdpr_');

        $fieldset = $form->addFieldset(
            'common_fieldset',
            array(
                'legend' => $this->__('Main Info'),
                'class' => 'fieldset-wide'
            )
        );

        $fieldset->addField('comment', 'text', array(
            'name' => 'comment',
            'label' => $this->__('Comment'),
            'title' => $this->__('Comment'),
            'required' => true,
        ));

        $fieldset->addField('policy_version', 'text', array(
            'name' => 'policy_version',
            'label' => $this->__('Policy Version'),
            'title' => $this->__('Policy Version'),
            'required' => true,
        ));

        $fieldset->addField('status', 'select', array(
            'name' => 'status',
            'label' => $this->__('Policy Status'),
            'title' => $this->__('Policy Status'),
            'required' => true,
            'values' => $model->getStatuses()
        ));

        $checkboxDefault = $storeId ?
            $this->getLayout()
                ->createBlock('core/template')
                ->setTemplate('amgdpr/default.phtml')
                ->setUseDefault($useDefault)->toHtml() : '';

        $fieldset->addField('content', 'editor', array(
            'name' => 'content',
            'label' => $this->__('Policy Content'),
            'title' => $this->__('Policy Content'),
            'required' => true,
            'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'wysiwyg' => true,
            'disabled' => ($useDefault && $storeId) ? true : false,
            'after_element_html' => $checkboxDefault,
        ));

        if (!$useDefault) {
            $model->setContent($content->getContent());
        }
        $form->addValues($model->getData());

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
