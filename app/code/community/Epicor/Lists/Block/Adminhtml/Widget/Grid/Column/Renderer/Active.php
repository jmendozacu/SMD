<?php

/**
 * Active column renderer
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_Widget_Grid_Column_Renderer_Active extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render active grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $active = $row->getData($this->getColumn()->getIndex());

        if ($active) {
            $startDate = $row->getData($this->getColumn()->getStartDate());
            $endDate = $row->getData($this->getColumn()->getEndDate());

            $dateModel = Mage::getModel('core/date');
            $currentTimeStamp = $dateModel->timestamp(time());
            $startTimeStamp = $dateModel->timestamp(strtotime($startDate));
            $endTimeStamp = $dateModel->timestamp(strtotime($endDate));

            if ($endDate && $endTimeStamp < $currentTimeStamp) {
                $status = $this->__('Ended');
            } else if ($startDate && $startTimeStamp > $currentTimeStamp) {
                $status = $this->__('Pending');
            } else {
                $status = $this->__('Active');
            }
        } else {
            $status = $this->__('Disabled');
        }

        return $status;
    }

}
