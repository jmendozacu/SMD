<?php

/**
 * Renderer for Sites > Stores column, shows list of stores for the site
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Block_Adminhtml_Sites_Column_Renderer_Ssl extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $cert_id = $row->getData($this->getColumn()->getCertId());
        if ($cert_id) {
            $cert = Mage::getSingleton('hostingmanager/certificate')->load($cert_id);
            /* @var $cert Epicor_HostingManager_Model_Certificate */
            if ($cert->isValidCertificate()) {
                $output = ' <img src="' . $this->getSkinUrl('images/success_msg_icon.gif') . '" alt="SSL Certificate Ready" /> ';
            } else {
                $output = ' <img src="' . $this->getSkinUrl('images/warning_msg_icon.gif') . '" alt="SSL Certificate Not Ready" /> ';
            }
            if ($this->getColumn()->getShowName())
                $output .= $cert->getName();
        } else {
            $output = ' <img src="' . $this->getSkinUrl('images/cancel_icon.gif') . '" alt="No SSL Certificate" /> ';
        }
        return $output;
    }

}
