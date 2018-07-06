<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Adminhtml_Policy_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('policy_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Policy Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main', array(
            'label' => $this->__('Main Info'),
            'title' => $this->__('Main Info'),
            'content' => $this->getLayout()->createBlock('amgdpr/adminhtml_policy_edit_tab_main')->toHtml(),
            'active' => true
        ));

        return parent::_beforeToHtml();
    }
}
