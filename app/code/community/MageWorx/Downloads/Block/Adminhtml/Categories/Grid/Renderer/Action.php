<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Adminhtml_Categories_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $actions = array();
        if ($row->getId() > 1) {
            $actions[] = array(
                '@' => array(
                    'href' => $this->getUrl(
                        '*/*/edit',
                        array(
                            'id' => $row->getId(),
                            'store' => Mage::registry('store_id')
                        )
                    ),
                ),
                '#' => Mage::helper('mageworx_downloads')->__('Edit')
            );
            return $this->_actionsToHtml($actions);
        }
    }

    protected function _actionsToHtml(array $actions)
    {
        $html = array();
        $attributesObject = new Varien_Object();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode('<span class="separator">&nbsp;|&nbsp;</span>', $html);
    }
}
