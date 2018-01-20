<?php

/**
 * Return line attachment delete tickbox
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Lines_Attachments_Renderer_Delete extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Common_Model_File */

        $line = Mage::registry('current_return_line');
        /* @var $line Epicor_Comm_Model_Customer_Return_Line */

        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */

        $link = $line->getAttachmentLink($row->getId());
        /* @var $link Epicor_Comm_Model_Customer_Return_Attachment */
        
        $checked = ($link->getToBeDeleted() == 'Y' 
            || $line->getToBeDeleted() == 'Y' ) ? ' checked="checked"' : '';

        $html = '<input type="checkbox" class="line_attachments_delete" name="lineattachments[existing][' . $line->getUniqueId() . '][' . $row->getUniqueId() . '][delete]" ' . $checked . '/>';

        $oldDetails = array(
            'return_id' => $return->getId(),
            'line_id' => $line->getId(),
            'attachment_id' => $row->getId(),
            'web_file_id' => $row->getId(),
            'erp_file_id' => $row->getErpId(),
            'url' => $row->getUrl(),
        );

        $html .= '<input type="hidden" name="lineattachments[existing][' . $line->getUniqueId() . '][' . $row->getUniqueId() . '][old_data]" value="' . base64_encode(serialize($oldDetails)) . '" /> ';

        return $html;
    }

}
