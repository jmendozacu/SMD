<?php

/**
 * List edit form container
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {

        $list = $this->getList();

        $url = $list->isObjectNew() ? '*/*/create' : '*/*/save';

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl($url, array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Gets the current List
     *
     * @return Epicor_Lists_Model_List
     */
    public function getList()
    {
        if (!$this->_list) {
            $this->_list = Mage::registry('list');
        }
        return $this->_list;
    }

}
