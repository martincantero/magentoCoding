<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Block_Adminhtml_Operatory extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct() {
        $this->_addButtonLabel = Mage::helper('oca')->__('Add New Operatory');
        parent::__construct();

        $this->_blockGroup = 'oca';
        $this->_controller = 'adminhtml_operatory';
        $this->_headerText = Mage::helper('oca')->__('Manage Operatories');
    }
}
