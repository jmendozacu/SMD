<?php

/**
 * Front-end Faqs List block
 * 
 * @category   Epicor
 * @package    Faq
 * @author     Epicor Websales Team
 * 
 * @property Epicor_Faqs_Model_Resource_Faqs_Collection $_faqsCollection
 * 
 */
class Epicor_Faqs_Block_List extends Mage_Core_Block_Template
{

    /**
     * Container for the fetched FAQs
     *
     * @var Epicor_Faqs_Model_Resource_Faqs_Collection
     */
    protected $_faqsCollection = null;

    /**
     * Retrieve faqs collection
     *
     * @return Epicor_Faqs_Model_Resource_Faqs_Collection
     */
    protected function _getCollection()
    {
        return Mage::getResourceModel('epicor_faqs/faqs_collection');
    }

    /**
     * Retrieve prepared faqs collection, filtered by stores and sorted by weight
     *
     * @return Epicor_Faqs_Model_Resource_Faqs_Collection
     */
    public function getCollection()
    {
        //We fetch only the F.A.Q. active for the current store.
        $currentStore = Mage::app()->getStore()->getId();
        if (is_null($this->_faqsCollection)) {
            $this->_faqsCollection = $this->_getCollection()
                    ->addFieldToFilter('stores', array('finset' => $currentStore));
            if (Mage::helper('epicor_faqs')->getSortParameter() == 'usefulness') {
                $this->_faqsCollection->addExpressionFieldToSelect('usefulness', '(useful-useless)', array('useful' => 'useful', 'useless' => 'useless'))
                        ->addOrder('usefulness', 'DSC');
            } else {
                $this->_faqsCollection->addOrder('weight', 'ASC');
            }
        }

        return $this->_faqsCollection;
    }

    public function isIndexedView()
    {
        return Mage::helper('epicor_faqs')->getPresentation() == 'paragraphs';
    }

    public function isCustomerRegistered()
    {
        $customerSession = Mage::getSingleton('customer/session');
        return $customerSession->isLoggedIn();
    }

}
