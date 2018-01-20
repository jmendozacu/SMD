<?php

class Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Notes extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {

        $index = $this->getColumn()->getIndex();
        $comment = $row->getData($index);
        $notesLength = Mage::getStoreConfig('epicor_comm_returns/notes/line_notes_length');
        $maxLength = $notesLength? 'maxLength='.$notesLength : '';
        $notesRequired = Mage::getStoreConfig('epicor_comm_returns/notes/line_notes_required');
        if (!Mage::registry('review_display') && $row->isActionAllowed('Notes')) {
            $disabled = $row->getToBeDeleted() == 'Y' ? ' disabled="disabled"' : '';
            $html = '<textarea class="return_line_notes" '.$maxLength.' name="lines[' . $row->getUniqueId() . '][note_text]"' . $disabled . '>' . $this->escapeHtml($comment) . '</textarea>';
        } else {
            $html = '<textarea class="return_line_notes" '.$maxLength.' name="lines[' . $row->getUniqueId() . '][note_text]">' . $this->escapeHtml($comment) . '</textarea>';
        }
        if($notesLength){
            $html .= '<div id="truncated_message_line_notes">max '.$notesLength.' chars</div>';
        }
        return $html;
    }

}
