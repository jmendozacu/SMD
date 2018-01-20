<?php

/**
 * Return attachments editable file field renderer
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Lines_Attachments_Renderer_File extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Common_Model_File */

        $line = Mage::registry('current_return_line');
        /* @var $line Epicor_Comm_Model_Customer_Return_Line */

        $helper = Mage::helper('epicor_common/file');
        /* @var $helper Epicor_Common_Helper_File */

        $index = $this->getColumn()->getIndex();

        $url = $helper->getFileUrl($row->getId(), $row->getErpId(), $row->getFilename(), $row->getUrl());
        $html = $row->getFilename() . ' <a href="' . $url . '" target="_blank" class="attachment_view">View</a>';

        if (!Mage::registry('review_display') && $line->isActionAllowed('Attachments')) {
            $link = $line->getAttachmentLink($row->getId());
            /* @var $link Epicor_Comm_Model_Customer_Return_Attachment */

            $disabled = ($link->getToBeDeleted() == 'Y' || $line->getToBeDeleted() == 'Y' ) ? ' disabled="disabled"' : '';
            $html .= ' | ' . $this->__('Update File') . ': <input type="file" name="lineattachments[existing][' . $line->getUniqueId() . '][' . $row->getUniqueId() . '][' . $index . ']" class="line_attachments_' . $index . '"' . $disabled . '/>';
        }

        return $html;
    }

}
