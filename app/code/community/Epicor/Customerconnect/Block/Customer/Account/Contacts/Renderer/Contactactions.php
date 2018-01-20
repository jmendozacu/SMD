<?php

class Epicor_Customerconnect_Block_Customer_Account_Contacts_Renderer_Contactactions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action {

    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('customerconnect');
        $actions = $this->getColumn()->getActions();
        $id = $row->getSource();
        switch ($id){
            case $helper::SYNC_OPTION_ONLY_ERP:
                unset($actions[2]);
                break;
        }
        //$actions = $this->getColumn()->getActions();
        return parent::render($row);
    }    

}
?>