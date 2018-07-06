<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Adminhtml_DeleteRequest_DenyTemplate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'template_id';
        $this->_blockGroup = 'amgdpr';
        $this->_controller = 'adminhtml_deleteRequest_denyTemplate';

        parent::__construct();

        $this->_removeButton('reset');
        $this->_removeButton('save');

        $this->_addButton(
            'send',
            array (
                'label' => $this->__('Send'),
                'onclick' => 'editForm.submit();',
                'class' => 'save',
            ),
            1
        );
    }

    public function getHeaderText()
    {
        return $this->__('Send Email');
    }
}