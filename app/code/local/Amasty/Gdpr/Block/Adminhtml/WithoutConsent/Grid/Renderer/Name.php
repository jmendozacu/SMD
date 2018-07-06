<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Adminhtml_WithoutConsent_Grid_Renderer_Name
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $customerUrl = $this->getUrl(
            "adminhtml/customer/edit/",
            array('id' => $row->getData('entity_id'))
        );

        return sprintf('<a target="_blank" href="%s">%s</a>', $customerUrl, $row->getData('name'));
    }
}
