<?php

/**
 * Generic list functionality
 * 
 * Used by modules to create grids from message data
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 * 
 * @method setBoxed(boolean)
 * @method getBoxed
 * @method setBoxClass(string)
 * @method getBoxClass
 */
class Epicor_Common_Block_Generic_List extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_setupGrid();
        parent::__construct();
        $this->_postSetup();
        if($this->getBoxed()) {
            $this->setTemplate('widget/grid/container-boxed.phtml');
        }
        
    }

    /**
     * Do any pre-construct stuff here
     */
    protected function _setupGrid() {
        $this->_controller = '';
        $this->_blockGroup = '';
        $this->_headerText = Mage::helper('epicor_common')->__('Generic list');
    }

    /**
     * Do any post construct stuff here
     */
    protected function _postSetup() {
        $this->removeButton('add');
    }
    /*
     * This replaces the adminhtml/block/widget/grid/container.php which sets setSaveParametersInSession to true
     */
    protected function _prepareLayout()
    {
        // this is needed for frontend grid use to stop search options being retained for future users. the omission of calling the parent is intentional
        // as all the processing required when calling parent:: should be included
        $this->setChild( 'grid',
            $this->getLayout()->createBlock( $this->_blockGroup.'/' . $this->_controller . '_grid',
            $this->_controller . '.grid')->setSaveParametersInSession(false) );
        
        foreach ($this->_buttons as $level => $buttons) {
            foreach ($buttons as $id => $data) {
                $childId = $this->_prepareButtonBlockId($id);
                $this->_addButtonChildBlock($childId);
            }
        }
        return $this;
    }

}
