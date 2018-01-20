<?php

/**
 * Expand column for PO lines
 *
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Orders_Details_Lines_Renderer_Expand extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $html = '';

        $releasesMsg = $row->getReleases();

        if ($releasesMsg) {
            $releases = $releasesMsg->getRelease();
            if (count($releases) > 0) {
                $html = '<span class="plus-minus" id="releases-' . $row->getId() . '">+</span>';
            }
        }

        return $html;
    }

}
