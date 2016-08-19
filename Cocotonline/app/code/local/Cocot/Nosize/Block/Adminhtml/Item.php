<?php

class Cocot_Nosize_Block_Adminhtml_Item extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        // The blockGroup must match the first half of how we call the block, and controller matches the second half
        // ie. foo_bar/adminhtml_baz
        $this->_blockGroup = 'nosize';
        $this->_controller = 'adminhtml_item';
        $this->_headerText = $this->__('Nosize Items');

        parent::__construct();
    }

}