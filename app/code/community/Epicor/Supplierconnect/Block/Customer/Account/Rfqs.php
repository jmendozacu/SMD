<?php

class Epicor_Supplierconnect_Block_Customer_Account_Rfqs extends Epicor_Supplierconnect_Block_Customer_Info
{

    protected $_linkTo = array();

    public function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('RFQs'));
        if (Mage::registry('supplier_connect_account_details')) {
            $helper = Mage::helper('supplierconnect');

            $locale = Mage::app()->getLocale()->getLocaleCode();
            $url = Mage::helper("adminhtml")->getUrl('supplierconnect/rfq/index') . 'filter/';

            $dayToday = date('l');

            $today = date('m/d/Y');
            
            if ($dayToday == 'Monday') {
                $lastMonday = date('m/d/Y');
            } else {
                $lastMonday = date('m/d/Y', strtotime('last monday'));
            }
            
            $tomorrow = date('m/d/Y', strtotime("tomorrow"));
            $yesterday = date('m/d/Y', strtotime("yesterday"));
            
            if ($dayToday == 'Sunday') {
                $nextSunday = date('m/d/Y');
            } else {
                $nextSunday = date('m/d/Y', strtotime('next sunday'));
            }

            $this->_linkTo = array(
                $this->__('Today :') => array('link' => $url, 'filter' => "status=O&response=Waiting&due_date[locale]={$locale}&due_date[from]={$today}&due_date[to]={$today}"),
                $this->__('This Week :') => array('link' => $url, 'filter' => "status=O&response=Waiting&due_date[locale]={$locale}&due_date[from]={$lastMonday}&due_date[to]={$nextSunday}"),
                $this->__('Future :') => array('link' => $url, 'filter' => "status=O&response=Waiting&due_date[locale]={$locale}&due_date[from]={$tomorrow}"),
                $this->__('Open :') => array('link' => $url, 'filter' => "status=O&due_date[locale]={$locale}"),
                $this->__('Overdue :') => array('link' => $url, 'filter' => "status=O&response=Waiting&due_date[locale]={$locale}&due_date[to]={$yesterday}"),
            );

            /* @var $helper Epicor_Supplierconnect_Helper_Data */
            foreach ($this->_linkTo as $key => $value) {
                $this->_linkTo[$key]['link'] = $value['link'] . urlencode($helper->urlEncode($value['filter'])) . '/';
                $this->_linkTo[$key]['active'] = true;
            }
            $details = Mage::registry('supplier_connect_account_details');
            $rfq = $details->getRfqs();

            $this->_infoData = array(
                $this->__('Today :') => $rfq->getDueToday(),
                $this->__('This Week :') => $rfq->getDueWeek(),
                $this->__('Future :') => $rfq->getDueFuture(),
                $this->__('Open :') => $rfq->getOpen(),
                $this->__('Overdue :') => $rfq->getOverDue()
            );
        }
        $this->setColumnCount(2);
        $this->setOnRight(true);
    }

}
