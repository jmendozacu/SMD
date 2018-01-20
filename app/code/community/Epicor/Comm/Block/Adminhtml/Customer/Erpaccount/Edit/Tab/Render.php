<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Render extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
      $value = $row->getData($this->getColumn()->getIndex());
      
       if($value==1){
           $val = "Yes";
       }
       else{
           $val = "No";
       }
        return $val;
    }

}
