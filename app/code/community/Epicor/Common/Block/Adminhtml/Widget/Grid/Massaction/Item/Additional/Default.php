<?php

class Epicor_Common_Block_Adminhtml_Widget_Grid_Massaction_Item_Additional_Default extends Mage_Adminhtml_Block_Widget_Grid_Massaction_Item_Additional_Default
{

    public function createFromConfiguration(array $configuration)
    {
        
        $form = new Varien_Data_Form();

        foreach ($configuration as $itemId => $item) {
            
            if (isset($item['renderer'])) {
                $form->addType($item['renderer']['type'], $item['renderer']['class']);
            }
            
            $item['class'] = isset($item['class']) ? $item['class'] . ' absolute-advice' : 'absolute-advice';
            $form->addField($itemId, $item['type'], $item);
        }
        $this->setForm($form);
        return $this;
    }

}
