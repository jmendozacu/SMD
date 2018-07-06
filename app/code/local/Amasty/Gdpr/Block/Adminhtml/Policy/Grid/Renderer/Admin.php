<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Adminhtml_Policy_Grid_Renderer_Admin
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $adminUrl = $this->getUrl(
            "adminhtml/permissions_user/edit/",
            array('user_id' => $row->getData('last_edited_by'))
        );

        $fullName = $row->getData('fullname');

        return sprintf('<a target="_blank" href="%s">%s</a>', $adminUrl, $fullName);
    }

    public function renderExport(Varien_Object $row)
    {
        return $row->getData('fullname');
    }
}
