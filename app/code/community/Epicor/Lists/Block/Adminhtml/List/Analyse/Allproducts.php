<?php

class Epicor_Lists_Block_Adminhtml_List_Analyse_Allproducts extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_list_analyse_allproducts';
        $this->_blockGroup = 'epicor_lists';
        $this->_headerText = $this->__('Products');

        $this->addButton(20, array('label' => 'Cancel', 'onclick' => "listsAnalyse.closepopup()"), 1);

        parent::__construct();
        $this->removeButton('add');
    }

}
