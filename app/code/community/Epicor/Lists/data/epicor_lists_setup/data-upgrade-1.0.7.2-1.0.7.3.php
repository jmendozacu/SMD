<?php

/**
 * 
 * Upgrade - 1.0.7.1 to 1.0.7.2
 * 
 * Adding contract code to cuos, cuis and crqs grids 
 */
$gridsToUpdate = array('CUOS', 'CUIS', 'CRQS');
foreach($gridsToUpdate as $gridId){

    $currentGridValue = Mage::getStoreConfig("customerconnect_enabled_messages/{$gridId}_request/grid_config"); 
    if($currentGridValue){
        
        $gridArray = unserialize($currentGridValue);
        $lastEntry = end($gridArray);
        $configArray = array();
        if($lastEntry['renderer'] != 'Epicor_Customerconnect_Block_List_Renderer_ContractCode' && $lastEntry['index'] != "contracts_contract_code"){

            $contractCodeArray = array( "header" => "Contract Code", "type" => "text", "options" => "", "index" => 'contracts_contract_code'
                                        ,"filter_by" => "erp", "condition" => "EQ", "sort_type" => "text", "renderer" => "Epicor_Customerconnect_Block_List_Renderer_ContractCode");

            $gridArray['_1473783939607_607'] = $contractCodeArray;
            $serializedArray = serialize($gridArray);
            $update_config = new Mage_Core_Model_Config();                   
            $update_config->saveConfig("customerconnect_enabled_messages/{$gridId}_request/grid_config", $serializedArray);
        }
    }    
}
