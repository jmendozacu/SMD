<?php

class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Attachments_Renderer_Delete extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        
        $html = '<input type="checkbox" class="attachments_delete" name="attachments['.$key.'][' . $row->getUniqueId() . '][delete]" />';

        $oldDetails = array(
            'description' => $row->getDescription(),
            'filename' => $row->getFilename(),
            'erp_file_id' => $row->getErpFileId(),
            'web_file_id' => $row->getWebFileId(),
            'attachment_number' => $row->getAttachmentNumber(),
            'url' => $row->getUrl(),
            'attachment_status' => $row->getAttachmentStatus()
        );

        $html .= '<input type="hidden" name="attachments[' . $key . '][' . $row->getUniqueId() . '][old_data]" value="' . base64_encode(serialize($oldDetails)) . '" /> ';
        $html .= '<input type="hidden" name="attachments[' . $key . '][' . $row->getUniqueId() . '][is_duplicate]" value="1" /> ';

        return $html;
    }

}
