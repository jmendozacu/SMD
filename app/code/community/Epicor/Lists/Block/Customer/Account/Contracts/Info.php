<?php

class Epicor_Lists_Block_Customer_Account_Contracts_Info extends Mage_Core_Block_Template
{

    protected $_infoData;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('epicor/lists/contract/info.phtml');
        $this->setColumnCount(3);

        $details = Mage::registry('epicor_lists_contracts_details');
        if ($details) {

            $contract = $details->getContract();

            $helper = Mage::helper('epicor_lists');
            if (is_object($contract->getSalesRep())) {
                $salesRepName = (trim($contract->getSalesRep()->getName()) != '') ? $contract->getSalesRep()->getName() : $contract->getSalesRep()->getNumber();
            } else {
                $salesRepName = $contract->getSalesRep();
            }


            $this->_infoData[$this->__('Contract Code :')] = $contract->getContractCode();
            $this->_infoData[$this->__('Title :')] = $contract->getContractTitle();
            $this->_infoData[$this->__('Start Date :')] = $contract->getStartDate();
            $this->_infoData[$this->__('End Date :')] = date('jS M Y', strtotime($contract->getEndDate()));
            $this->_infoData[$this->__('Status :')] = $contract->getContractStatus()== 'A' ? 'Active' : 'Inactive';
            $this->_infoData[$this->__('Last Modified Date :')] = date('jS M Y', strtotime($contract->getLastModifiedDate()));$contract->getLastModifiedDate();
            $this->_infoData[$this->__('Sales Rep :')] = $salesRepName;
            $this->_infoData[$this->__('Contact Name :')] = $contract->getContactName();
            $this->_infoData[$this->__('PO Number :')] = $contract->getPurchaseOrderNumber();

        }
        
        $this->setTitle($this->__('Customer Contract Information'));
        $this->setColumnCount(1);
        $this->setOnRight(true);
    }

}
