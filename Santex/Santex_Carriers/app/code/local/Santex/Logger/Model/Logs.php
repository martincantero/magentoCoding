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

class Santex_Logger_Model_Logs extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('logger/logs');
    }

    public function saveLog($message, $process_name = 'logger', $log_anyway = false, $allow_url = false)
    {
        if (!$log_anyway) { 
            $_enable = Mage::app()->getStore()->getConfig($process_name . '/settings/enable');
        } else {
            $_enable = true;
        }
        if ($_enable) {
            $_log = Mage::getModel('logger/logs');
            if ($allow_url) {
                $_log->setLogDate(now())->setProcessName($process_name)->setMessage($message);
            } else {
                $_log->setLogDate(now())->setProcessName($process_name)->setMessage(filter_var($message, FILTER_SANITIZE_STRING));
            }
            $_log->save();
        }
    }
    
    public function cleanOld($process_name)
    {
        $_days = Mage::app()->getStore()->getConfig($process_name . '/settings/save_days_logs');
        if ($_days) {
            $_date = new Zend_Date(Mage::app()->getLocale()->date(now()), Zend_Date::ISO_8601);
            $_connection = Mage::getSingleton('core/resource')->getConnection('read');
            $_table = Mage::getSingleton('core/resource')->getTableName('logger/logs');
            $_connection->query("DELETE FROM {$_table} WHERE process_name = '{$process_name}' AND log_date <= '{$_date->subDay($_days)->toString('yyyy-MM-dd HH:mm:ss')}';");
        }
    }

}
