<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Adminhtml_Policy_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'amgdpr';
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_policy';

        parent::__construct();
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true, 'back' => null));
    }

    public function getHeaderText()
    {
        $version = Mage::registry('current_policy')->getPolicyVersion();
        if ($version) {
            return $this->escapeHtml($this->__('Privacy Policy v.') . $version);
        }
        else {
            return $this->__('Privacy Policy');
        }
    }
}
