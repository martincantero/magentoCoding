<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Model_System_Config_Source_Timeframe
{
    public function toOptionArray() {
        return array(
            array(
                'value' => '1',
                'label' => Mage::helper('oca')->__('08:00 to 17:00')
            ),
            array(
                'value' => '2',
                'label' => Mage::helper('oca')->__('08:00 to 12:00')
            ),
            array(
                'value' => '3',
                'label' => Mage::helper('oca')->__('14:00 to 17:00')
            ),
        );
    }
}
