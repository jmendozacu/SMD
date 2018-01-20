<?php

/**
 * Front-end Faqs Vote block
 * 
 * @category   Epicor
 * @package    Faq
 * @author     Epicor Websales Team
 */
class Epicor_Faqs_Block_Vote extends Mage_Core_Block_Template {
	
	public function getFaqItem($faqId){
		$model_faq = Mage::getModel('epicor_faqs/faqs');
		$model_faq->load($faqId)->getResource();
		return $model_faq;
	}
	
	public function getMessageVote($faqId){
        $collection =  Mage::getModel('epicor_faqs/vote')->getCollection();
		$collection->getSelect()
				->columns(array(
					'voted_yes' =>'SUM(IF(value > 0, 1, 0))',
					'voted' => 'COUNT(*)'
				))
				->where("faqs_id = '{$faqId}'")
				->group('faqs_id');
				
		$voted = $collection->getSize() > 0 ? $collection->getFirstItem()->getVoted() : 0;
		$voted_yes = $collection->getSize() > 0 ? $collection->getFirstItem()->getVotedYes() : 0;
		return sprintf($this->__('%s of %s voted this as helpful'), $voted_yes, $voted);
	}
}