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

class Santex_Logger_Model_Cron
{
    
    private $_process_name = 'logger';
	private $_logger;

    public function runCleanLogs() {
        $this->_getLogger()->saveLog('Logger has started to clean logs.', $this->_process_name, true);
        $this->_execute(); 
        $this->_getLogger()->saveLog('Logger has finished to clean logs.', $this->_process_name, true);
    }
    
    private function _execute() {
        $_sync_enable = Mage::getStoreConfig($this->_process_name . '/settings/enable');
        if ($_sync_enable) {
            $this->_getLogger()->cleanOld($this->_process_name);
        } else {
            $this->_getLogger()->saveLog('Logger can not clean logs because process is fully disabled.', $this->_process_name);
        }
        $this->sendEmail();
        return true;
    }
    
    protected function _getLogger() {
    	if(!isset($this->_logger)) {
    		$this->_logger = Mage::getModel('logger/logs');
    	}
    	return $this->_logger;
    }

    public function sendEmail() {
        $_use_email = Mage::getStoreConfig($this->_process_name . '/email/enable');
        if ($_use_email) {
            $_translate = Mage::getSingleton('core/translate');
            $_translate->setTranslateInline(false);
            try {
                $_email = Mage::getModel('core/email_template');
                $_email->setDesignConfig(array('area' => 'frontend'))
                    ->sendTransactional(
                        Mage::getStoreConfig($this->_process_name . '/email/template'),
                        Mage::getStoreConfig($this->_process_name . '/email/identity'),
                        Mage::getStoreConfig($this->_process_name . '/email/recipient'),
                        null,
                        null
                    );
                if (!$_email->getSentSuccess()) {
                    throw new Exception();
                }
                $_translate->setTranslateInline(true);
                $this->_getLogger()->saveLog('Log email sent.', $this->_process_name, true);
                return true;
            } catch (Exception $e) {
                Mage::log($e->getMessage());
                $this->_getLogger()->saveLog('There was an error trying to send the log email.', $this->_process_name, true);
                return false;
            }

        }
    }

}