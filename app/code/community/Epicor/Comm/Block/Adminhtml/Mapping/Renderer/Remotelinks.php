<?php

/**
 * Renderer for Sites > Stores column, shows list of stores for the site
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Mapping_Renderer_Remotelinks extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $httpAuth = $row->getHttpAuthorization();
        if ($httpAuth) {
              $output = ' <img src="' . $this->getSkinUrl('images/success_msg_icon.gif') . '" alt="HTTP NOT Authorised" /> ';
//            if ($cert->isValidCertificate()) {
//                $output = ' <img src="' . $this->getSkinUrl('images/success_msg_icon.gif') . '" alt="HTTP Authorised" /> ';
//            } else {
//                $output = ' <img src="' . $this->getSkinUrl('images/warning_msg_icon.gif') . '" alt="HTTP NOT Authorised" /> ';
//            }
        } else {
            $output = ' <img src="' . $this->getSkinUrl('images/cancel_icon.gif') . '" alt="HTTP NOT Authorised" /> ';
        }
        return $output;
    }

}
