<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Block_Adminhtml_Operatory_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct() {
        parent::__construct();

        $this->_blockGroup = 'oca';
        $this->_controller = 'adminhtml_operatory';
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_oca_operatory')->getId()) {
            return Mage::helper('oca')->__("Edit OCA Operatory '%s'", $this->escapeHtml(Mage::registry('current_oca_operatory')->getName()));
        }
        else {
            return Mage::helper('oca')->__('New OCA Operatory');
        }
    }
}
