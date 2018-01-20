<?php

/**
 * Return line attachments description
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Lines_Attachments_Renderer_Description extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Common_Model_File */

        $line = Mage::registry('current_return_line');
        /* @var $line Epicor_Comm_Model_Customer_Return_Line */

        $index = $this->getColumn()->getIndex();
        $value = $this->escapeHtml($row->getDescription());

        $link = $line->getAttachmentLink($row->getId());
        /* @var $link Epicor_Comm_Model_Customer_Return_Attachment */

        $disabled = ($link->getToBeDeleted() == 'Y' || $line->getToBeDeleted() == 'Y' ) ? ' disabled="disabled"' : '';

        if (!Mage::registry('review_display') && $line->isActionAllowed('Attachments')) {
            $html = '<input type="text" name="lineattachments[existing][' . $line->getUniqueId() . '][' . $row->getUniqueId() . '][' . $index . ']" value="' . $value . '" class="line_attachments_' . $index . '"' . $disabled . '/>';
        } else {
            $html = $value;
        }
        return $html;
    }

}
