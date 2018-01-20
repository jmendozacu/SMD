<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:43
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Tag_Category_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                'id'         => 'edit_form',
                'action'     => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'), 'store' => $this->getRequest()->getParam('store'))),
                'method'     => 'post',
                'enctype'    => 'multipart/form-data'
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return $this;
    }
}