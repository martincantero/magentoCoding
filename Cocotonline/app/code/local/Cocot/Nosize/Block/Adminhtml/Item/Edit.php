<?php

class Cocot_Nosize_Block_Adminhtml_Item_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    /**
     * Init class
     */
    public function __construct() {
        $this->_blockGroup = 'nosize';
        $this->_controller = 'adminhtml_item';

        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Save Item'));
        $this->_updateButton('delete', 'label', $this->__('Delete Item'));
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('cocot_nosize')->getNosizeId()) {
            return $this->__('Edit Item');
        } else {
            return $this->__('New Item');
        }
    }

}