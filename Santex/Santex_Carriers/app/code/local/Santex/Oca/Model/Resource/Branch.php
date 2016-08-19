<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Model_Resource_Branch extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {    
        $this->_init('oca/branch', 'branch_id');
    }
    
}
