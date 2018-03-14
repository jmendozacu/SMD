<?php

/**
 * Renderer for TrackingUrl
 * 
 * @category   Epicor
 * @package    Epicor_comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Mapping_Renderer_Trackingurl extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        
        $trackingUrl = $row->getTrackingUrl();
        if ($trackingUrl) {
            $rowAttributes ="shipping_method_trakcing";
            $magentoCurrentUrl = Mage::helper("adminhtml")->getUrl("adminhtml/epicorcomm_mapping_shippingmethods/trackingurltest");
            $output .="<input type='hidden' id='popupshipurl' value='$magentoCurrentUrl'>";
            $output .="<input type='hidden' id='trackshipurl_".$row->getId()."' value='$trackingUrl'>";
            $output .= '<a href="#"  id="aaa" onclick="shippingmethodtrack.openpopup(\'' . $rowAttributes . '\',\'' . $row->getId() . '\'); return false;">Test Tracking Url</a>';
        } else {
            $output = '';
        }
        return $output;
    }

}
