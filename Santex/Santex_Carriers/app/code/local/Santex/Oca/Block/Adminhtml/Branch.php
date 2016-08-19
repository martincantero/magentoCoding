<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Block_Adminhtml_Branch extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct() {
        $this->_addButtonLabel = Mage::helper('oca')->__('Add New Branch');
        $this->_addButton('add_new', array(
            'label'   => Mage::helper('oca')->__('Update Branches via WS'),
            'onclick' => "setLocation('{$this->getUrl('*/*/load')}')",
        ));
        parent::__construct();

        $this->_blockGroup = 'oca';
        $this->_controller = 'adminhtml_branch';
        $this->_headerText = Mage::helper('oca')->__('Manage Branches');
    }
}
