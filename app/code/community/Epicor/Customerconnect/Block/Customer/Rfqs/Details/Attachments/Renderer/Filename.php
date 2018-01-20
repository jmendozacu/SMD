<?php

/**
 * Line comment display
 *
 * @author Gareth.James
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Attachments_Renderer_Filename extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        $helper = Mage::helper('epicor_common/file');
        /* @var $helper Epicor_Common_Helper_File */

        $index = $this->getColumn()->getIndex();
        $value = $row->getData($index);

        $url = $helper->getFileUrl($row->getWebFileId(), $row->getErpFileId(), $row->getFilename(), $row->getUrl());
        $html = $value . '<a href="' . $url . '" target="_blank" class="attachment_view">View</a>';

        if (Mage::registry('rfqs_editable') || Mage::registry('rfqs_editable_partial')) {
            $html .= ' | ' . $this->__('Update File') . ': <input type="file" name="attachments[' . $key . '][' . $row->getUniqueId() . '][' . $index . ']" value="' . $value . '" class="lines_' . $index . '"/>';
        }

        return $html;
    }

}
