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

//Logger
$insert = array('code' => 'logger', 'label' => 'Logger');
Mage::getModel('logger/process')->setData($insert)->save();
