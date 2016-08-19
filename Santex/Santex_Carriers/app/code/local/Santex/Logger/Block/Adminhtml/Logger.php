<?php
/**
 * Santex_Logger
 * 
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * Module based on Dc_Logger.
 *
 * @category   Santex
 * @package    Santex_Logger
 * @copyright  Copyright (c) 2014 Santex Group. (http://santexgroup.com/)
 */
 
class Santex_Logger_Block_Adminhtml_Logger extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    
    public function __construct()
    {
        $this->_controller = 'adminhtml_logger';
        $this->_blockGroup = 'logger';
        $this->_headerText = Mage::helper('logger')->__('Logger');
        $this->_addButtonLabel = Mage::helper('logger')->__('Add Item');
        parent::__construct();
        $this->_removeButton('add');
        $this->_addCleanLogsButton();
    }
    
    private function _addCleanLogsButton() {
        if (Mage::getStoreConfig('logger/settings/enable')) {
            $this->_addButton('logger', array(
                    'label'     => Mage::helper('logger')->__('Clean Logs'),
                    'onclick'   => 'setLocation(\'' . $this->getUrl('*/adminhtml_logger/cleanLogs') .'\')',
                    'class'     => '',
                ), -100);
        }
    }

}
