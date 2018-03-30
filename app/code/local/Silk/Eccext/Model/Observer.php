<?php

class Silk_Eccext_Model_Observer
{
	public function filterAccountReturn($observer)
	{
		try {
			$event = $observer->getEvent();
			$message = $event->getMessage();
			$data = $message->getMessageArray();
			//Mage::log($data, null, 'json_data.log');
			$searches = $data['messages']['request']['body']['results']['searches'];
			$returnFilter = array(
				"criteria" => "returnsStatus",
				"condition" => "EQ",
				"value" => "Open"
			);
			$flag = true;
			if(isset($searches['search'])){
				foreach ($searches['search'] as $idx => $search) {
					if($search['criteria']=='returnsStatus' && $search['condition'] == 'EQ'){
						//Mage::log('have filter', null, 'json_data.log');
						$flag = false;
						break;
					}
				}
				if($flag){
					array_push($searches, $returnFilter);
					$data['messages']['request']['body']['results']['searches'] = $searches;
				}
			}
			else{
				//Mage::log('push in', null, 'json_data.log');
				$data['messages']['request']['body']['results']['searches']['search'] = $returnFilter;
			}
			$message->setMessageArray($data);
		}
		catch (Exception $e) {
			Mage::log($e->getMessage(), null, 'json_data.log');
		}
	}

}
