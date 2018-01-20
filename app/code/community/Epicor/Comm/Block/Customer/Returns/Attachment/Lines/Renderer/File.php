<?php

/**
 * Description of File
 *
 * @author Paul.Ketelle
 */
class Epicor_Comm_Block_Customer_Returns_Attachment_Lines_Renderer_File extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Common_Model_File */

        $helper = Mage::helper('epicor_common/file');
        /* @var $helper Epicor_Common_Helper_File */

        $index = $this->getColumn()->getIndex();

        $url = $helper->getFileUrl($row->getId(), $row->getErpId(), $row->getFilename(), $row->getUrl());
        $html = $row->getFilename() . '<a href="' . $url . '" target="_blank" class="attachment_view">View</a>';

        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */
        
        $link = $return->getAttachmentLink($row->getId());
        /* @var $link Epicor_Comm_Model_Customer_Return_Attachment */

        $disabled = ($link && $link->getToBeDeleted() == 'Y') ? ' disabled="disabled"' : '';

        $allowed = ($return) ? $return->isActionAllowed('Attachments') : true;
        
        if (!Mage::registry('review_display') && $allowed) {
            $html .= ' | ' . $this->__('Update File') . ': <input type="file" name="attachments[existing][' . $row->getUniqueId() . '][' . $index . ']" class="attachments_' . $index . '" ' . $disabled . '/>';
        }

        return $html;
    }

}
