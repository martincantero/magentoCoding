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

class Santex_Logger_Model_Process extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('logger/process');
    }
    
    public function getOptionArray()
    {
        $collection = Mage::getSingleton('logger/process')->getCollection();
        $options = array();
        foreach ($collection as $process) {
            $options[$process->getCode()] = $process->getLabel();
        }
        return $options;
    }

}
