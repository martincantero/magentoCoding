<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Model_Operatory extends Mage_Core_Model_Abstract
{
    public function _construct() {
        parent::_construct();
        $this->_init('oca/operatory');
    }
}
