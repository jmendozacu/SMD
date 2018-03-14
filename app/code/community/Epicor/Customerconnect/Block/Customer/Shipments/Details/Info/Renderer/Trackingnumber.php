<?php

class Epicor_Customerconnect_Block_Customer_Shipments_Details_Info_Renderer_Trackingnumber extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {

    public function render(Varien_Object $row) {
        $html ="";
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */         
        if ((!is_null($row->getMethodCode())) && ($row->getTrackingNumber())){
            $mappingMode = $helper->getMappingShippingCode($row->getMethodCode());
            $mappingCode = $mappingMode['method'];
            $mappingUrl  = $mappingMode['methodurl'];
        }    
        
        if (!is_null($row->getTrackingUrl())){
            $html .= '<a href="' . $row->getTrackingUrl() . '" target="_blank" >' . $row->getTrackingNumber() . '</a>';
        } else {
            $getGlobalUrl = $helper->getGlobalReturnUrl();
            if((!$getGlobalUrl) &&($row->getTrackingNumber()) && (!$mappingUrl)) {
                $html .= $row->getTrackingNumber();
            }
            if(($getGlobalUrl) &&($row->getTrackingNumber()) && (!$mappingUrl)) {
                $combineUrl = $helper->formatTrackingUrl($getGlobalUrl,$row->getTrackingNumber()); 
                $html .= '<a href="' . $combineUrl . '" target="_blank" >' . $row->getTrackingNumber() . '</a>';    
            }
            if(($row->getTrackingNumber()) && ($mappingUrl)) {
                $combineUrl = $helper->formatTrackingUrl($mappingUrl,$row->getTrackingNumber()); 
                $html .= '<a href="' . $combineUrl . '" target="_blank" >' . $row->getTrackingNumber() . '</a>';    
            }            
        }
        

        return $html;
    }
    
}