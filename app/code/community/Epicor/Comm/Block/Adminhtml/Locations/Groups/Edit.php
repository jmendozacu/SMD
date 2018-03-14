<?php
/**
 * @category    Epicor
 * @package     Epicor_Comm
 */

/**
 * Group edit block
 *
 * @category   Epicor
 * @package    Epicor
 */
class Epicor_Comm_Block_Adminhtml_Locations_Groups_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_locations_groups';
        $this->_blockGroup = 'epicor_comm';
        $this->_mode = 'edit';
        
        $this->_addButton('save_and_continue', array(
            'label' => Mage::helper('epicor_comm')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";

        parent::__construct();
        $this->_removeButton('reset');
    }


    public function getGroup()
    {
        return Mage::registry('group');
    }

    public function getHeaderText()
    {
        if ($this->getGroup()->getId()) {
            return Mage::helper('epicor_comm')->__('Group: %s',$this->getGroup()->getGroupName());
        }
        else {
            return Mage::helper('epicor_comm')->__('New Group');
        }
    }
}
