<?php

/**
 * Return line attachments column renderer
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Attachments extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Comm_Model_Customer_Return_Line */
        if (!Mage::registry('review_display')) {

            $html = '</td>'
                . '</tr>'
                . '<tr class="lines_row attachment" id="row_return_line_attachments_' . $row->getUniqueId() . '" style="display:none">'
                . '<td colspan="10" class="attachments-row">';

            if (Mage::registry('current_return_line')) {
                Mage::unregister('current_return_line');
            }

            Mage::register('current_return_line', $row);

            $block = $this->getLayout()->createBlock('epicor_comm/customer_returns_lines_attachments');
            /* @var $block Epicor_Comm_Block_Customer_Returns_Lines_Attachments */

            $html .= $block->toHtml();
        } else {
            $attachments = $row->getAttachments();
            if (!empty($attachments)) {
                $html = '';
                $deleted = '';
                $helper = Mage::helper('epicor_common/file');
        /* @var $helper Epicor_Common_Helper_File */
                foreach ($attachments as $attachment) {
                    /* @var $attachment Epicor_Common_Model_File */

                    $link = $row->getAttachmentLink($attachment->getId());
                    /* @var $link Epicor_Comm_Model_Customer_Return_Attachment */
                    if ((!$link || $link->getToBeDeleted() != 'Y') && $row->getToBeDeleted() != 'Y') {
                        $url = $helper->getFileUrl($attachment->getId(), $attachment->getErpId(), $attachment->getFilename(), $attachment->getUrl());
                        $html .= '<p>' . $attachment->getFilename() . ' <a href="' . $url . '" target="_blank">View</a></p>';
                    } else {
                        $deleted .= '<p>' . $attachment->getFilename() . '</p>';
                    }
                }

                if ($deleted != '') {
                    $html .= '<p>' . $this->__('To Be Deleted') . ':</p>';
                    $html .= $deleted;
                }
            } else {
                $html = $this->__('No Attachments');
            }
        }

        return $html;
    }

}
