<?php

class Epicor_Lists_Block_Contract_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $nowTime = time();
        $startDate = strtotime($row->getStartDate());
        $endDate = strtotime($row->getEndDate());  
        $productStatusCode = $row->getStatus();

        $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($row->getEntityId());
        if($parentIds || $row->getVisibility() == Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE){
            return 'Not available on this Store';
        }        
        

        if(in_array($row->getContractProductStatus(), array(0, NULL))){
            return 'Inactive';
        }            

        if(!$startDate && !$endDate){
            return 'Active';
        }
        if($startDate && $startDate > $nowTime){
            return 'Pending';
        }
        if($endDate && $endDate < $nowTime){
            return 'Expired';
        }

        return 'Active';
    }    
}