<?php

/**
 * 
 * Upgrade - 1.0.7.6 to 1.0.7.7
 * 
 * Adding contract code to cuos, cuis and crqs grids 
 */
$gridsToUpdate = array('CUOS', 'CUIS', 'CRQS');
foreach($gridsToUpdate as $gridId){

    $currentGridValue = Mage::getStoreConfig("customerconnect_enabled_messages/{$gridId}_request/grid_config"); 
    if($currentGridValue){
        $gridArray = unserialize($currentGridValue);
        foreach($gridArray as $key=> $element){
            if($element['index'] == 'contracts_contract_code'){                
                unset($gridArray[$key]); //remove possible incorrect contract code definition
            }
        }
        $contractCodeArray = array( "header" => "Contract Code", "type" => "text", "options" => "", "index" => 'contracts_contract_code'
                                    ,"filter_by" => "erp", "condition" => "EQ", "sort_type" => "text", "renderer" => "Epicor_Customerconnect_Block_List_Renderer_ContractCode");

        $id = "_".time()."_".intval(microtime()*1000);
        $gridArray[$id] = $contractCodeArray;
        $serializedArray = serialize($gridArray);
        $update_config = new Mage_Core_Model_Config();                   
        $update_config->saveConfig("customerconnect_enabled_messages/{$gridId}_request/grid_config", $serializedArray);
    }    
}
