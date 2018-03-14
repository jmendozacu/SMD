<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Adminhtml_Files_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => (int)$this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
