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

$installer = $this;

$installer->startSetup();

$installer->run("

CREATE TABLE {$this->getTable('logger/logs')} (
  log_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  log_date DATETIME NULL,
  process_name VARCHAR(50),
  message TEXT,
  PRIMARY KEY (log_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('logger/process')} (
  process_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  label VARCHAR(50),
  code VARCHAR(50),
  PRIMARY KEY (process_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 