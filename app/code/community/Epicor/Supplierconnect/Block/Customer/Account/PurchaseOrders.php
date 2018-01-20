<?php

class Epicor_Supplierconnect_Block_Customer_Account_PurchaseOrders extends Epicor_Supplierconnect_Block_Customer_Info
{

    protected $_linkTo = array();

    public function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('Purchase Orders'));
        if (Mage::registry('supplier_connect_account_details')) {
            $details = Mage::registry('supplier_connect_account_details');
            $purchaseOrders = $details->getPurchaseOrders();

            $locale = Mage::app()->getLocale()->getLocaleCode();
            $helper = Mage::helper('supplierconnect');

            $this->_linkTo = array(
                $this->__('Open :') => array('link' => Mage::getUrl('supplierconnect/orders/new')),
                $this->__('PO Line / Release Changes :') => array('link' => Mage::getUrl('supplierconnect/orders/changes')),
            );

            foreach ($this->_linkTo as $key => $value) {
                $this->_linkTo[$key]['link'] = $value['link'];
                if (!empty($value['filter'])) {
                    $this->_linkTo[$key]['link'] .= urlencode($helper->urlEncode($value['filter'])) . '/';
                }
                $this->_linkTo[$key]['active'] = true;
            }

            $this->_infoData = array(
                $this->__('Open :') => $purchaseOrders->getOpen(),
                $this->__('PO Line / Release Changes :') => $purchaseOrders->getChanges()
            );
        }
        $this->setColumnCount(1);
        $this->setOnRight(true);
    }

}
