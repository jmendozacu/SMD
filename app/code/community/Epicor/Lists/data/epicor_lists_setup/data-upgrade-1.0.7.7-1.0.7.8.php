<?php

/**
 * 
 * Upgrade - 1.0.7.7 to 1.0.7.8
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
                $gridArray[$key]['header'] = 'Contract';
            }
        }        
        $serializedArray = serialize($gridArray);
        $update_config = new Mage_Core_Model_Config();                   
        $update_config->saveConfig("customerconnect_enabled_messages/{$gridId}_request/grid_config", $serializedArray);
    }    
}
