<?php

/**
 * Description of File
 *
 * @author Paul.Ketelle
 */
class Epicor_Comm_Block_Customer_Returns_Attachment_Lines_Renderer_Description extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Common_Model_File */

        $index = $this->getColumn()->getIndex();
        $value = $this->escapeHtml($row->getDescription());

        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */

        $link = $return->getAttachmentLink($row->getId());
        /* @var $link Epicor_Comm_Model_Customer_Return_Attachment */

        $disabled = ($link && $link->getToBeDeleted() == 'Y') ? ' disabled="disabled"' : '';

        $allowed = ($return) ? $return->isActionAllowed('Attachments') : true;
        
        if (!Mage::registry('review_display') && $allowed) {
            $html = '<input type="text" name="attachments[existing][' . $row->getUniqueId() . '][' . $index . ']" value="' . $value . '" class="attachments_' . $index . '"' . $disabled . '/>';
        } else {

            if ($link && $link->getToBeDeleted() == 'Y') {
                $html = $this->__('To Be Deleted') . ' : ' . $value;
            } else {
                $html = $value;
            }
        }

        return $html;
    }

}
