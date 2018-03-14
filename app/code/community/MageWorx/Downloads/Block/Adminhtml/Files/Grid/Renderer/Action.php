<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Adminhtml_Files_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('mageworx_downloads');
        $actions = array();
        $actions[] = array(
            '@' => array(
                'href' => $this->getUrl(
                    '*/*/edit', array(
                        'id' => $row->getId(),
                        'store' => Mage::registry('store_id')
                    )
                ),
            ),
            '#' => $helper->__('Edit')
        );
        $actions[] = array(
            '@' => array(
                'href' => $this->getUrl(
                    '*/*/download', array(
                        'id' => $row->getId(),
                        'store' => Mage::registry('store_id')
                    )
                ),
            ),
            '#' => $helper->__('Download')
        );
        $actions[] = array(
            '@' => array(
                'href' => '#',
                'onclick' => "alert('{$helper->getDownloadLink($row)}'); return false;"
            ),
            '#' => $helper->__('Get Link')
        );

        return $this->_actionsToHtml($actions);
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