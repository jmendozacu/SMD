<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Adminhtml_Consent_Grid_Renderer_Name
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $customerUrl = $this->getUrl(
            "adminhtml/customer/edit/",
            array('id' => $row->getData('customer_id'))
        );

        $fullName = $row->getData('prefix') . ' '
            . $row->getData('firstname') . ' '
            . $row->getData('middlename') . ' '
            . $row->getData('lastname') . ' '
            . $row->getData('suffix');

        return sprintf('<a target="_blank" href="%s">%s</a>', $customerUrl, $fullName);
    }
}
