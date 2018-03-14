<?php
class Epicor_Customerconnect_Block_Customer_Account_Contacts_Renderer_Contactsync extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row){

        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $actions = $this->getColumn()->getActions();
        $source = $row->getSource();
        $out = '<select class="action-select" onchange="varienGridAction.execute(this);customerConnect.contactsEdit(this);">'
             . '<option value=""></option>';
        foreach ($actions as $action){
            if ( is_array($action) ) {
                $syncAction = Mage::helper('customerconnect')->__('Sync Contact');
                if((($action['caption'] == $syncAction && $source == $helper::SYNC_OPTION_ONLY_ECC) || $action['caption'] != $syncAction)){
                    $out .= $this->_toOptionHtml($action, $row);
                }
            }
        }
        $out .= '</select>';
        return $out;
    }
}
?>
