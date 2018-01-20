<?php
/**
 * Branchpickup page search page grid
 *
 * @category   Epicor
 * @package    Epicor_BranchPickup
 * @author     Epicor Websales Team
 */
class Epicor_BranchPickup_Block_Location_Edit extends Mage_Core_Block_Template
{
    
    protected $_validationLocationFields = array('street1', 'city', 'country_id', 'postcode', 'telephone');
    
    public function getActionOfForm()
    {
        return $this->getUrl('branchpickup/pickup/savelocation');
    }
    
    public function checkFieldEmpty()
    {
        /* @var $this Epicor_SalesRep_Block_Account_Dashboard_ErpSelector */
        $helpers        = Mage::helper('epicor_branchpickup');
        /* @var $helper Epicor_BranchPickup_Helper_Data */
        $selectedBranch = $helpers->getSelectedBranch();
        if ($selectedBranch) {
            $details = $helpers->getPickupAddress($selectedBranch);
            $errors  = array();
            foreach ($details as $key => $value) {
                if ((empty($details[$key])) && (in_array($key, $this->_validationLocationFields))) {
                    $errors[] = $key;
                }
                
            }
        }
        return $errors;
    }
    
    
    public function showEmptyFields()
    {
        $emptyFields = $this->checkFieldEmpty();
        $returnEmpty = array();
        if (in_array('street1', $emptyFields)) {
            $returnEmpty['address1'] = "hideaddress1";
        }
        if (in_array('city', $emptyFields)) {
            $returnEmpty['city'] = "hidecity";
        }
        if (in_array('postcode', $emptyFields)) {
            $returnEmpty['postcode'] = "hidepostcode";
        }
        if (in_array('telephone', $emptyFields)) {
            $returnEmpty['telephone_number'] = "hidetelephone_number";
        }
        if (in_array('country_id', $emptyFields)) {
            $returnEmpty['country_id'] = "hidecountry_id";
        }
        return $returnEmpty;
    }
    
}