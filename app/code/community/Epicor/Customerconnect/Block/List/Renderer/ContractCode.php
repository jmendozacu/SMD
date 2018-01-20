<?php

class Epicor_Customerconnect_Block_List_Renderer_ContractCode extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
     public function render(Varien_Object $row) {
        $rowData = $row->getData(); 
        $rowNumber =  array_key_exists('invoice_number', $rowData) ? $row->getInvoiceNumber() : $row->getOrderNumber();   // added for cuis or cuos screen
        $contracts = Mage::helper('epicor_common/xml')->varienToArray($row->getContracts());
        $contracts = $contracts ? $contracts : Mage::helper('epicor_common/xml')->varienToArray($row->getContractCode()); 
        if(is_array($contracts)){
            $contractList = '';
            foreach($contracts as $contract){
                if(is_array($contract)){                
                    foreach($contract as $key => $contractCode){
                        $contractList .= ($key > 0) ? '</br>' : ''; 
                        $contractList .= Mage::helper('epicor_comm')->retrieveContractTitle($contractCode);                
                    }
                    return '<span id= "contract_code_heading_'.$rowNumber.'" style = "display:block">multiple</span><span id = "contract_codes_'.$rowNumber.'" style = "display:none">'.$contractList.'</span>';
                }else{
                    return Mage::helper('epicor_comm')->retrieveContractTitle($contract);     
                }
            }
        }else{  
            return $contracts ? Mage::helper('epicor_comm')->retrieveContractTitle($contracts) : null;   
        }    
    }  
}