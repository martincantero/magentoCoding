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

class Santex_Logger_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    private $_logger;

    protected function _getLogger()
    {
        if(!isset($this->_logger)) {
            $this->_logger = Mage::getModel('logger/logs');
        }
        return $this->_logger;
    }
    
    public function save($message)
    {
        $this->_getLogger()->saveLog($message);
    }

}
