<?php

/**
 * RFQ line attachment delete tickbox
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Attachments_Renderer_Delete extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $line = Mage::registry('current_rfq_row');

        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        
        $html = '<input type="checkbox" class="line_attachments_delete" name="lineattachments[' . $key . '][' . $line->getUniqueId() . '][' . $row->getUniqueId() . '][delete]" />';
        $oldDetails = array(
            'description' => $row->getDescription(),
            'filename' => $row->getFilename(),
            'erp_file_id' => $row->getErpFileId(),
            'web_file_id' => $row->getWebFileId(),
            'attachment_number' => $row->getAttachmentNumber(),
            'url' => $row->getUrl(),
            'attachment_status' => $row->getAttachmentStatus()
        );

        $html .= '<input type="hidden" name="lineattachments[' . $key . '][' . $line->getUniqueId() . '][' . $row->getUniqueId() . '][old_data]" value="' . base64_encode(serialize($oldDetails)) . '" /> ';
        $html .= '<input type="hidden" name="lineattachments[' . $key . '][' . $line->getUniqueId() . '][' . $row->getUniqueId() . '][is_duplicate]" value="1" /> ';
        
        return $html;
    }

}
