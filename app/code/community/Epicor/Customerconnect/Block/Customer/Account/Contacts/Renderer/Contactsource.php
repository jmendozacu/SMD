<?php
class Epicor_Customerconnect_Block_Customer_Account_Contacts_Renderer_Contactsource extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {

    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $id = $row->getSource();
        switch ($id){
            case $helper::SYNC_OPTION_ONLY_ERP:
                $value = Mage::helper('epicor_comm')->__("ERP only");
                break;
            case $helper::SYNC_OPTION_ONLY_ECC:
                $value = Mage::helper('epicor_comm')->__("Web only");
                break;
            case $helper::SYNC_OPTION_ECC_ERP:
                $value = Mage::helper('epicor_comm')->__("Both");
                break;
        }
        return $value;
    }    

}
?>