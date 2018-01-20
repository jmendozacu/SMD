<?php

/**
 * Description of File
 *
 * @author Paul.Ketelle
 */
class Epicor_Comm_Block_Customer_Returns_Attachment_Lines_Renderer_Delete extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Common_Model_File */

        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */

        $link = $return->getAttachmentLink($row->getId());
        /* @var $link Epicor_Comm_Model_Customer_Return_Attachment */

        $checked = ($link->getToBeDeleted() == 'Y') ? ' checked="checked"' : '';

        $html = '<input type="checkbox" class="attachment_delete" onclick="attachments.delete(this)" name="attachments[existing][' . $row->getUniqueId() . '][delete]"' . $checked . ' />';

        $oldDetails = array(
            'return_id' => $return->getId(),
            'line_id' => '',
            'attachment_id' => $row->getId(),
            'web_file_id' => $row->getId(),
            'erp_file_id' => $row->getErpId(),
            'url' => $row->getUrl(),
        );

        $html .= '<input type="hidden" name="attachments[existing][' . $row->getUniqueId() . '][old_data]" value="' . base64_encode(serialize($oldDetails)) . '" /> ';

        return $html;
    }

}
