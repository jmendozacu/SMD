<?php

/**
 * Locations Manufacturers renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Widget_Grid_Column_Renderer_Customer_Erpaccount_Address extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render country grid column
     *
     * @param   Epicor_Comm_Model_Location_Product $row
     * 
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $html = '';
        
        $addressCode = $row->getData($this->getColumn()->getIndex());
        
        $addressCollection = Mage::getModel('epicor_comm/customer_erpaccount_address')->getCollection();
        $addressCollection->addFieldToFilter('erp_code', $addressCode);
        
        $address = $addressCollection->getFirstItem();
        
        $addressFields = array('name', 'address1', 'address2', 'address3', 'city', 'county', 'country', 'postcode');
        
        $glue = '';
        if ($address) {
            foreach ($addressFields as $field) {
                $fieldData = trim($address->getData($field));
                if ($fieldData && !empty($fieldData)) {
                    $html .= $glue . $fieldData;
                    $glue = ', ';
                }
            }
        }

        return $html;
    }

}
