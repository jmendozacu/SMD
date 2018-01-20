<?php

/**
 * Location display
 *
 * @author Gareth.James
 */
class Epicor_Customerconnect_Block_List_Renderer_Location extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        /* @var $locHelper Epicor_Comm_Helper_Locations */
        $location = $row->getLocationCode();
        if ($row->getLocationCode()) {
            $locHelper = Mage::helper('epicor_comm/locations');
            $location = $locHelper->getLocationName($row->getLocationCode());
            if (!$location) {
                $location = $row->getLocationCode();
            }
        }
        return $location;
    }

}
