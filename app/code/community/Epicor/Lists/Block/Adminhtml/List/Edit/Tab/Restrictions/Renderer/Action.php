<?php

/**
 * List Restricted address renderer
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Restrictions_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{

    public function render(Varien_Object $row)
    {
        $rowId = $row->getId();
        $actions = $this->getColumn()->getActions();
        if (empty($actions) || !is_array($actions)) {
            return '&nbsp;';
        }
        $id = $this->getRequest()->getParam('list_id');
        $restrictionType = Mage::getSingleton('admin/session')->getRestrictionTypeValue();
        if ($this->getColumn()->getLinks() == true) {
            $html = '';
            foreach ($actions as $action) {
                if ($action['caption'] == 'Edit') {
                    $action['onclick'] = "openRestrictionForm($rowId,$id,'edit','" . $restrictionType . "');";
                }
                if ($action['caption'] == 'Delete') {
                    $action['onclick'] = "deleteRestrictedAddress($rowId,'" . $restrictionType . "')";
                }

                if (is_array($action)) {
                    if ($html != '') {
                        $html .= '<span class="action-divider">' . ($this->getColumn()->getDivider() ? : ' | ') . '</span>';
                    }
                    $html .= $this->_toLinkHtml($action, $row);
                }
            }
            return $html;
        } else {
            return parent::render($row);
        }
    }

}
